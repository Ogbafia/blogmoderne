import { defineStore } from "pinia";

export const useCategoryStore = defineStore("categories", {
  state: () => ({
    list: [] as any[],
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetchAll(): Promise<any[]> {
      this.loading = true;
      this.error = null;
      try {
        const config = useRuntimeConfig();
        const response = await $fetch<{ data: any[] }>(
          `${config.public.apiBase}/categories`,
        );
        this.list = response.data ?? [];
        return this.list;
      } catch (e: any) {
        this.error = e?.message ?? "Erreur lors du chargement des catégories.";
        return [];
      } finally {
        this.loading = false;
      }
    },

    async fetchBySlug(slug: string): Promise<any> {
      this.loading = true;
      this.error = null;
      try {
        const config = useRuntimeConfig();
        const response = await $fetch<{ data: any }>(
          `${config.public.apiBase}/categories/${slug}`,
        );
        return response.data;
      } catch (e: any) {
        this.error = e?.message ?? "Catégorie introuvable.";
        throw e;
      } finally {
        this.loading = false;
      }
    },
  },
});
