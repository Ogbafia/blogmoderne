<template>
  <section class="bg-yellow-300 py-12">
    <div class="max-w-4xl mx-auto px-4 text-center">
      <h2 class="text-3xl font-display font-bold text-dark-900 mb-4">Rejoignez le club</h2>
      <p class="text-dark-900/70 mb-8 max-w-md mx-auto">
        Recevez une fois par semaine une sélection des meilleurs articles et actualités tech.
      </p>
      <form @submit.prevent="subscribe" class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
        <input 
          v-model="email"
          type="email" 
          placeholder="Votre email" 
          class="flex-1 px-4 py-3 rounded-lg outline-none focus:ring-2 focus:ring-dark-900/10 placeholder:text-dark-900/30"
          required
        />
        <button 
          type="submit" 
          :disabled="loading"
          class="bg-dark-900 text-white font-bold px-8 py-3 rounded-lg hover:bg-dark-800 transition-colors disabled:opacity-50"
        >
          {{ loading ? 'Inscription...' : "S'abonner" }}
        </button>
      </form>
      <p v-if="message" class="mt-4 text-sm font-semibold text-dark-900">{{ message }}</p>
    </div>
  </section>
</template>

<script setup lang="ts">
const email = ref('')
const loading = ref(false)
const message = ref('')

async function subscribe() {
  loading.value = true
  message.value = ''
  try {
    const api = useApi()
    await api.post('/newsletter/subscribe', { email: email.value })
    message.value = 'Merci ! Votre inscription est validée.'
    email.value = ''
  } catch (e: any) {
    message.value = "Une erreur est survenue. Vérifiez votre email."
  } finally {
    loading.value = false
  }
}
</script>
