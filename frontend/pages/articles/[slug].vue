<template>
  <div>

    <!-- ══ CHARGEMENT ══ -->
    <div v-if="pending" class="min-h-screen">
      <div class="h-96 bg-surface-container animate-pulse"></div>
      <div class="max-w-3xl mx-auto px-4 py-12 space-y-4">
        <div class="h-4 bg-surface-container rounded w-1/4"></div>
        <div class="h-10 bg-surface-container rounded w-3/4"></div>
        <div class="h-4 bg-surface-container rounded w-1/2"></div>
        <div class="h-4 bg-surface-container rounded w-full mt-8"></div>
        <div class="h-4 bg-surface-container rounded w-full"></div>
        <div class="h-4 bg-surface-container rounded w-4/5"></div>
      </div>
    </div>

    <!-- ══ ARTICLE TROUVÉ ══ -->
    <template v-else-if="article">
      <Head>
        <Title>{{ article.title }} | BlogModerne</Title>
        <Meta name="description" :content="article.excerpt ?? ''" />
        <Meta property="og:title" :content="article.title" />
        <Meta property="og:image" :content="article.cover_image_url ?? ''" />
      </Head>

      <!-- Barre de progression de lecture -->
      <div
        class="fixed top-16 left-0 h-[3px] bg-primary z-50 transition-all duration-100"
        :style="{ width: readProgress + '%' }"
      ></div>

      <!-- ── HERO IMAGE ── -->
      <div v-if="article.cover_image_url" class="relative w-full h-72 md:h-[480px] overflow-hidden bg-surface-container">
        <img
          :src="article.cover_image_url"
          :alt="article.title"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

        <!-- Catégorie badge sur l'image -->
        <div class="absolute bottom-6 left-6 right-6 flex items-end justify-between flex-wrap gap-3">
          <NuxtLink
            v-if="article.category"
            :to="`/categories/${article.category.slug}`"
            class="px-4 py-1.5 rounded-full text-sm font-bold text-white backdrop-blur-sm hover:opacity-90 transition"
            :style="{ backgroundColor: article.category.color || '#b80035' }"
          >
            {{ article.category.name }}
          </NuxtLink>
          <div class="flex items-center gap-3 text-white text-sm">
            <span class="flex items-center gap-1 bg-black/30 backdrop-blur-sm px-3 py-1 rounded-full">
              <span class="material-symbols-outlined text-[15px]">schedule</span>
              {{ article.reading_time }} min
            </span>
            <span class="flex items-center gap-1 bg-black/30 backdrop-blur-sm px-3 py-1 rounded-full">
              <span class="material-symbols-outlined text-[15px]">visibility</span>
              {{ article.views_count }}
            </span>
          </div>
        </div>
      </div>

      <!-- ── EN-TÊTE ARTICLE ── -->
      <header class="max-w-3xl mx-auto px-4 md:px-6 pt-10 pb-6">
        <!-- Catégorie + temps si pas d'image -->
        <div v-if="!article.cover_image_url" class="flex items-center gap-3 mb-5 flex-wrap">
          <NuxtLink
            v-if="article.category"
            :to="`/categories/${article.category.slug}`"
            class="px-4 py-1.5 rounded-full text-sm font-bold text-white"
            :style="{ backgroundColor: article.category.color || '#b80035' }"
          >
            {{ article.category.name }}
          </NuxtLink>
          <span class="text-secondary text-sm flex items-center gap-1">
            <span class="material-symbols-outlined text-[15px]">schedule</span>
            {{ article.reading_time }} min de lecture
          </span>
        </div>

        <!-- Titre -->
        <h1 class="font-display text-3xl md:text-5xl font-bold text-on-surface leading-tight mb-6">
          {{ article.title }}
        </h1>

        <!-- Extrait -->
        <p class="text-lg text-on-surface-variant leading-relaxed mb-8 border-l-4 border-primary pl-5 bg-surface-container-low py-3 pr-4 rounded-r-lg">
          {{ article.excerpt }}
        </p>

        <!-- Auteur + date + actions -->
        <div class="flex items-center justify-between flex-wrap gap-4 py-5 border-t border-b border-outline-variant">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-primary/20 flex items-center justify-center font-bold text-primary text-lg border-2 border-primary/30">
              {{ article.author?.name?.charAt(0) ?? 'A' }}
            </div>
            <div>
              <p class="font-bold text-on-surface text-sm">{{ article.author?.name }}</p>
              <p class="text-secondary text-xs">{{ formatDate(article.published_at) }}</p>
            </div>
          </div>
          <!-- Actions -->
          <div class="flex items-center gap-3">
            <button
              @click="toggleLike"
              class="flex items-center gap-1.5 px-4 py-2 rounded-full border transition-all text-sm font-bold"
              :class="article.is_liked
                ? 'border-primary text-primary bg-primary/10'
                : 'border-outline-variant text-secondary hover:border-primary hover:text-primary'"
            >
              <span class="material-symbols-outlined text-[18px]" :style="article.is_liked ? 'font-variation-settings: FILL 1' : ''">favorite</span>
              {{ article.likes_count }}
            </button>
            <button
              @click="copyLink"
              class="flex items-center gap-1.5 px-4 py-2 rounded-full border border-outline-variant text-secondary hover:border-primary hover:text-primary transition-all text-sm font-bold"
            >
              <span class="material-symbols-outlined text-[18px]">share</span>
              Partager
            </button>
          </div>
        </div>
      </header>

      <!-- ── CONTENU DE L'ARTICLE ── -->
      <main class="max-w-3xl mx-auto px-4 md:px-6 pb-16">
        <div
          class="article-content prose prose-lg max-w-none text-on-surface leading-relaxed"
          v-html="article.content"
        ></div>

        <!-- Tags -->
        <div v-if="article.tags?.length" class="flex flex-wrap gap-2 mt-12 pt-8 border-t border-outline-variant">
          <span class="text-secondary text-sm font-bold mr-2">Tags :</span>
          <NuxtLink
            v-for="tag in article.tags"
            :key="tag"
            :to="`/articles?tag=${tag}`"
            class="px-3 py-1 bg-surface-container border border-outline-variant rounded-lg text-xs font-bold text-secondary hover:border-primary hover:text-primary transition-all uppercase"
          >
            #{{ tag }}
          </NuxtLink>
        </div>

        <!-- Auteur card -->
        <div class="mt-12 p-6 bg-surface-container-low rounded-2xl border border-outline-variant flex items-start gap-5">
          <div class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center font-bold text-primary text-2xl border-2 border-primary/30 flex-shrink-0">
            {{ article.author?.name?.charAt(0) ?? 'A' }}
          </div>
          <div>
            <p class="font-bold text-on-surface text-lg mb-1">{{ article.author?.name }}</p>
            <p class="text-secondary text-sm">Auteur sur BlogModerne — partage son expertise en développement web moderne.</p>
          </div>
        </div>

        <!-- Retour -->
        <div class="mt-10 flex items-center gap-4">
          <NuxtLink
            to="/articles"
            class="inline-flex items-center gap-2 text-primary font-bold text-sm hover:underline group"
          >
            <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Retour aux articles
          </NuxtLink>
        </div>
      </main>
    </template>

    <!-- ══ 404 ══ -->
    <div v-else class="text-center py-32 min-h-screen flex flex-col items-center justify-center">
      <span class="material-symbols-outlined text-8xl text-outline mb-6 opacity-40">article</span>
      <h2 class="font-headline-lg text-headline-lg text-on-surface mb-2">Article introuvable</h2>
      <p class="text-secondary mb-8">Cet article n'existe pas ou a été supprimé.</p>
      <NuxtLink
        to="/articles"
        class="inline-flex items-center gap-2 bg-primary text-on-primary px-6 py-3 rounded-full font-bold hover:opacity-90 transition"
      >
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Voir tous les articles
      </NuxtLink>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

