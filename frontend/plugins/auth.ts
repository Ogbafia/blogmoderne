export default defineNuxtPlugin(async (nuxtApp) => {
  const authStore = useAuthStore()
  
  // Appeler l'init sur le serveur et le client
  await authStore.init()
})
