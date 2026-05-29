<template>
  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Modifier l'Article</h1>
    <div v-if="pending" class="text-center py-10">
      <span class="material-symbols-outlined animate-spin text-3xl">refresh</span>
    </div>
    <form v-else @submit.prevent="save" class="space-y-4">
      <div>
        <label class="block font-medium">Titre</label>
        <input v-model="form.title" type="text" required class="w-full border rounded p-2" />
      </div>
      <div>
        <label class="block font-medium">Slug</label>
        <input v-model="form.slug" type="text" required class="w-full border rounded p-2" />
      </div>
      <div>
        <label class="block font-medium">Extrait</label>
        <textarea v-model="form.excerpt" class="w-full border rounded p-2" rows="3"></textarea>
      </div>
      <div>
        <label class="block font-medium">Contenu</label>
        <textarea v-model="form.content" class="w-full border rounded p-2" rows="8" required></textarea>
      </div>
      <div>
        <label class="block font-medium">Catégorie</label>
        <select v-model="form.category_id" class="w-full border rounded p-2" required>
          <option value="" disabled>Choisir une catégorie</option>
          <option v-for="cat in storeCategories.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
      </div>
      <div>
        <label class="block font-medium">Couverture (image actuelle : <a v-if="currentImage" :href="currentImage" target="_blank" class="text-primary underline">Voir</a>)</label>
        <input @change="onFileChange" type="file" accept="image/*" class="w-full" />
      </div>
      <div class="flex items-center space-x-4">
        <label class="flex items-center">
          <input v-model="form.is_featured" type="checkbox" class="mr-2" />
          Mettre en avant
        </label>
        <label class="flex items-center">
          <input v-model="form.status" type="radio" value="draft" class="mr-2" />
          Brouillon
        </label>
        <label class="flex items-center">
          <input v-model="form.status" type="radio" value="published" class="mr-2" />
          Publié
        </label>
      </div>
      <div class="flex gap-4">
        <button type="button" @click="router.push('/admin/articles')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Annuler</button>
        <button type="submit" class="bg-primary text-on-primary px-4 py-2 rounded hover:opacity-90 transition">
          Enregistrer les modifications
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAdminCategoryStore } from '@/stores/adminCategories'
import { useAuthStore } from '@/stores/auth'
import { useRuntimeConfig } from '#app'

definePageMeta({ layout: 'admin', middleware: ['auth', 'admin'] })

const router = useRouter()
const route = useRoute()
const config = useRuntimeConfig()
const storeCategories = useAdminCategoryStore()
const authStore = useAuthStore()

const id = route.params.id as string

const form = ref({
  title: '',
  slug: '',
  excerpt: '',
  content: '',
  category_id: null as number | null,
  is_featured: false,
  status: 'draft',
  cover_image: null as File | null,
})

const currentImage = ref<string|null>(null)
const pending = ref(true)

const getHeaders = () => ({
  'Authorization': `Bearer ${authStore.token}`,
  'Accept': 'application/json'
})

const loadData = async () => {
  await storeCategories.fetchCategories()
  try {
    const res = await fetch(`${config.public.apiBase}/articles/${id}`, {
      headers: getHeaders()
    })
    if (res.ok) {
      const data = await res.json()
      const article = data.data
      form.value = {
        title: article.title,
        slug: article.slug,
        excerpt: article.excerpt || '',
        content: article.content || '',
        category_id: article.category?.id || null,
        is_featured: article.is_featured || false,
        status: article.status || 'draft',
        cover_image: null
      }
      currentImage.value = article.cover_image_url
    }
  } catch(e) {
    console.error(e)
  } finally {
    pending.value = false
  }
}

onMounted(loadData)

const onFileChange = (e: Event) => {
  const files = (e.target as HTMLInputElement).files
  if (files && files[0]) {
    form.value.cover_image = files[0]
  }
}

const save = async () => {
  const payload = new FormData()
  // Add _method for PUT simulating
  payload.append('_method', 'PUT')

  Object.entries(form.value).forEach(([key, value]) => {
    if (value !== null && value !== undefined) {
      if (key === 'is_featured') {
        payload.append(key, value ? '1' : '0')
      } else {
        payload.append(key, value as any)
      }
    }
  })

  try {
    const res = await fetch(`${config.public.apiBase}/articles/${id}`, {
      method: 'POST', // Laravel uses POST + _method=PUT for multipart forms
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      },
      body: payload
    })
    if (res.ok) {
      router.push('/admin/articles')
    } else {
      const err = await res.json()
      alert('Erreur: ' + (err.message || 'Validation échouée'))
    }
  } catch (e) {
    console.error(e)
  }
}
</script>