const route        = useRoute()
const articleStore = useArticleStore()
const authStore    = useAuthStore()
const config       = useRuntimeConfig()

const pending     = ref(true)
const readProgress = ref(0)

// Charger l'article par slug
async function loadArticle() {
  pending.value = true
  try {
    await articleStore.fetchBySlug(route.params.slug as string)
  } catch {
    // article introuvable — géré par le template v-else
  } finally {
    pending.value = false
  }
}

const article = computed(() => articleStore.current)

const formatDate = (d: string | null) =>
  d
    ? new Intl.DateTimeFormat('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(d))
    : ''

async function toggleLike() {
  if (!authStore.isAuthenticated) {
    navigateTo('/login')
    return
  }
  if (article.value) {
    await articleStore.toggleLike(article.value.id)
  }
}

function copyLink() {
  if (import.meta.client) {
    navigator.clipboard.writeText(window.location.href)
      .then(() => alert('Lien copié !'))
  }
}

function updateProgress() {
  if (!import.meta.client) return
  const winScroll = document.documentElement.scrollTop
  const height    = document.documentElement.scrollHeight - document.documentElement.clientHeight
  readProgress.value = height > 0 ? Math.round((winScroll / height) * 100) : 0
}

onMounted(async () => {
  await loadArticle()
  window.addEventListener('scroll', updateProgress, { passive: true })
})

onUnmounted(() => {
  if (import.meta.client) {
    window.removeEventListener('scroll', updateProgress)
  }
})
</script>

<style scoped>
.article-content :deep(h2) {
  font-size: 1.6rem;
  font-weight: 700;
  color: var(--color-on-surface, #1a1c1d);
  margin-top: 2.5rem;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e5bdbe;
}
.article-content :deep(h3) {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-on-surface, #1a1c1d);
  margin-top: 2rem;
  margin-bottom: 0.75rem;
}
.article-content :deep(p) {
  margin-bottom: 1.25rem;
  line-height: 1.85;
  color: #374151;
}
.article-content :deep(ul),
.article-content :deep(ol) {
  margin: 1rem 0 1.5rem 1.5rem;
  space-y: 0.5rem;
}
.article-content :deep(li) {
  margin-bottom: 0.5rem;
  line-height: 1.7;
  color: #374151;
}
.article-content :deep(ul li) { list-style-type: disc; }
.article-content :deep(ol li) { list-style-type: decimal; }
.article-content :deep(strong) { font-weight: 700; color: #111827; }
.article-content :deep(code) {
  background: #f3f4f6;
  color: #b80035;
  padding: 0.15em 0.4em;
  border-radius: 4px;
  font-size: 0.875em;
  font-family: ui-monospace, monospace;
}
.article-content :deep(pre) {
  background: #1e293b;
  color: #e2e8f0;
  padding: 1.25rem 1.5rem;
  border-radius: 12px;
  overflow-x: auto;
  margin: 1.5rem 0;
  font-size: 0.875rem;
  line-height: 1.7;
}
.article-content :deep(pre code) {
  background: transparent;
  color: inherit;
  padding: 0;
}
.article-content :deep(blockquote) {
  border-left: 4px solid #b80035;
  padding: 0.75rem 1.25rem;
  background: #fff5f5;
  border-radius: 0 8px 8px 0;
  margin: 1.5rem 0;
  font-style: italic;
  color: #4b1c1c;
}
</style>
