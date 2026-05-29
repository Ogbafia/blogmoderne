<template>
  <div class="min-h-screen flex items-center justify-center bg-background px-4 py-12">
    <div class="max-w-md w-full">

      <!-- Logo / Titre -->
      <div class="text-center mb-8">
        <NuxtLink to="/" class="inline-block font-bold text-2xl text-on-surface mb-4">
          BlogModerne
        </NuxtLink>
        <h1 class="text-2xl font-bold text-on-surface">Créer un compte 🚀</h1>
        <p class="text-secondary mt-2">Rejoignez la communauté BlogModerne</p>
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

        <!-- Message de succès -->
        <div
          v-if="successMsg"
          class="mb-5 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm"
        >
          <span class="material-symbols-outlined text-[18px] flex-shrink-0">check_circle</span>
          <p>{{ successMsg }}</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-5">

          <!-- Nom complet -->
          <div>
            <label class="block text-sm font-bold text-on-surface mb-2">Nom complet</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Jean Dupont"
              autocomplete="name"
              :class="fieldClass('name')"
              required
            />
            <p v-if="fieldErrors.name" class="mt-1 text-xs text-error">{{ fieldErrors.name }}</p>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-bold text-on-surface mb-2">Adresse email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="nom@exemple.com"
              autocomplete="email"
              :class="fieldClass('email')"
              required
            />
            <p v-if="fieldErrors.email" class="mt-1 text-xs text-error">{{ fieldErrors.email }}</p>
          </div>

          <!-- Mot de passe -->
          <div>
            <label class="block text-sm font-bold text-on-surface mb-2">Mot de passe</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                autocomplete="new-password"
                :class="fieldClass('password') + ' pr-10'"
                required
                @input="checkPasswordStrength"
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
            <!-- Indicateur de force -->
            <div v-if="form.password" class="mt-2 space-y-1">
              <div class="flex gap-1">
                <div
                  v-for="i in 4"
                  :key="i"
                  class="h-1 flex-1 rounded-full transition-colors"
                  :class="i <= passwordStrength ? strengthColors[passwordStrength - 1] : 'bg-outline-variant'"
                ></div>
              </div>
              <p class="text-xs" :class="strengthTextColors[passwordStrength - 1] || 'text-secondary'">
                {{ strengthLabels[passwordStrength - 1] || '' }}
              </p>
            </div>
            <!-- Exigences -->
            <ul class="mt-2 space-y-1">
              <li
                v-for="req in passwordRequirements"
                :key="req.label"
                class="flex items-center gap-1.5 text-xs"
                :class="req.met ? 'text-green-600' : 'text-secondary'"
              >
                <span class="material-symbols-outlined text-[14px]">
                  {{ req.met ? 'check_circle' : 'radio_button_unchecked' }}
                </span>
                {{ req.label }}
              </li>
            </ul>
            <p v-if="fieldErrors.password" class="mt-1 text-xs text-error">{{ fieldErrors.password }}</p>
          </div>

          <!-- Confirmer le mot de passe -->
          <div>
            <label class="block text-sm font-bold text-on-surface mb-2">Confirmer le mot de passe</label>
            <div class="relative">
              <input
                v-model="form.password_confirmation"
                :type="showConfirm ? 'text' : 'password'"
                placeholder="••••••••"
                autocomplete="new-password"
                :class="fieldClass('password_confirmation') + ' pr-10'"
                required
              />
              <button
                type="button"
                @click="showConfirm = !showConfirm"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-primary transition-colors"
              >
                <span class="material-symbols-outlined text-[18px]">
                  {{ showConfirm ? 'visibility_off' : 'visibility' }}
                </span>
              </button>
            </div>
            <!-- Vérification en temps réel -->
            <p
              v-if="form.password_confirmation && form.password !== form.password_confirmation"
              class="mt-1 text-xs text-error flex items-center gap-1"
            >
              <span class="material-symbols-outlined text-[14px]">error</span>
              Les mots de passe ne correspondent pas
            </p>
            <p
              v-if="form.password_confirmation && form.password === form.password_confirmation"
              class="mt-1 text-xs text-green-600 flex items-center gap-1"
            >
              <span class="material-symbols-outlined text-[14px]">check_circle</span>
              Les mots de passe correspondent
            </p>
            <p v-if="fieldErrors.password_confirmation" class="mt-1 text-xs text-error">{{ fieldErrors.password_confirmation }}</p>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="loading || !canSubmit"
            class="w-full bg-primary text-on-primary font-bold py-3 rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center justify-center gap-2"
          >
            <span v-if="loading" class="material-symbols-outlined animate-spin text-[18px]">refresh</span>
            {{ loading ? 'Création du compte…' : 'Créer mon compte' }}
          </button>
        </form>
      </div>

      <!-- Lien connexion -->
      <p class="text-center mt-6 text-sm text-secondary">
        Déjà un compte ?
        <NuxtLink to="/login" class="text-primary font-bold hover:underline ml-1">Se connecter</NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

definePageMeta({ layout: false })

const authStore = useAuthStore()

const loading       = ref(false)
const showPassword  = ref(false)
const showConfirm   = ref(false)
const errorMsg      = ref('')
const successMsg    = ref('')
const fieldErrors   = reactive<Record<string, string>>({})
const passwordStrength = ref(0)

const form = reactive({
  name:                  '',
  email:                 '',
  password:              '',
  password_confirmation: '',
})

// ── Force du mot de passe ──────────────────────────────────────────
const passwordRequirements = computed(() => [
  { label: 'Au moins 8 caractères',     met: form.password.length >= 8 },
  { label: 'Une lettre majuscule',       met: /[A-Z]/.test(form.password) },
  { label: 'Une lettre minuscule',       met: /[a-z]/.test(form.password) },
  { label: 'Un chiffre',                 met: /[0-9]/.test(form.password) },
])

const strengthColors     = ['bg-error', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500']
const strengthTextColors = ['text-error', 'text-orange-500', 'text-yellow-600', 'text-green-600']
const strengthLabels     = ['Très faible', 'Faible', 'Moyen', 'Fort']

function checkPasswordStrength() {
  const metCount = passwordRequirements.value.filter(r => r.met).length
  passwordStrength.value = metCount
}

const canSubmit = computed(() =>
  form.name.trim().length > 0 &&
  form.email.trim().length > 0 &&
  form.password.length >= 8 &&
  form.password === form.password_confirmation
)

// ── Styles des champs ──────────────────────────────────────────────
function fieldClass(field: string) {
  const base = 'w-full px-4 py-2.5 rounded-lg border bg-surface outline-none transition-all text-sm '
  return base + (fieldErrors[field]
    ? 'border-error focus:ring-2 focus:ring-error/20'
    : 'border-outline-variant focus:ring-2 focus:ring-primary/20 focus:border-primary')
}

// ── Soumission ─────────────────────────────────────────────────────
async function handleRegister() {
  loading.value  = true
  errorMsg.value = ''
  successMsg.value = ''
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])

  try {
    await authStore.register(
      form.name,
      form.email,
      form.password,
      form.password_confirmation
    )
    successMsg.value = 'Compte créé ! Redirection…'
    await navigateTo('/')
  } catch (e: any) {
    const data = e?.data ?? e
    if (data?.errors) {
      // Erreurs de validation Laravel (422)
      Object.assign(
        fieldErrors,
        Object.fromEntries(
          Object.entries(data.errors).map(([k, v]) => [k, (v as string[])[0]])
        )
      )
      errorMsg.value = 'Veuillez corriger les erreurs ci-dessous.'
    } else if (data?.message) {
      errorMsg.value = data.message
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
