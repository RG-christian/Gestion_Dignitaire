import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any,
    token: null as string | null,
    tokenExpiry: null as string | null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userName: (state) => state.user?.nom_complet || '',
    isTokenValid: (state) => {
      if (!state.tokenExpiry) return !!state.token
      return new Date(state.tokenExpiry) > new Date()
    }
  },

  actions: {
    async login(credentials: { username: string; password: string }) {
      const api = useApi()
      try {
        const response = await api.login(credentials)
        this.token = response.token
        this.user = response.user
        this.tokenExpiry = response.expires_at
        
        // Sauvegarder dans localStorage
        if (process.client) {
          localStorage.setItem('auth_token', response.token)
          localStorage.setItem('user', JSON.stringify(response.user))
          localStorage.setItem('token_expiry', response.expires_at)
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
        if (this.token) {
          await api.logout()
        }
      } catch (error) {
        console.error('Erreur de déconnexion:', error)
      } finally {
        this.token = null
        this.user = null
        this.tokenExpiry = null
        
        if (process.client) {
          localStorage.removeItem('auth_token')
          localStorage.removeItem('user')
          localStorage.removeItem('token_expiry')
        }
        
        navigateTo('/login')
      }
    },

    async loadFromStorage() {
      if (process.client) {
        const token = localStorage.getItem('auth_token')
        const user = localStorage.getItem('user')
        const tokenExpiry = localStorage.getItem('token_expiry')
        
        if (token && user) {
          // Vérifier si le token n'est pas expiré
          if (tokenExpiry) {
            const expiryDate = new Date(tokenExpiry)
            if (expiryDate <= new Date()) {
              // Token expiré, nettoyer
              this.logout()
              return
            }
          }
          
          this.token = token
          this.user = JSON.parse(user)
          this.tokenExpiry = tokenExpiry
          
          // Vérifier que le token est toujours valide côté serveur
          await this.fetchUser()
        }
      }
    },

    async fetchUser() {
      if (!this.token) return
      
      const api = useApi()
      try {
        this.user = await api.getUser()
        // Mettre à jour l'utilisateur dans localStorage
        if (process.client) {
          localStorage.setItem('user', JSON.stringify(this.user))
        }
      } catch (error) {
        console.error('Erreur lors de la récupération de l\'utilisateur:', error)
        this.logout()
      }
    }
  }
})
