export default defineNuxtPlugin(() => {
  const authStore = useAuthStore()
  
  // Charger le token depuis localStorage au démarrage (sans await)
  if (process.client) {
    const token = localStorage.getItem('auth_token')
    const user = localStorage.getItem('user')
    const tokenExpiry = localStorage.getItem('token_expiry')
    
    if (token && user) {
      authStore.token = token
      authStore.user = JSON.parse(user)
      authStore.tokenExpiry = tokenExpiry
    }
  }
})
