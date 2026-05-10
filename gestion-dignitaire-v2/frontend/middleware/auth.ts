export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()
  
  // Charger le token depuis le localStorage si disponible
  if (process.client && !authStore.token) {
    authStore.loadFromStorage()
  }
  
  // Vérifier si l'utilisateur est authentifié
  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }
})
