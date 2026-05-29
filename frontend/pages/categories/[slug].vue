<template>
  <div class="min-h-screen bg-background">
    <Head>
      <Title>{{ category?.name ? `${category.name} | BlogModerne` : 'Catégorie' }}</Title>
      <Meta name="description" :content="category?.description ?? `Articles de la catégorie ${category?.name}`" />
    </Head>

    <!-- Hero avec image de fond -->
    <section
      v-if="category || pending"
      class="relative overflow-hidden text-on-primary min-h-[380px] flex items-end"
    >
      <div class="absolute inset-0">
        <!-- Image de la catégorie en fond -->
        <img
          v-if="category?.image_url"
          :src="category.image_url"
          :alt="category.name"
          class="w-full h-full object-cover"
        />
        <div
          v-else
          class="w-full h-full"
          :style="{ backgroundColor: category?.color || '#667EEA' }"
        ></div>
        <!-- Overlay sombre pour lisibilité -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/10"></div>
      </div>

      <div class="relative z-10 w-full max-w-6xl mx-auto px-4 md:px-6 pb-12 md:pb-16 pt-24">
        <!-- Bouton retour -->
        <NuxtLink
          to="/categories"
          class="inline-flex items-center gap-2 text-white/80 hover:text-white mb-8 transition-colors group underline-offset-4 hover:underline"
        >
          <span class="material-symbols-outlined text-[20px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
          <span class="font-body-md">Retour aux catégories</span>
        </NuxtLink>

        <!-- Titre et description -->
        <div v-if="category">
          <h1 class="font-display text-5xl md:text-6xl font-bold text-white mb-4 drop-shadow-lg">
            {{ category.name }}
          </h1>
          <p v-if="category.description" class="font-body-lg text-body-lg text-white/85 max-w-2xl mb-6">
            {{ category.description }}
          </p>
          <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/15 backdrop-blur-sm rounded-full text-white text-sm font-bold">
            <span class="material-symbols-outlined text-[18px]">article</span>
            {{ articles.length }} articles publiés
          </span>
        </div>
        <div v-else-if="pending" class="animate-pulse">
          <div class="h-12 bg-white/20 rounded-lg w-64 mb-4"></div>
          <div class="h-5 bg-white/15 rounded w-96"></div>
        </div>
      </div>
    </section>

    <!-- Articles -->
    <section class="max-w-7xl mx-auto px-4 md:px-6 py-16 md:py-20">
      <!-- Skeleton -->
      <div v-if="pending" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <div v-for="n in 6" :key="n" class="animate-pulse rounded-2xl overflow-hidden border border-outline-variant">
          <div class="aspect-video bg-surface-container-high"></div>
          <div class="p-6 space-y-3">
            <div class="h-4 bg-surface-container-high rounded w-1/4"></div>
            <div class="h-5 bg-surface-container-high rounded w-3/4"></div>
            <div class="h-4 bg-surface-container-high rounded w-full"></div>
          </div>
        </div>
      </div>

      <!-- Grille articles -->
      <div v-else-if="articles.length" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <ArticleCard
          v-for="article in articles"
          :key="article.id"
          :article="article"
        />
      </div>

      <!-- Empty -->
      <div v-else class="text-center py-24">
        <span class="material-symbols-outlined text-7xl text-outline mb-6 block opacity-40">article</span>
        <h3 class="font-headline-lg text-headline-lg text-on-surface mb-2">Aucun article dans cette catégorie</h3>
        <p class="font-body-md text-body-md text-secondary">Revenez bientôt !</p>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import ArticleCard from '@/components/articles/ArticleCard.vue'

const config = useRuntimeConfig()
const route  = useRoute()
const slug   = route.params.slug as string

const category = ref<any>(null)
const articles  = ref<any[]>([])
const pending   = ref(true)

onMounted(async () => {
  pending.value = true
  try {
    // Catégorie
    const catRes = await $fetch<{ data: any }>(`${config.public.apiBase}/categories/${slug}`)
    category.value = catRes.data

    // Articles de la catégorie
    const artRes = await $fetch<{ data: any[] }>(`${config.public.apiBase}/articles?category=${slug}`)
    articles.value = artRes.data ?? []
  } catch (err) {
    console.error('Erreur chargement catégorie:', err)
  } finally {
    pending.value = false
  }
})
</script>
