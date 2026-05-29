<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Gestion des Articles</h1>
      <NuxtLink to="/admin/articles/create" class="bg-primary text-on-primary px-4 py-2 rounded hover:opacity-90 transition">
        <span class="material-symbols-outlined align-middle">add</span>
        Nouveau Article
      </NuxtLink>
    </div>
    <table class="w-full table-auto border-collapse">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Titre</th>
          <th class="px-4 py-2 text-left">Statut</th>
          <th class="px-4 py-2 text-left">Vues</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="article in store.articles" :key="article.id" class="border-t">
          <td class="px-4 py-2">{{ article.title }}</td>
          <td class="px-4 py-2 capitalize">{{ article.status }}</td>
          <td class="px-4 py-2">{{ article.views_count }}</td>
          <td class="px-4 py-2 space-x-2">
            <NuxtLink :to="`/admin/articles/${article.id}/edit`" class="text-primary hover:underline">
              <span class="material-symbols-outlined align-middle">edit</span> éditer
            </NuxtLink>
            <button @click="togglePublish(article)" class="text-green-600 hover:underline">
              <span class="material-symbols-outlined align-middle" v-if="article.status === 'published'">visibility_off</span>
              <span class="material-symbols-outlined align-middle" v-else>visibility</span>
              {{ article.status === 'published' ? 'Dépublier' : 'Publier' }}
            </button>
            <button @click="remove(article.id)" class="text-red-600 hover:underline">
              <span class="material-symbols-outlined align-middle">delete</span> supprimer
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAdminArticleStore } from '@/stores/adminArticles'

definePageMeta({ layout: 'admin', middleware: ['auth', 'admin'] })

const store = useAdminArticleStore()
const router = useRouter()

const load = async () => {
  await store.fetchArticles()
}

const togglePublish = async (article: any) => {
  if (article.status === 'published') {
    await store.unpublish(article.id)
  } else {
    await store.publish(article.id)
  }
}

const remove = async (id: string) => {
  if (confirm('Supprimer cet article ?')) {
    await store.destroy(id)
  }
}

onMounted(load)
</script>

<style scoped>
table {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
}
th, td {
  font-size: 0.95rem;
}
</style>
