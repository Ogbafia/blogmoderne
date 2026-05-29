import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useRuntimeConfig } from '#app';
import { useAuthStore } from '@/stores/auth';

export const useAdminUserStore = defineStore('adminUsers', () => {
  const config = useRuntimeConfig();
  const authStore = useAuthStore();
  
  const users = ref<any[]>([]);
  const pending = ref(false);

  const getHeaders = () => ({
    'Authorization': `Bearer ${authStore.token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  });

  const fetchUsers = async () => {
    pending.value = true;
    try {
      const res = await fetch(`${config.public.apiBase}/admin/users`, {
        headers: getHeaders()
      });
      if (res.ok) {
        const data = await res.json();
        users.value = data.data.data || data.data; // Handle pagination
      }
    } catch (e) {
      console.error(e);
    } finally {
      pending.value = false;
    }
  };

  const updateRole = async (id: number, role: string) => {
    try {
      await fetch(`${config.public.apiBase}/admin/users/${id}/role`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify({ role })
      });
      await fetchUsers();
    } catch (e) { console.error(e); }
  };

  const destroy = async (id: number) => {
    try {
      await fetch(`${config.public.apiBase}/admin/users/${id}`, {
        method: 'DELETE',
        headers: getHeaders()
      });
      await fetchUsers();
    } catch (e) { console.error(e); }
  };

  return { users, pending, fetchUsers, updateRole, destroy };
});
