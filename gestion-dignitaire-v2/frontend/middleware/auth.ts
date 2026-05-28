export default defineNuxtRouteMiddleware(async (to, from) => {
  // Le middleware ne s'exécute que côté client pour éviter les problèmes SSR
  if (process.server) {
    return
  }
  
  const authStore = useAuthStore()
  
  // Essayer de charger depuis localStorage si pas déjà en mémoire
  if (!authStore.token) {
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
          // Autoriser la navigation
          return
        } else {
          // Token expiré, nettoyer le localStorage
          localStorage.removeItem('auth_token')
          localStorage.removeItem('user')
          localStorage.removeItem('token_expiry')
        }
      } else {
        // Pas d'expiration définie, charger quand même
        authStore.token = token
        authStore.user = JSON.parse(user)
        // Autoriser la navigation
        return
      }
    }
  }
  
  // Vérifier si l'utilisateur est authentifié
  if (!authStore.isAuthenticated) {
    // Sauvegarder l'URL de destination pour y revenir après connexion
    if (to.path !== '/login') {
      return navigateTo({
        path: '/login',
        query: { redirect: to.fullPath }
      })
    }
    return navigateTo('/login')
  }
})
