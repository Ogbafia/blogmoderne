import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useRuntimeConfig } from '#app';
import { useAuthStore } from '@/stores/auth';

export const useAdminArticleStore = defineStore('adminArticles', () => {
  const config = useRuntimeConfig();
  const authStore = useAuthStore();
  
  const articles = ref<any[]>([]);
  const pending = ref(false);

  const getHeaders = () => ({
    'Authorization': `Bearer ${authStore.token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  });

  const fetchArticles = async () => {
    pending.value = true;
    try {
      const res = await fetch(`${config.public.apiBase}/admin/articles`, {
        headers: getHeaders()
      });
      if (res.ok) {
        const data = await res.json();
        articles.value = data.data.data || data.data; // Paginated or not
      }
    } catch (e) {
      console.error(e);
    } finally {
      pending.value = false;
    }
  };

  const publish = async (id: string) => {
    try {
      await fetch(`${config.public.apiBase}/admin/articles/${id}/publish`, {
        method: 'POST',
        headers: getHeaders()
      });
      await fetchArticles();
    } catch (e) { console.error(e); }
  };

  const unpublish = async (id: string) => {
    try {
      await fetch(`${config.public.apiBase}/admin/articles/${id}/unpublish`, {
        method: 'POST',
        headers: getHeaders()
      });
      await fetchArticles();
    } catch (e) { console.error(e); }
  };

  const destroy = async (id: string) => {
    try {
      await fetch(`${config.public.apiBase}/articles/${id}`, {
        method: 'DELETE',
        headers: getHeaders()
      });
      await fetchArticles();
    } catch (e) { console.error(e); }
  };

  return { articles, pending, fetchArticles, publish, unpublish, destroy };
});
