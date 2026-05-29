<!-- components/articles/ArticleCard.vue -->
<template>
  <article class="group h-full flex flex-col overflow-hidden rounded-2xl border-2 border-outline-variant bg-surface-container-lowest transition-all duration-300 hover:shadow-2xl hover:border-primary hover:-translate-y-2">
    <!-- Image -->
    <NuxtLink :to="`/articles/${article.slug}`" class="block overflow-hidden bg-surface-container relative">
      <div class="aspect-video">
        <img
          v-if="article.cover_image_url"
          :src="article.cover_image_url"
          :alt="article.title"
          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
        />
        <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/10 to-primary/5">
          <span class="material-symbols-outlined text-4xl text-primary/40">image</span>
        </div>
      </div>
    </NuxtLink>

    <!-- Content -->
    <div class="p-6 flex flex-col flex-grow gap-4">
      <!-- Category Badge -->
      <div v-if="article.category" class="flex items-center gap-2">
        <span
          class="px-3 py-1 text-xs font-bold rounded-full transition-colors"
          :style="{
            backgroundColor: (article.category.color || '#667EEA') + '25',
            color: article.category.color || '#667EEA'
          }"
        >
          {{ article.category.name }}
        </span>
      </div>

      <!-- Titre -->
      <NuxtLink :to="`/articles/${article.slug}`" class="group/title">
        <h2 class="font-headline-md text-headline-md text-on-surface leading-snug line-clamp-2 group-hover/title:text-primary transition-colors duration-200 underline-offset-4 group-hover/title:underline">
          {{ article.title }}
        </h2>
      </NuxtLink>

      <!-- Extrait -->
      <p class="text-sm text-on-surface-variant line-clamp-2 flex-grow">{{ article.excerpt }}</p>

      <!-- Footer -->
      <div class="flex items-center justify-between pt-4 border-t border-outline-variant">
        <div class="flex items-center gap-2">
          <div v-if="article.author" class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-primary text-[11px] font-bold flex-shrink-0">
            {{ article.author.name.charAt(0) }}
          </div>
          <span class="text-xs text-secondary">{{ article.author?.name }}</span>
        </div>
        <div class="flex items-center gap-3 text-xs text-secondary">
          <span class="flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">schedule</span> {{ article.reading_time }}min
          </span>
          <span class="flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">visibility</span> {{ article.views_count }}
          </span>
        </div>
      </div>
    </div>
  </article>
</template>

<script setup lang="ts">
defineProps<{
  article: {
    id: string
    title: string
    slug: string
    excerpt: string | null
    cover_image_url: string | null
    reading_time: number
    views_count: number
    author: { name: string }
    category: { name: string; slug: string; color?: string } | null
  }
}>()
</script>
