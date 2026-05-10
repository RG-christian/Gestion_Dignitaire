import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any,
    token: null as string | null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userName: (state) => state.user?.nom_complet || '',
  },

  actions: {
    async login(credentials: { username: string; password: string }) {
      const api = useApi()
      try {
        const response = await api.login(credentials)
        this.token = response.token
        this.user = response.user
        
        // Sauvegarder dans localStorage
        if (process.client) {
          localStorage.setItem('auth_token', response.token)
          localStorage.setItem('user', JSON.stringify(response.user))
        }
        
        return true
      } catch (error) {
        console.error('Erreur de connexion:', error)
        return false
      }
    },

    async logout() {
      const api = useApi()
      try {
        await api.logout()
      } catch (error) {
        console.error('Erreur de déconnexion:', error)
      } finally {
        this.token = null
        this.user = null
        
        if (process.client) {
          localStorage.removeItem('auth_token')
          localStorage.removeItem('user')
        }
        
        navigateTo('/login')
      }
    },

    async loadFromStorage() {
      if (process.client) {
        const token = localStorage.getItem('auth_token')
        const user = localStorage.getItem('user')
        
        if (token && user) {
          this.token = token
          this.user = JSON.parse(user)
        }
      }
    },

    async fetchUser() {
      if (!this.token) return
      
      const api = useApi()
      try {
        this.user = await api.getUser()
      } catch (error) {
        console.error('Erreur lors de la récupération de l\'utilisateur:', error)
        this.logout()
      }
    }
  }
})
