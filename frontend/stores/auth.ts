import { defineStore } from "pinia";

interface User {
  id: string;
  name: string;
  email: string;
  role: "admin" | "author" | "reader";
  avatar: string | null;
  bio?: string;
}

interface AuthState {
  user: User | null;
  token: string | null;
}

export const useAuthStore = defineStore("auth", {
  state: (): AuthState => ({
    user: null,
    token: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === "admin",
    isAuthor: (state) => ["admin", "author"].includes(state.user?.role ?? ""),
    displayName: (state) => state.user?.name ?? "Visiteur",
  },

  actions: {
    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Résoudre l'URL de base correctement que ce soit en SSR ou côté client.
     * En SSR le chemin relatif `/proxy` n'a pas de sens → on utilise l'URL complète.
     */
    _buildUrl(path: string): string {
      const config = useRuntimeConfig();
      const base = config.public.apiBase as string;

      // Si la base commence par http → URL absolue, l'utiliser directement
      if (base.startsWith("http")) {
        return `${base}${path}`;
      }

      // Base relative (ex : /proxy) → construire l'URL absolue Nuxt pour SSR
      if (import.meta.server) {
        return `http://localhost:3000${base}${path}`;
      }

      // Côté client → chemin relatif suffit (même origine)
      return `${base}${path}`;
    },

    /** Headers communs */
    _headers(extra: Record<string, string> = {}): Record<string, string> {
      return {
        "Content-Type": "application/json",
        Accept: "application/json",
        ...(this.token ? { Authorization: `Bearer ${this.token}` } : {}),
        ...extra,
      };
    },

    // ── Actions publiques ────────────────────────────────────────────────────

    async login(email: string, password: string): Promise<void> {
      const url = this._buildUrl("/auth/login");

      const response = await $fetch<{ token: string; user: User }>(url, {
        method: "POST",
        headers: this._headers(),
        body: JSON.stringify({ email, password }),
      });

      this.token = response.token;
      this.user = response.user;

      const tokenCookie = useCookie("auth_token", {
        maxAge: 60 * 60 * 24 * 30,
      });
      tokenCookie.value = response.token;
    },

    async register(
      name: string,
      email: string,
      password: string,
      passwordConfirmation: string,
    ): Promise<void> {
      const url = this._buildUrl("/auth/register");

      const response = await $fetch<{ token: string; user: User }>(url, {
        method: "POST",
        headers: this._headers(),
        body: JSON.stringify({
          name,
          email,
          password,
          password_confirmation: passwordConfirmation,
        }),
      });

      this.token = response.token;
      this.user = response.user;

      const tokenCookie = useCookie("auth_token");
      tokenCookie.value = response.token;
    },

    async fetchUser(): Promise<void> {
      if (!this.token) return;
      try {
        const url = this._buildUrl("/auth/me");
        const response = await $fetch<{ data: User }>(url, {
          headers: this._headers(),
        });
        this.user = response.data;
      } catch {
        this.logout();
      }
    },

    async logout(): Promise<void> {
      try {
        if (this.token) {
          const url = this._buildUrl("/auth/logout");
          await $fetch(url, {
            method: "POST",
            headers: this._headers(),
          });
        }
      } catch {
        /* ignorer */
      }

      this.user = null;
      this.token = null;

      const tokenCookie = useCookie("auth_token");
      tokenCookie.value = null;

      if (import.meta.client) {
        navigateTo("/");
      }
    },

    /** Appelé au démarrage (plugin auth-init.ts) */
    async init(): Promise<void> {
      const tokenCookie = useCookie<string | null>("auth_token");
      if (tokenCookie.value) {
        this.token = tokenCookie.value;
        await this.fetchUser();
      }
    },
  },
});
