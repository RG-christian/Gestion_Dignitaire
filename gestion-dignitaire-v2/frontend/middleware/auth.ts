export default defineNuxtRouteMiddleware(async (to, from) => {
  const authStore = useAuthStore()
  
  // Si on est côté client et qu'il n'y a pas de token en mémoire
  if (process.client && !authStore.token) {
    // Essayer de charger depuis localStorage
    const token = localStorage.getItem('auth_token')
    const user = localStorage.getItem('user')
    const tokenExpiry = localStorage.getItem('token_expiry')
    
    if (token && user) {
      // Vérifier l'expiration
      if (tokenExpiry) {
        const expiryDate = new Date(tokenExpiry)
        if (expiryDate > new Date()) {
          // Token valide, le charger directement
          authStore.token = token
          authStore.user = JSON.parse(user)
          authStore.tokenExpiry = tokenExpiry
          return // Autoriser la navigation
        }
      } else {
        // Pas d'expiration définie, charger quand même
        authStore.token = token
        authStore.user = JSON.parse(user)
        return // Autoriser la navigation
      }
    }
  }
  
  // Vérifier si l'utilisateur est authentifié
  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }
})
