<template>
  <div class="p-margin-desktop max-w-container-max mx-auto w-full">
    <div class="flex justify-between items-end mb-8">
      <div>
        <h2 class="font-headline-lg text-headline-lg text-on-background">Aperçu Global</h2>
        <p class="text-secondary font-body-md text-body-md mt-1">Bienvenue dans votre centre de contrôle éditorial.</p>
      </div>
      <div class="flex gap-3">
        <button class="bg-white border border-surface-container-high text-on-background px-4 py-2 rounded-lg font-label-md text-label-md hover:bg-surface-container-low transition-colors flex items-center gap-2">
          <span class="material-symbols-outlined text-[18px]">upload</span>
          Uploader Média
        </button>
        <button class="bg-primary text-on-primary px-4 py-2 rounded-lg font-label-md text-label-md hover:opacity-90 transition-opacity flex items-center gap-2 shadow-sm">
          <span class="material-symbols-outlined text-[18px]">add</span>
          Nouvel Article
        </button>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter mb-10">
      <div v-for="stat in transformedStats" :key="stat.label" class="bg-white border border-surface-container-high p-6 rounded-xl hover:border-primary/30 transition-colors">
        <div class="flex justify-between items-start mb-4">
          <div class="p-2 bg-surface-container-low rounded-lg text-primary">
            <span class="material-symbols-outlined">{{ stat.icon }}</span>
          </div>
          <span :class="stat.trendClass" class="font-label-sm text-label-sm">{{ stat.trend }}</span>
        </div>
        <p class="text-secondary font-label-md text-label-md mb-1">{{ stat.label }}</p>
        <h3 class="font-headline-md text-headline-md text-on-surface">{{ stat.value }}</h3>
      </div>
    </div>

    <!-- Main Dashboard Layout: Recent Articles & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
      <!-- Recent Articles Table -->
      <div class="lg:col-span-2 bg-white border border-surface-container-high rounded-xl overflow-hidden">
        <div class="p-6 border-b border-surface-container-high flex justify-between items-center">
          <h3 class="font-headline-md text-headline-md text-on-surface">Articles Récents</h3>
          <NuxtLink to="/admin/articles" class="text-primary font-label-md text-label-md hover:underline">Voir tout</NuxtLink>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-surface-container-low text-secondary font-label-sm text-label-sm uppercase tracking-wider">
                <th class="px-6 py-4">Titre</th>
                <th class="px-6 py-4">Statut</th>
                <th class="px-6 py-4">Vues</th>
                <th class="px-6 py-4">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-high">
              <tr v-for="article in stats?.top_articles" :key="article.id" class="hover:bg-surface-container-lowest transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-surface-container-high overflow-hidden flex-shrink-0">
                      <img v-if="article.cover_image_url" :src="article.cover_image_url" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[20px]">image</span>
                      </div>
                    </div>
                    <span class="font-label-md text-label-md text-on-surface font-semibold max-w-[200px] truncate">{{ article.title }}</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 bg-green-100 text-green-700 text-[11px] font-bold rounded uppercase">Publié</span>
                </td>
                <td class="px-6 py-4 text-secondary font-body-md text-body-md">{{ article.views_count }}</td>
                <td class="px-6 py-4">
                  <button class="text-secondary hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                  </button>
                </td>
              </tr>
              <tr v-if="!stats?.top_articles?.length">
                <td colspan="4" class="px-6 py-8 text-center text-secondary font-body-md text-body-md">Aucun article trouvé.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Right Sidebar Content: Quick Actions & Notifications -->
      <div class="space-y-6">
        <!-- Analytics Summary Card -->
        <div class="bg-primary text-on-primary p-8 rounded-xl relative overflow-hidden shadow-lg">
          <div class="relative z-10">
            <h4 class="font-headline-md text-headline-md mb-2">Performance du Mois</h4>
            <p class="font-body-md text-body-md opacity-90 mb-6">Votre audience a augmenté de 15% par rapport au mois dernier.</p>
            <div class="h-24 w-full flex items-end gap-1">
              <div class="bg-on-primary/30 w-full h-[40%] rounded-t"></div>
              <div class="bg-on-primary/30 w-full h-[60%] rounded-t"></div>
              <div class="bg-on-primary/30 w-full h-[30%] rounded-t"></div>
              <div class="bg-on-primary/50 w-full h-[80%] rounded-t"></div>
              <div class="bg-on-primary/30 w-full h-[50%] rounded-t"></div>
              <div class="bg-on-primary w-full h-[100%] rounded-t shadow-[0_-4px_10px_rgba(255,255,255,0.2)]"></div>
            </div>
          </div>
          <!-- Abstract Background Decoration -->
          <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Categories / Quick Filters -->
        <div class="bg-white border border-surface-container-high p-6 rounded-xl">
          <h4 class="font-label-md text-label-md font-bold mb-4 uppercase tracking-wider text-secondary">Catégories Rapides</h4>
          <div class="flex flex-wrap gap-2">
            <span class="px-3 py-1 bg-surface-container-low text-on-surface font-label-sm text-label-sm rounded-full border border-surface-container-high">Technologie</span>
            <span class="px-3 py-1 bg-primary text-on-primary font-label-sm text-label-sm rounded-full border border-primary">Design UI/UX</span>
            <span class="px-3 py-1 bg-surface-container-low text-on-surface font-label-sm text-label-sm rounded-full border border-surface-container-high">Web Dev</span>
          </div>
        </div>

        <!-- Help / Support CTA -->
        <div class="bg-surface-container-low border border-dashed border-outline p-6 rounded-xl text-center">
          <span class="material-symbols-outlined text-primary mb-2 text-[32px]">help_outline</span>
          <h4 class="font-label-md text-label-md font-bold text-on-surface">Besoin d'aide ?</h4>
          <p class="font-body-md text-body-md text-secondary mb-4">Consultez notre documentation complète.</p>
          <a href="#" class="text-primary font-bold font-label-md text-label-md hover:underline inline-flex items-center gap-1">
            Documentation
            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin' })

const api = useApi()
const { data: statsData } = await useAsyncData('admin-stats', () => api.get<any>('/admin/stats'))
const stats = computed(() => statsData.value?.stats ?? {})

const transformedStats = computed(() => [
  { label: 'Total Articles', value: stats.value.articles ?? 0, icon: 'article', trend: '+12%', trendClass: 'text-green-600' },
  { label: 'Total Vues', value: '1.2k', icon: 'visibility', trend: '+24%', trendClass: 'text-green-600' },
  { label: 'Commentaires', value: stats.value.comments ?? 0, icon: 'forum', trend: '-3%', trendClass: 'text-error' },
  { label: 'Utilisateurs', value: stats.value.users ?? 0, icon: 'person_add', trend: '+18%', trendClass: 'text-green-600' },
])

const formatDate = (d: string | null) =>
  d ? new Intl.DateTimeFormat('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(d)) : ''
</script>
