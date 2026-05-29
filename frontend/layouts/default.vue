<template>
  <div class="min-h-screen bg-background text-on-surface selection:bg-primary-fixed selection:text-on-primary-fixed font-body-md">
    <!-- TopNavBar -->
    <header class="sticky top-0 z-50 flex items-center justify-between px-6 h-16 w-full max-w-[1200px] mx-auto bg-surface/80 dark:bg-background/80 backdrop-blur-md border-b border-outline-variant dark:border-outline">
      <div class="flex items-center gap-8">
        <NuxtLink to="/" class="font-headline-md text-headline-md font-bold text-on-surface dark:text-on-surface">BlogModerne</NuxtLink>
        <nav class="hidden md:flex items-center gap-6">
          <!-- Home Link -->
          <NuxtLink
            to="/"
            :class="[
              'font-body-md text-body-md font-bold pb-1 transition-all border-b-2',
              route.path === '/'
                ? 'text-primary dark:text-primary-fixed border-primary'
                : 'text-secondary dark:text-secondary-fixed-dim border-transparent hover:text-primary dark:hover:text-primary-fixed'
            ]"
          >
            Home
          </NuxtLink>

          <!-- Articles Link -->
          <NuxtLink
            to="/articles"
            :class="[
              'font-body-md text-body-md font-bold pb-1 transition-all border-b-2',
              route.path.startsWith('/articles')
                ? 'text-primary dark:text-primary-fixed border-primary'
                : 'text-secondary dark:text-secondary-fixed-dim border-transparent hover:text-primary dark:hover:text-primary-fixed'
            ]"
          >
            Articles
          </NuxtLink>

          <!-- Categories Link -->
          <NuxtLink
            to="/categories"
            :class="[
              'font-body-md text-body-md font-bold pb-1 transition-all border-b-2',
              route.path.startsWith('/categories')
                ? 'text-primary dark:text-primary-fixed border-primary'
                : 'text-secondary dark:text-secondary-fixed-dim border-transparent hover:text-primary dark:hover:text-primary-fixed'
            ]"
          >
            Categories
          </NuxtLink>
        </nav>
      </div>
      <div class="flex items-center gap-4">
        <template v-if="authStore.isAuthenticated">
          <NuxtLink v-if="authStore.isAuthor" to="/admin" class="font-body-sm text-body-sm text-secondary hover:text-primary transition-colors hidden md:block">Admin</NuxtLink>
          <button @click="authStore.logout()" class="font-body-sm text-body-sm text-secondary px-4 py-2 hover:text-primary transition-colors active:scale-95 duration-100">Logout</button>
        </template>
        <template v-else>
          <NuxtLink to="/login" class="font-body-sm text-body-sm text-secondary px-4 py-2 hover:text-primary transition-colors active:scale-95 duration-100">Login</NuxtLink>
          <NuxtLink to="/register" class="font-body-sm text-body-sm bg-primary text-on-primary px-5 py-2 rounded font-medium hover:opacity-90 active:scale-95 transition-all duration-100">Sign Up</NuxtLink>
        </template>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-[1200px] mx-auto px-6">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="w-full bg-surface-container-lowest dark:bg-on-secondary-fixed border-t border-outline-variant dark:border-outline mt-16">
      <div class="w-full py-stack-lg px-6 flex flex-col md:flex-row justify-between items-center max-w-[1200px] mx-auto gap-8">
        <div class="flex flex-col gap-2 items-center md:items-start">
          <span class="font-headline-md text-headline-md font-bold text-on-surface dark:text-on-surface">BlogModerne</span>
          <p class="font-body-sm text-body-sm text-secondary dark:text-secondary-fixed-dim">© {{ new Date().getFullYear() }} BlogModerne. All rights reserved.</p>
        </div>
        <nav class="flex flex-wrap justify-center gap-6 md:gap-10">
          <NuxtLink to="/privacy-policy" class="font-body-sm text-body-sm text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-colors opacity-80 hover:opacity-100">Privacy Policy</NuxtLink>
          <NuxtLink to="/terms-of-service" class="font-body-sm text-body-sm text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-colors opacity-80 hover:opacity-100">Terms of Service</NuxtLink>
          <NuxtLink to="/about" class="font-body-sm text-body-sm text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-colors opacity-80 hover:opacity-100">About</NuxtLink>
          <NuxtLink to="/contact" class="font-body-sm text-body-sm text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-colors opacity-80 hover:opacity-100">Contact</NuxtLink>
        </nav>
        <div class="flex gap-4">
          <a href="#" class="w-10 h-10 rounded-full border border-outline-variant flex items-center justify-center text-secondary hover:border-primary hover:text-primary transition-all">
            <span class="material-symbols-outlined text-[20px]">public</span>
          </a>
          <a href="#" class="w-10 h-10 rounded-full border border-outline-variant flex items-center justify-center text-secondary hover:border-primary hover:text-primary transition-all">
            <span class="material-symbols-outlined text-[20px]">code</span>
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { useHead } from '#app'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const route = useRoute()

useHead({
  link: [
    {
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap'
    }
  ],
  style: [
    {
      children: ':root { --font-sans: "Inter", system-ui, sans-serif; }'
    }
  ]
})
</script>
