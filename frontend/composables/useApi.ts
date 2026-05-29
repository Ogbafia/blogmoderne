// composables/useApi.ts
// Client HTTP centralisé pour toutes les requêtes vers l'API Laravel

/**
 * Résout l'URL de base selon le contexte d'exécution.
 *
 * Problème : `apiBase = '/proxy'` (chemin relatif) n'a pas de sens
 * côté serveur SSR car il n'y a pas de domaine de base.
 *
 * - Côté client (navigateur) : '/proxy/...'  → même origine → OK
 * - Côté serveur SSR           : 'http://localhost:3000/proxy/...' → OK
 */
function resolveBase(base: string): string {
  if (base.startsWith("http")) return base;

  if (import.meta.server) {
    // En SSR : construire l'URL absolue vers le serveur Nuxt local
    return `http://localhost:3000${base}`;
  }

  return base;
}

export const useApi = () => {
  const config = useRuntimeConfig();
  const authStore = useAuthStore();

  const apiBase = resolveBase(config.public.apiBase as string);

  function buildHeaders(extra: Record<string, string> = {}) {
    return {
      Accept: "application/json",
      ...(authStore.token
        ? { Authorization: `Bearer ${authStore.token}` }
        : {}),
      ...extra,
    };
  }

  async function apiFetch<T>(path: string, options: any = {}): Promise<T> {
    const { params, body, ...fetchOptions } = options;

    let url = `${apiBase}${path}`;

    if (params) {
      const qs = new URLSearchParams(
        Object.fromEntries(
          Object.entries(params)
            .filter(([, v]) => v !== undefined && v !== null)
            .map(([k, v]) => [k, String(v)]),
        ),
      );
      url += "?" + qs.toString();
    }

    const isFormData = body instanceof FormData;
    const contentHeader = isFormData
      ? {}
      : { "Content-Type": "application/json" };
    const serializedBody = body && !isFormData ? JSON.stringify(body) : body;

    try {
      return await $fetch<T>(url, {
        ...fetchOptions,
        body: serializedBody,
        headers: {
          ...buildHeaders(),
          ...contentHeader,
          ...(fetchOptions.headers ?? {}),
        },
        async onResponseError({ response }) {
          if (response.status === 401) {
            authStore.user = null;
            authStore.token = null;
            const cookie = useCookie("auth_token");
            cookie.value = null;
            if (import.meta.client) navigateTo("/login");
          }
        },
      });
    } catch (error: any) {
      if (error?.data?.errors) throw error.data.errors;
      if (error?.data?.message) throw error.data.message;
      throw error?.message ?? "Une erreur est survenue.";
    }
  }

  return {
    get: <T>(path: string, params?: Record<string, any>) =>
      apiFetch<T>(path, { method: "GET", params }),
    post: <T>(path: string, body?: any) =>
      apiFetch<T>(path, { method: "POST", body }),
    put: <T>(path: string, body?: any) =>
      apiFetch<T>(path, { method: "PUT", body }),
    delete: <T>(path: string) => apiFetch<T>(path, { method: "DELETE" }),
    upload: <T>(path: string, formData: FormData) =>
      apiFetch<T>(path, { method: "POST", body: formData }),
  };
};
