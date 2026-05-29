<template>
  <div class="min-h-screen bg-background">
    <Head>
      <Title>Catégories | BlogModerne</Title>
      <Meta name="description" content="Explorez nos catégories et trouvez les articles qui vous intéressent." />
    </Head>

    <!-- Hero -->
    <section class="bg-gradient-to-br from-on-secondary-fixed to-primary-container text-on-primary py-16 md:py-24">
      <div class="max-w-6xl mx-auto px-4 md:px-6">
        <h1 class="font-display text-5xl md:text-6xl text-on-primary font-bold mb-4">🏷️ Catégories</h1>
        <p class="font-body-lg text-body-lg text-on-primary/90 max-w-2xl">
          Découvrez nos catégories thématiques et explorez les articles correspondants.
        </p>
      </div>
    </section>

    <!-- Grid -->
    <section class="max-w-7xl mx-auto px-4 md:px-6 py-16 md:py-20">

      <!-- Skeleton -->
      <div v-if="store.loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="n in 6" :key="n" class="animate-pulse rounded-3xl overflow-hidden border border-outline-variant">
          <div class="h-52 bg-surface-container-high"></div>
          <div class="p-6 space-y-3">
            <div class="h-5 bg-surface-container-high rounded w-1/2"></div>
            <div class="h-4 bg-surface-container-high rounded w-full"></div>
            <div class="h-4 bg-surface-container-high rounded w-2/3"></div>
          </div>
        </div>
      </div>

      <!-- Cards -->
      <div v-else-if="store.list.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <NuxtLink
          v-for="cat in store.list"
          :key="cat.id"
          :to="`/categories/${cat.slug}`"
          class="group overflow-hidden rounded-3xl border border-outline-variant bg-surface-container-lowest shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 flex flex-col"
        >
          <!-- Image -->
          <div class="relative h-52 overflow-hidden bg-surface-container shrink-0">
            <img
              v-if="cat.image_url"
              :src="cat.image_url"
              :alt="cat.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div
              v-else
              class="w-full h-full flex items-center justify-center text-white text-4xl font-bold"
              :style="{ backgroundColor: cat.color || '#667EEA' }"
            >
              {{ cat.name.charAt(0) }}
            </div>
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
            <!-- Badge compteur -->
            <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end">
              <span
                class="px-3 py-1 rounded-full text-xs font-bold bg-white/90 backdrop-blur-sm"
                :style="{ color: cat.color || '#111' }"
              >
                {{ cat.articles_count ?? 0 }} articles
              </span>
              <span class="material-symbols-outlined text-white text-[22px] group-hover:translate-x-1 transition-transform">
                arrow_forward
              </span>
            </div>
          </div>

          <!-- Texte -->
          <div class="p-6 flex flex-col gap-2 flex-grow">
            <h2 class="font-headline-md text-headline-md text-on-surface group-hover:text-primary transition-colors underline-offset-4 group-hover:underline">
              {{ cat.name }}
            </h2>
            <p class="font-body-md text-body-md text-on-surface-variant line-clamp-2">
              {{ cat.description }}
            </p>
          </div>
        </NuxtLink>
      </div>

      <!-- Empty -->
      <div v-else class="text-center py-24">
        <span class="material-symbols-outlined text-7xl text-outline mb-6 block opacity-40">category</span>
        <h3 class="font-headline-lg text-headline-lg text-on-surface mb-2">Aucune catégorie disponible</h3>
        <p class="font-body-md text-body-md text-secondary">Revenez bientôt !</p>
      </div>

    </section>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useCategoryStore } from '@/stores/categories'

const store = useCategoryStore()

onMounted(async () => {
  if (!store.list.length) {
    await store.fetchAll()
  }
})
</script>
