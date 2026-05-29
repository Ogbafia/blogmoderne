<template>
  <div>
    <Head>
      <Title>BlogModerne | Développement Web Moderne</Title>
      <Meta name="description" content="Blog moderne dédié aux développeurs — Laravel, Nuxt, Vue, architecture web." />
    </Head>

    <!-- ══ HERO : Article à la une ══ -->
    <section class="py-10 md:py-16">
      <div v-if="featured" class="group relative grid md:grid-cols-2 gap-0 bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300">
        <!-- Image -->
        <div class="relative h-64 md:h-auto min-h-[320px] overflow-hidden bg-surface-container">
          <NuxtLink :to="`/articles/${featured.slug}`">
            <img
              v-if="featured.cover_image_url"
              :src="featured.cover_image_url"
              :alt="featured.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/20 to-primary/5">
              <span class="material-symbols-outlined text-6xl text-primary/40">article</span>
            </div>
          </NuxtLink>
          <!-- Badge catégorie sur l'image -->
          <div v-if="featured.category" class="absolute top-4 left-4">
            <span
              class="px-3 py-1 rounded-full text-xs font-bold text-white backdrop-blur-sm"
              :style="{ backgroundColor: featured.category.color || '#b80035' }"
            >
              {{ featured.category.name }}
            </span>
          </div>
        </div>

        <!-- Contenu -->
        <div class="p-8 md:p-12 flex flex-col justify-center gap-5">
          <div class="flex items-center gap-2 text-secondary text-sm">
            <span class="material-symbols-outlined text-[16px]">schedule</span>
            <span>{{ featured.reading_time }} min de lecture</span>
            <span>·</span>
            <span>{{ formatDate(featured.published_at) }}</span>
          </div>

          <NuxtLink :to="`/articles/${featured.slug}`">
            <h1 class="font-display text-3xl md:text-4xl font-bold text-on-surface leading-tight hover:text-primary transition-colors">
              {{ featured.title }}
            </h1>
          </NuxtLink>

          <p class="font-body-lg text-body-lg text-on-surface-variant line-clamp-3">
            {{ featured.excerpt }}
          </p>

          <div class="flex items-center gap-3 pt-2 border-t border-outline-variant">
            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm">
              {{ featured.author?.name?.charAt(0) ?? 'A' }}
            </div>
            <div>
              <p class="font-bold text-on-surface text-sm">{{ featured.author?.name }}</p>
              <p class="text-secondary text-xs">Auteur</p>
            </div>
            <NuxtLink
              :to="`/articles/${featured.slug}`"
              class="ml-auto flex items-center gap-2 px-5 py-2 bg-primary text-on-primary text-sm font-bold rounded-full hover:opacity-90 transition-opacity"
            >
              Lire l'article
              <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Skeleton hero -->
      <div v-else-if="articleStore.loading" class="grid md:grid-cols-2 rounded-2xl overflow-hidden border border-outline-variant animate-pulse">
        <div class="h-64 md:h-96 bg-surface-container"></div>
        <div class="p-12 space-y-4">
          <div class="h-4 bg-surface-container rounded w-1/3"></div>
          <div class="h-8 bg-surface-container rounded w-4/5"></div>
          <div class="h-4 bg-surface-container rounded w-full"></div>
          <div class="h-4 bg-surface-container rounded w-2/3"></div>
        </div>
      </div>
    </section>

    <!-- ══ DERNIERS ARTICLES (3 max) ══ -->
    <section class="py-10 md:py-16 border-t border-outline-variant">
      <div class="flex justify-between items-center mb-10">
        <div>
          <p class="text-primary text-sm font-bold uppercase tracking-widest mb-1">Récents</p>
          <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Derniers articles</h2>
        </div>
        <NuxtLink
          to="/articles"
          class="hidden md:inline-flex items-center gap-2 text-primary font-bold text-sm hover:underline group"
        >
          Voir tous les articles
          <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </NuxtLink>
      </div>

      <!-- Skeleton -->
      <div v-if="articleStore.loading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="i in 3" :key="i" class="animate-pulse rounded-2xl overflow-hidden border border-outline-variant">
          <div class="aspect-video bg-surface-container"></div>
          <div class="p-6 space-y-3">
            <div class="h-3 bg-surface-container rounded w-1/4"></div>
            <div class="h-5 bg-surface-container rounded w-3/4"></div>
            <div class="h-3 bg-surface-container rounded w-full"></div>
          </div>
        </div>
      </div>

      <!-- 3 articles max sur home -->
      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <ArticleCard
          v-for="article in latestThree"
          :key="article.id"
          :article="article as any"
        />
      </div>

      <!-- Lien mobile -->
      <div class="text-center mt-10 md:hidden">
        <NuxtLink to="/articles" class="inline-flex items-center gap-2 text-primary font-bold text-sm border border-primary px-6 py-3 rounded-full hover:bg-primary hover:text-on-primary transition-all">
          Tous les articles
          <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </NuxtLink>
      </div>
    </section>

    <!-- ══ NEWSLETTER ══ -->
    <section class="py-10 md:py-16">
      <div class="bg-on-secondary-fixed rounded-2xl p-8 md:p-14 flex flex-col md:flex-row items-center justify-between gap-10 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
          <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full border-[36px] border-primary"></div>
          <div class="absolute -left-8 -bottom-8 w-56 h-56 rounded-full border-[18px] border-secondary"></div>
        </div>
        <div class="relative z-10 max-w-lg">
          <h2 class="font-headline-lg text-headline-lg text-on-primary mb-3">Restez à jour</h2>
          <p class="font-body-md text-body-md text-on-primary/80">
            Newsletter hebdomadaire : conseils techniques, tutoriels et dernières tendances.
          </p>
        </div>
        <div class="relative z-10 w-full max-w-md">
          <form @submit.prevent="handleSubscribe" class="flex flex-col sm:flex-row gap-3">
            <input
              v-model="email"
              class="flex-grow bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20 font-body-md"
              placeholder="votre@email.com"
              type="email"
              required
            />
            <button
              type="submit"
              class="px-7 py-3 bg-primary text-white font-bold rounded-lg hover:opacity-90 transition-all active:scale-95 whitespace-nowrap"
            >
              {{ subscribed ? '✓ Merci !' : "S'abonner" }}
            </button>
          </form>
          <p class="text-white/40 text-xs mt-2 text-center sm:text-left">Désabonnement à tout moment.</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import ArticleCard from '@/components/articles/ArticleCard.vue'

const articleStore = useArticleStore()

const email      = ref('')
const subscribed = ref(false)

// Article à la une = le premier featured, sinon le premier de la liste
const featured = computed(() =>
  articleStore.featured[0] ?? articleStore.list[0] ?? null
)

// 3 derniers articles (pas le featured)
const latestThree = computed(() => {
  const all = articleStore.list.filter(a => a.id !== featured.value?.id)
  return all.slice(0, 3)
})

const formatDate = (d: string | null) =>
  d ? new Intl.DateTimeFormat('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(d)) : ''

async function handleSubscribe() {
  if (!email.value) return
  subscribed.value = true
  email.value = ''
  setTimeout(() => { subscribed.value = false }, 3000)
}

// Chargement SSR
await Promise.all([
  articleStore.fetchFeatured(),
  articleStore.fetchArticles({ sort: 'latest', per_page: 7 }),
])
</script>
