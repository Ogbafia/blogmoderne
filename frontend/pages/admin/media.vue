<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-display font-bold">Médiathèque</h1>
      <button @click="triggerUpload" class="bg-primary text-white px-4 py-2 rounded shadow flex items-center gap-2 hover:bg-primary/90 transition-colors">
        <span class="material-symbols-outlined text-[20px]">upload_file</span>
        Ajouter un média
      </button>
      <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleFileUpload" />
    </div>

    <!-- Upload Progress -->
    <div v-if="uploading" class="mb-6 p-4 bg-primary-container text-on-primary-container rounded flex items-center gap-3">
      <span class="material-symbols-outlined animate-spin">refresh</span>
      Envoi en cours...
    </div>

    <!-- Media Grid -->
    <div v-if="pending" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
      <div v-for="n in 12" :key="n" class="aspect-square bg-gray-200 animate-pulse rounded-lg"></div>
    </div>
    
    <div v-else-if="mediaList.length" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
      <div v-for="media in mediaList" :key="media.id" class="group relative aspect-square rounded-lg border border-outline-variant overflow-hidden bg-surface flex items-center justify-center">
        <img :src="getMediaUrl(media)" :alt="media.alt_text || media.filename" class="max-w-full max-h-full object-contain p-1" />
        
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-center items-center gap-2 text-white">
          <button @click="copyUrl(getMediaUrl(media))" class="hover:text-primary transition-colors flex flex-col items-center">
            <span class="material-symbols-outlined">content_copy</span>
            <span class="text-xs">Copier le lien</span>
          </button>
          <button @click="deleteMedia(media.id)" class="hover:text-error transition-colors flex flex-col items-center mt-2">
            <span class="material-symbols-outlined">delete</span>
            <span class="text-xs">Supprimer</span>
          </button>
        </div>
      </div>
    </div>
    
    <div v-else class="text-center py-12 text-gray-500 border border-dashed border-outline-variant rounded-lg">
      <span class="material-symbols-outlined text-4xl mb-2 text-gray-400">imagesmode</span>
      <p>Aucun média trouvé. Commencez par en ajouter.</p>
    </div>

    <!-- Pagination -->
    <div v-if="meta && meta.last_page > 1" class="flex justify-center items-center gap-4 mt-8">
      <button :disabled="meta.current_page === 1" @click="fetchMedia(meta.current_page - 1)" class="p-2 border rounded hover:bg-gray-50 disabled:opacity-50">
        Précédent
      </button>
      <span class="text-sm">Page {{ meta.current_page }} sur {{ meta.last_page }}</span>
      <button :disabled="meta.current_page === meta.last_page" @click="fetchMedia(meta.current_page + 1)" class="p-2 border rounded hover:bg-gray-50 disabled:opacity-50">
        Suivant
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRuntimeConfig } from '#app';
import { useAuthStore } from '@/stores/auth';

definePageMeta({
  layout: 'admin',
  middleware: ['auth', 'admin']
});

const config = useRuntimeConfig();
const authStore = useAuthStore();
const fileInput = ref<HTMLInputElement | null>(null);

const mediaList = ref<any[]>([]);
const meta = ref<any>(null);
const pending = ref(true);
const uploading = ref(false);

const headers = {
  'Authorization': `Bearer ${authStore.token}`,
  'Accept': 'application/json'
};

const getMediaUrl = (media: any) => {
  // If the path is already a full URL (e.g. starting with http)
  if (media.path.startsWith('http')) return media.path;
  // Otherwise, construct local URL based on public disk
  return `${config.public.apiBase.replace('/api', '')}/storage/${media.path}`;
};

const fetchMedia = async (page = 1) => {
  pending.value = true;
  try {
    const res = await fetch(`${config.public.apiBase}/media?page=${page}`, { headers });
    if (res.ok) {
      const data = await res.json();
      mediaList.value = data.data.data;
      meta.value = data.data; // Includes current_page, last_page, etc.
    }
  } catch (error) {
    console.error("Failed to fetch media", error);
  } finally {
    pending.value = false;
  }
};

const triggerUpload = () => {
  fileInput.value?.click();
};

const handleFileUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file) return;

  uploading.value = true;
  const formData = new FormData();
  formData.append('file', file);
  
  try {
    const res = await fetch(`${config.public.apiBase}/media/upload`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${authStore.token}` },
      body: formData
    });
    
    if (res.ok) {
      alert('Média ajouté avec succès');
      await fetchMedia(meta.value?.current_page || 1);
    } else {
      const err = await res.json();
      alert(`Erreur: ${err.message}`);
    }
  } catch (error) {
    console.error("Upload failed", error);
    alert('Erreur réseau lors de l\'upload');
  } finally {
    uploading.value = false;
    if (fileInput.value) fileInput.value.value = '';
  }
};

const deleteMedia = async (id: number) => {
  if (!confirm('Voulez-vous vraiment supprimer ce média ?')) return;
  
  try {
    const res = await fetch(`${config.public.apiBase}/media/${id}`, {
      method: 'DELETE',
      headers
    });
    if (res.ok) {
      await fetchMedia(meta.value?.current_page || 1);
    } else {
      alert('Erreur lors de la suppression');
    }
  } catch (error) {
    console.error("Delete failed", error);
  }
};

const copyUrl = (url: string) => {
  navigator.clipboard.writeText(url);
  alert('Lien copié dans le presse-papier !');
};

onMounted(() => {
  fetchMedia();
});
</script>
