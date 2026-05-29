<template>
  <div class="min-h-screen bg-background">
    <Head>
      <Title>Tous les articles | BlogModerne</Title>
      <Meta name="description" content="Tous les articles du blog — Laravel, Vue, Nuxt, DevOps, Architecture." />
    </Head>

    <!-- ══ HERO ══ -->
    <section class="bg-gradient-to-br from-on-secondary-fixed to-primary/80 text-on-primary py-14 md:py-20">
      <div class="max-w-6xl mx-auto px-4 md:px-6">
        <h1 class="font-display text-5xl md:text-6xl font-bold mb-4">📚 Articles</h1>
        <p class="font-body-lg text-body-lg text-on-primary/85 max-w-2xl mb-6">
          Tous nos articles sur le développement web moderne — tutoriels, bonnes pratiques, retours d'expérience.
        </p>
        <div class="flex items-center gap-3 flex-wrap">
          <span class="px-4 py-1.5 bg-white/15 backdrop-blur-sm rounded-full text-sm font-bold">
            {{ store.meta?.total ?? store.list.length }} articles
          </span>
        </div>
      </div>
    </section>

    <!-- ══ FILTRES PAR CATÉGORIE ══ -->
    <div class="sticky top-16 z-30 bg-surface/95 backdrop-blur-md border-b border-outline-variant">
      <div class="max-w-7xl mx-auto px-4 md:px-6 py-3 flex gap-2 overflow-x-auto scrollbar-none">
        <button
          @click="selectCategory(null)"
          :class="[
            'flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-bold transition-all border',
            activeCategory === null
              ? 'bg-primary text-on-primary border-primary'
              : 'bg-surface text-secondary border-outline-variant hover:border-primary hover:text-primary'
          ]"
        >
          Tous
        </button>
        <button
          v-for="cat in categories"
          :key="cat.slug"
          @click="selectCategory(cat.slug)"
          :class="[
            'flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-bold transition-all border',
            activeCategory === cat.slug
              ? 'bg-primary text-on-primary border-primary'
              : 'bg-surface text-secondary border-outline-variant hover:border-primary hover:text-primary'
          ]"
        >
          {{ cat.name }}
        </button>
      </div>
    </div>

    <!-- ══ GRILLE D'ARTICLES ══ -->
    <section class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-16">

      <!-- Skeleton -->
      <div v-if="store.loading" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <div v-for="n in 6" :key="n" class="animate-pulse rounded-2xl overflow-hidden border border-outline-variant">
          <div class="aspect-video bg-surface-container-high"></div>
          <div class="p-6 space-y-3">
            <div class="h-3 bg-surface-container-high rounded w-1/4"></div>
            <div class="h-5 bg-surface-container-high rounded w-3/4"></div>
            <div class="h-3 bg-surface-container-high rounded w-full"></div>
            <div class="h-3 bg-surface-container-high rounded w-2/3"></div>
          </div>
        </div>
      </div>

      <!-- Articles -->
      <div v-else-if="store.list.length" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <ArticleCard
          v-for="article in store.list"
          :key="article.id"
          :article="article as any"
        />
      </div>

      <!-- Empty -->
      <div v-else class="text-center py-24">
        <span class="material-symbols-outlined text-7xl text-outline mb-4 block opacity-40">article</span>
        <h3 class="font-headline-lg text-on-surface mb-2">Aucun article disponible</h3>
        <p class="text-secondary">Essayez une autre catégorie ou revenez bientôt.</p>
      </div>

      <!-- Load More -->
      <div v-if="store.meta && store.hasNextPage && !store.loading" class="flex justify-center mt-14">
        <button
          @click="loadMore"
          class="group inline-flex items-center gap-2 px-8 py-4 bg-primary text-on-primary font-bold rounded-full hover:opacity-90 hover:-translate-y-0.5 transition-all shadow-lg"
        >
          Charger plus d'articles
          <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </button>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import ArticleCard from '@/components/articles/ArticleCard.vue'
import { useArticleStore } from '~/stores/articles'
import { useCategoryStore } from '~/stores/categories'

const store         = useArticleStore()
const categoryStore = useCategoryStore()
const categories    = ref<any[]>([])
const activeCategory = ref<string | null>(null)

async function selectCategory(slug: string | null) {
  activeCategory.value = slug
  store.loading = true
  await store.fetchArticles(slug ? { category: slug } : undefined)
}

async function loadMore() {
  if (!store.meta) return
  const nextPage = store.meta.current_page + 1
  await store.fetchArticles({
    page: nextPage,
    per_page: store.meta.per_page,
    ...(activeCategory.value ? { category: activeCategory.value } : {}),
  })
}

onMounted(async () => {
  // Charger les catégories pour les filtres
  if (!categoryStore.list.length) {
    await categoryStore.fetchAll()
  }
  categories.value = categoryStore.list

  // Charger les articles si pas encore chargés
  if (!store.list.length) {
    await store.fetchArticles()
  }
})
</script>
