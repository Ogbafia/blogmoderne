<template>
  <div class="min-h-screen flex items-center justify-center bg-background px-4 py-12">
    <div class="max-w-md w-full">

      <!-- Logo / Titre -->
      <div class="text-center mb-8">
        <NuxtLink to="/" class="inline-block font-bold text-2xl text-on-surface mb-4">
          BlogModerne
        </NuxtLink>
        <h1 class="text-2xl font-bold text-on-surface">Content de vous revoir 👋</h1>
        <p class="text-secondary mt-2">Connectez-vous à votre compte</p>
      </div>

      <!-- Card -->
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-8 shadow-sm">

        <!-- Message d'erreur global -->
        <div
          v-if="errorMsg"
          class="mb-5 flex items-start gap-3 p-4 bg-error/10 border border-error/30 rounded-xl text-error text-sm"
        >
          <span class="material-symbols-outlined text-[18px] mt-0.5 flex-shrink-0">error</span>
          <p>{{ errorMsg }}</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">

          <!-- Email -->
          <div>
            <label class="block text-sm font-bold text-on-surface mb-2">
              Adresse email
            </label>
            <input
              v-model="form.email"
              type="email"
              placeholder="nom@exemple.com"
              autocomplete="email"
              :class="[
                'w-full px-4 py-2.5 rounded-lg border bg-surface outline-none transition-all text-sm',
                fieldErrors.email
                  ? 'border-error focus:ring-2 focus:ring-error/20'
                  : 'border-outline-variant focus:ring-2 focus:ring-primary/20 focus:border-primary'
              ]"
              required
            />
            <p v-if="fieldErrors.email" class="mt-1 text-xs text-error">{{ fieldErrors.email }}</p>
          </div>

          <!-- Mot de passe -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="block text-sm font-bold text-on-surface">Mot de passe</label>
            </div>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                autocomplete="current-password"
                :class="[
                  'w-full px-4 py-2.5 pr-10 rounded-lg border bg-surface outline-none transition-all text-sm',
                  fieldErrors.password
                    ? 'border-error focus:ring-2 focus:ring-error/20'
                    : 'border-outline-variant focus:ring-2 focus:ring-primary/20 focus:border-primary'
                ]"
                required
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-primary transition-colors"
              >
                <span class="material-symbols-outlined text-[18px]">
                  {{ showPassword ? 'visibility_off' : 'visibility' }}
                </span>
              </button>
            </div>
            <p v-if="fieldErrors.password" class="mt-1 text-xs text-error">{{ fieldErrors.password }}</p>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-primary text-on-primary font-bold py-3 rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center justify-center gap-2"
          >
            <span v-if="loading" class="material-symbols-outlined animate-spin text-[18px]">refresh</span>
            {{ loading ? 'Connexion…' : 'Se connecter' }}
          </button>
        </form>

        <!-- Séparateur -->
        <div class="my-6 flex items-center gap-3">
          <div class="flex-1 h-px bg-outline-variant"></div>
          <span class="text-xs text-secondary">Comptes de test</span>
          <div class="flex-1 h-px bg-outline-variant"></div>
        </div>

        <!-- Comptes de test rapide -->
        <div class="grid grid-cols-1 gap-2">
          <button
            v-for="account in testAccounts"
            :key="account.email"
            type="button"
            @click="fillTestAccount(account)"
            class="flex items-center justify-between px-4 py-2.5 rounded-lg border border-outline-variant hover:border-primary hover:bg-primary/5 transition-all text-sm group"
          >
            <div class="text-left">
              <p class="font-bold text-on-surface group-hover:text-primary">{{ account.label }}</p>
              <p class="text-secondary text-xs">{{ account.email }}</p>
            </div>
            <span class="material-symbols-outlined text-[16px] text-secondary group-hover:text-primary group-hover:translate-x-1 transition-transform">login</span>
          </button>
        </div>
      </div>

      <!-- Lien inscription -->
      <p class="text-center mt-6 text-sm text-secondary">
        Pas encore de compte ?
        <NuxtLink to="/register" class="text-primary font-bold hover:underline ml-1">S'inscrire</NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })

const authStore = useAuthStore()

const loading      = ref(false)
const showPassword = ref(false)
const errorMsg     = ref('')
const fieldErrors  = reactive<Record<string, string>>({})

const form = reactive({
  email:    '',
  password: '',
})

// Comptes de test pré-remplis
const testAccounts = [
  { label: 'Admin',    email: 'admin@blogmoderne.fr',  password: 'password', role: '👑 Admin' },
  { label: 'Auteur',   email: 'sophie@blogmoderne.fr', password: 'password', role: '✍️ Auteur' },
  { label: 'Auteur 2', email: 'thomas@blogmoderne.fr', password: 'password', role: '✍️ Auteur' },
]

function fillTestAccount(account: { email: string; password: string }) {
  form.email    = account.email
  form.password = account.password
  errorMsg.value = ''
}

async function handleLogin() {
  loading.value  = true
  errorMsg.value = ''
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])

  try {
    await authStore.login(form.email, form.password)
    await navigateTo(authStore.isAdmin ? '/admin' : '/')
  } catch (e: any) {
    // Extraire le message d'erreur Laravel
    const data = e?.data ?? e
    if (data?.message) {
      errorMsg.value = data.message
    } else if (data?.errors) {
      Object.assign(fieldErrors, Object.fromEntries(
        Object.entries(data.errors).map(([k, v]) => [k, (v as string[])[0]])
      ))
    } else if (typeof data === 'string') {
      errorMsg.value = data
    } else {
      errorMsg.value = 'Une erreur est survenue. Veuillez réessayer.'
    }
  } finally {
    loading.value = false
  }
}
</script>
