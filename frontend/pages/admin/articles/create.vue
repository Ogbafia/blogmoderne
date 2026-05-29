<template>
  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Nouvel Article</h1>
    <form @submit.prevent="save" class="space-y-4">
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
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
      </div>
      <div>
        <label class="block font-medium">Couverture (image)</label>
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
      <button type="submit" class="bg-primary text-on-primary px-4 py-2 rounded hover:opacity-90 transition">
        Enregistrer
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const api = useApi()

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

const categories = ref<Array<any>>([])

const loadCategories = async () => {
  const { data } = await api.get<{ data: any[] }>('/admin/categories')
  categories.value = data
}

onMounted(loadCategories)

const onFileChange = (e: Event) => {
  const files = (e.target as HTMLInputElement).files
  if (files && files[0]) {
    form.value.cover_image = files[0]
  }
}

const save = async () => {
  const payload = new FormData()
  Object.entries(form.value).forEach(([key, value]) => {
    if (value !== null && value !== undefined) {
      payload.append(key, value as any)
    }
  })
  await api.post('/articles', payload, { headers: { 'Content-Type': 'multipart/form-data' } })
  router.push('/admin/articles')
}
</script>

<style scoped>
/* Minimal styling – rely on global design tokens */
</style>
