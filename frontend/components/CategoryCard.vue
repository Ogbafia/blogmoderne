<template>
  <NuxtLink :to="`/categories/${category.slug}`" class="block p-4 rounded-lg border border-outline-variant hover:shadow-lg hover:scale-105 hover:-translate-y-1 transition-all duration-300" :style="{ backgroundColor: category.color ? category.color + '20' : '#f5f5f5' }">
    <div class="flex items-center justify-between">
      <h2 class="font-headline-md text-on-surface" v-text="category.name"></h2>
      <span class="material-symbols-outlined text-[20px] text-primary">arrow_forward</span>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
interface Category {
  id: number;
  name: string;
  slug: string;
  color?: string;
}
const props = defineProps<{ category: Category }>();
</script>

<style scoped>
/* No extra CSS needed – Tailwind handles layout */
</style>
