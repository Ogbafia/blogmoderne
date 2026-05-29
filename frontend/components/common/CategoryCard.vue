<template>
  <router-link :to="`/categories/${category.slug}`" class="block">
    <div
      class="rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-shadow duration-300"
      :style="{ backgroundColor: category.color || '#f5f5f5' }"
    >
      <div class="p-6 text-center">
        <h2 class="text-xl font-semibold" style="color:#b80035;">
          {{ category.name }}
        </h2>
      </div>
    </div>
  </router-link>
</template>

<script setup lang="ts">
defineProps<{
  category: {
    id: string;
    name: string;
    slug: string;
    color?: string;
  };
}>();
</script>

<style scoped>
/* Subtle hover scaling */
a:hover .rounded-xl {
  transform: scale(1.03);
}
</style>
