<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Gestion des Catégories</h1>
      <button @click="openForm()" class="bg-primary text-on-primary px-4 py-2 rounded hover:opacity-90 transition">
        <span class="material-symbols-outlined align-middle">add</span>
        Nouvelle Catégorie
      </button>
    </div>
    <table class="w-full table-auto border-collapse">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Nom</th>
          <th class="px-4 py-2 text-left">Couleur</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cat in store.categories" :key="cat.id" class="border-t">
          <td class="px-4 py-2">{{ cat.name }}</td>
          <td class="px-4 py-2">
            <div class="flex items-center gap-2">
              <span class="w-4 h-4 rounded-full inline-block border" :style="{ backgroundColor: cat.color || '#ccc' }"></span>
              {{ cat.color || 'Aucune' }}
            </div>
          </td>
          <td class="px-4 py-2 space-x-2">
            <button @click="openForm(cat)" class="text-primary hover:underline">
              <span class="material-symbols-outlined align-middle">edit</span> éditer
            </button>
            <button @click="remove(cat.id)" class="text-red-600 hover:underline">
              <span class="material-symbols-outlined align-middle">delete</span> supprimer
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal Form -->
    <div v-if="isFormOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-bold mb-4">{{ editingId ? 'Modifier' : 'Ajouter' }} une catégorie</h2>
        <form @submit.prevent="save">
          <div class="mb-4">
            <label class="block mb-1 text-sm font-semibold">Nom</label>
            <input v-model="form.name" required type="text" class="w-full border rounded p-2" />
          </div>
          <div class="mb-4">
            <label class="block mb-1 text-sm font-semibold">Description</label>
            <textarea v-model="form.description" class="w-full border rounded p-2"></textarea>
          </div>
          <div class="mb-6">
            <label class="block mb-1 text-sm font-semibold">Couleur (Hex, ex: #ff0000)</label>
            <input v-model="form.color" type="text" class="w-full border rounded p-2" />
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" @click="closeForm" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Annuler</button>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:opacity-90">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAdminCategoryStore } from '@/stores/adminCategories'

definePageMeta({ layout: 'admin', middleware: ['auth', 'admin'] })

const store = useAdminCategoryStore()

const isFormOpen = ref(false)
const editingId = ref<string|number|null>(null)
const form = ref({ name: '', description: '', color: '' })

const load = async () => {
  await store.fetchCategories()
}

const openForm = (cat?: any) => {
  if (cat) {
    editingId.value = cat.id
    form.value = { name: cat.name, description: cat.description || '', color: cat.color || '' }
  } else {
    editingId.value = null
    form.value = { name: '', description: '', color: '' }
  }
  isFormOpen.value = true
}

const closeForm = () => {
  isFormOpen.value = false
}

const save = async () => {
  if (editingId.value) {
    await store.update(editingId.value, form.value)
  } else {
    await store.store(form.value)
  }
  closeForm()
}

const remove = async (id: number|string) => {
  if (confirm('Supprimer cette catégorie ?')) {
    await store.destroy(id)
  }
}

onMounted(load)
</script>

<style scoped>
table { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
th, td { font-size: 0.95rem; }
</style>
