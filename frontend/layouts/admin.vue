<template>
  <div class="bg-background text-on-background antialiased flex min-h-screen font-body-md">
    <!-- SideNavBar (Shared Component) -->
    <aside class="fixed left-0 top-0 h-full flex flex-col py-gutter border-r border-surface-container-high dark:border-outline-variant bg-surface-container-lowest dark:bg-surface w-64 z-40">
      <div class="px-6 mb-10 flex flex-col gap-1">
        <h1 class="font-headline-md text-headline-md font-bold text-primary">Admin Blog</h1>
        <p class="font-label-md text-label-md text-secondary">Édition Premium</p>
      </div>
      <nav class="flex-1 px-4 space-y-1">
        <NuxtLink 
          v-for="item in menu" 
          :key="item.to"
          :to="item.to"
          class="flex items-center gap-3 px-4 py-3 rounded-lg font-label-md text-label-md transition-all active:translate-x-1"
          :class="[route.path === item.to ? 'text-primary bg-surface-container-low font-bold' : 'text-secondary hover:bg-surface-container-low']"
        >
          <span class="material-symbols-outlined">{{ item.icon }}</span>
          <span>{{ item.label }}</span>
        </NuxtLink>
      </nav>
      <div class="mt-auto px-4 space-y-1 border-t border-surface-container-high pt-4">
        <a href="#" class="flex items-center gap-3 px-4 py-3 text-secondary hover:bg-surface-container-low transition-all font-label-md text-label-md rounded-lg">
          <span class="material-symbols-outlined">help</span>
          <span>Aide</span>
        </a>
        <button @click="authStore.logout()" class="flex w-full items-center gap-3 px-4 py-3 text-secondary hover:bg-surface-container-low transition-all font-label-md text-label-md rounded-lg">
          <span class="material-symbols-outlined">logout</span>
          <span>Déconnexion</span>
        </button>
      </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="ml-64 flex-1 flex flex-col min-h-screen">
      <!-- TopNavBar (Shared Component) -->
      <header class="sticky top-0 z-30 flex justify-between items-center w-full px-margin-desktop h-16 bg-surface dark:bg-surface border-b border-surface-container-high dark:border-outline-variant">
        <div class="flex items-center gap-4">
          <div class="relative group">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-on-surface-variant">
              <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <input 
              type="text" 
              placeholder="Rechercher..." 
              class="bg-surface-container-low border-none rounded-lg pl-10 pr-4 py-1.5 font-body-md text-body-md w-64 focus:ring-2 focus:ring-primary/20 transition-all outline-none"
            />
          </div>
        </div>
        <div class="flex items-center gap-6">
          <NuxtLink to="/" class="text-on-surface-variant hover:bg-surface-container-low p-2 rounded-full transition-colors flex items-center justify-center" title="Retour au site">
            <span class="material-symbols-outlined text-[20px]">public</span>
          </NuxtLink>
          <button class="text-on-surface-variant hover:bg-surface-container-low p-2 rounded-full transition-colors relative">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full"></span>
          </button>
          <div class="flex items-center gap-3 pl-4 border-l border-surface-container-high">
            <div class="text-right hidden sm:block">
              <p class="font-label-md text-label-md font-bold text-on-surface">{{ authStore.displayName }}</p>
              <p class="font-label-sm text-label-sm text-secondary">Administrateur</p>
            </div>
            <div class="w-10 h-10 rounded-full border border-surface-container-high overflow-hidden bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-bold">
              <img v-if="authStore.user?.avatar" :src="authStore.user.avatar" class="w-full h-full object-cover" />
              <span v-else>{{ authStore.displayName.charAt(0) }}</span>
            </div>
          </div>
        </div>
      </header>

      <!-- Dashboard Content -->
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const route = useRoute()

// Map the old icons to material-symbols
const menu = [
  { label: 'Tableau de bord', icon: 'dashboard', to: '/admin' },
  { label: 'Articles',        icon: 'article', to: '/admin/articles' },
  { label: 'Catégories',      icon: 'category', to: '/admin/categories' },
  { label: 'Médias',          icon: 'perm_media', to: '/admin/media' },
  { label: 'Commentaires',    icon: 'comment', to: '/admin/comments' },
  { label: 'Newsletter',      icon: 'forward_to_inbox', to: '/admin/newsletter' },
  { label: 'Utilisateurs',    icon: 'group', to: '/admin/users' },
]
</script>
