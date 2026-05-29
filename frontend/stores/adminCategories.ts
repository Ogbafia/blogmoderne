import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useRuntimeConfig } from '#app';
import { useAuthStore } from '@/stores/auth';

export const useAdminCategoryStore = defineStore('adminCategories', () => {
  const config = useRuntimeConfig();
  const authStore = useAuthStore();
  
  const categories = ref<any[]>([]);
  const pending = ref(false);

  const getHeaders = () => ({
    'Authorization': `Bearer ${authStore.token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  });

  const fetchCategories = async () => {
    pending.value = true;
    try {
      const res = await fetch(`${config.public.apiBase}/admin/categories`, {
        headers: getHeaders()
      });
      if (res.ok) {
        const data = await res.json();
        categories.value = data.data || data;
      }
    } catch (e) {
      console.error(e);
    } finally {
      pending.value = false;
    }
  };

  const store = async (payload: { name: string; description?: string; color?: string }) => {
    try {
      await fetch(`${config.public.apiBase}/admin/categories`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(payload)
      });
      await fetchCategories();
    } catch (e) { console.error(e); }
  };

  const update = async (id: string | number, payload: { name: string; description?: string; color?: string }) => {
    try {
      await fetch(`${config.public.apiBase}/admin/categories/${id}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify(payload)
      });
      await fetchCategories();
    } catch (e) { console.error(e); }
  };

  const destroy = async (id: string | number) => {
    try {
      await fetch(`${config.public.apiBase}/admin/categories/${id}`, {
        method: 'DELETE',
        headers: getHeaders()
      });
      await fetchCategories();
    } catch (e) { console.error(e); }
  };

  return { categories, pending, fetchCategories, store, update, destroy };
});
