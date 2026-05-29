<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Gestion des Utilisateurs</h1>
    </div>
    
    <div v-if="store.pending" class="text-center py-10">
      <span class="material-symbols-outlined animate-spin text-3xl">refresh</span>
    </div>
    
    <table v-else class="w-full table-auto border-collapse">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Nom</th>
          <th class="px-4 py-2 text-left">Email</th>
          <th class="px-4 py-2 text-left">Rôle</th>
          <th class="px-4 py-2 text-left">Inscrit le</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in store.users" :key="user.id" class="border-t">
          <td class="px-4 py-2 font-medium">{{ user.name }}</td>
          <td class="px-4 py-2 text-gray-600">{{ user.email }}</td>
          <td class="px-4 py-2">
            <span class="px-2 py-1 rounded text-xs font-bold uppercase" 
                  :class="user.role === 'admin' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700'">
              {{ user.role }}
            </span>
          </td>
          <td class="px-4 py-2 text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
          <td class="px-4 py-2 space-x-2">
            <button v-if="user.role !== 'admin'" @click="changeRole(user.id, 'admin')" class="text-green-600 hover:underline text-sm">
              Nommer Admin
            </button>
            <button v-else-if="user.id !== currentUserId" @click="changeRole(user.id, 'user')" class="text-orange-600 hover:underline text-sm">
              Rétrograder
            </button>
            <button v-if="user.id !== currentUserId" @click="remove(user.id)" class="text-red-600 hover:underline text-sm">
              Supprimer
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useAdminUserStore } from '@/stores/adminUsers'
import { useAuthStore } from '@/stores/auth'

definePageMeta({ layout: 'admin', middleware: ['auth', 'admin'] })

const store = useAdminUserStore()
const authStore = useAuthStore()

const currentUserId = computed(() => authStore.user?.id)

const formatDate = (d: string) => {
  return new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(d))
}

const load = async () => {
  await store.fetchUsers()
}

const changeRole = async (id: number, newRole: string) => {
  if (confirm(`Changer le rôle de cet utilisateur en ${newRole} ?`)) {
    await store.updateRole(id, newRole)
  }
}

const remove = async (id: number) => {
  if (confirm('Voulez-vous vraiment supprimer cet utilisateur ? Cette action est irréversible.')) {
    await store.destroy(id)
  }
}

onMounted(load)
</script>

<style scoped>
table { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
th, td { font-size: 0.95rem; }
</style>
