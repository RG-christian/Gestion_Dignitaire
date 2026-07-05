export const useApi = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const apiFetch = $fetch.create({
    baseURL: config.public.apiBase as string,
    credentials: 'include',
    
    onRequest({ options }) {
      const token = authStore.token
      if (token) {
        // Ne pas définir Content-Type pour FormData (le navigateur le fait automatiquement)
        const headers: any = {
          ...options.headers,
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
        
        // Ajouter Content-Type seulement si ce n'est pas un FormData
        if (!(options.body instanceof FormData)) {
          headers['Content-Type'] = 'application/json'
        }
        
        options.headers = headers
      }
    },

    onResponse({ response }) {
      // Vérifier si le token est toujours valide
      if (response.status === 200 && response._data?.token) {
        // Mettre à jour le token si un nouveau est fourni
        authStore.token = response._data.token
        if (process.client) {
          localStorage.setItem('auth_token', response._data.token)
        }
      }
    },

    onResponseError({ response }) {
      if (response.status === 401) {
        console.warn('Session expirée, déconnexion...')
        authStore.logout()
      }
    }
  })

  // Cache simple pour les requêtes GET
  const cache = new Map()
  const CACHE_TTL = 5 * 60 * 1000 // 5 minutes

  const cachedFetch = async (url: string, options: any = {}) => {
    // Ne mettre en cache que les GET
    if (options.method && options.method !== 'GET') {
      return apiFetch(url, options)
    }

    const cacheKey = url + JSON.stringify(options.params || {})
    const cached = cache.get(cacheKey)

    if (cached && Date.now() - cached.timestamp < CACHE_TTL) {
      return cached.data
    }

    const data = await apiFetch(url, options)
    cache.set(cacheKey, { data, timestamp: Date.now() })
    return data
  }

  return {
    // Dignitaires
    getDignitaires: (params?: any) => cachedFetch('/dignitaires', { params }),
    getDignitaire: (id: number) => apiFetch(`/dignitaires/${id}`),
    createDignitaire: (data: any) => apiFetch('/dignitaires', { method: 'POST', body: data }),
    updateDignitaire: (id: number, data: any) => apiFetch(`/dignitaires/${id}`, { method: 'PUT', body: data }),
    deleteDignitaire: (id: number) => apiFetch(`/dignitaires/${id}`, { method: 'DELETE' }),
    getStats: () => cachedFetch('/dignitaires-stats'),

    // Nominations
    getNominations: (params?: any) => cachedFetch('/nominations', { params }),
    createNomination: (data: any) => apiFetch('/nominations', { method: 'POST', body: data }),
    updateNomination: (id: number, data: any) => apiFetch(`/nominations/${id}`, { method: 'PUT', body: data }),
    deleteNomination: (id: number) => apiFetch(`/nominations/${id}`, { method: 'DELETE' }),

    // Décorations
    getDecorations: () => cachedFetch('/decorations'),
    createDecoration: (data: any) => apiFetch('/decorations', { method: 'POST', body: data }),

    // Enfants
    getEnfants: (params?: any) => cachedFetch('/enfants', { params }),
    getEnfant: (id: number) => apiFetch(`/enfants/${id}`),
    createEnfant: (data: any) => apiFetch('/enfants', { method: 'POST', body: data }),
    updateEnfant: (id: number, data: any) => apiFetch(`/enfants/${id}`, { method: 'PUT', body: data }),
    deleteEnfant: (id: number) => apiFetch(`/enfants/${id}`, { method: 'DELETE' }),

    // Conjoints
    getConjoints: (dignitaireId: number) => apiFetch(`/dignitaires/${dignitaireId}/conjoints`),
    createConjoint: (dignitaireId: number, data: any) => apiFetch(`/dignitaires/${dignitaireId}/conjoints`, { method: 'POST', body: data }),
    updateConjoint: (id: number, data: any) => apiFetch(`/conjoints/${id}`, { method: 'PUT', body: data }),
    deleteConjoint: (id: number) => apiFetch(`/conjoints/${id}`, { method: 'DELETE' }),
    terminerUnionConjoint: (id: number, data: any) => apiFetch(`/conjoints/${id}/terminer-union`, { method: 'POST', body: data }),

    // Documents dignitaires
    getDignitaireDocuments: (dignitaireId: number) => apiFetch(`/dignitaires/${dignitaireId}/documents`),
    uploadDignitaireDocument: async (dignitaireId: number, formData: FormData) => {
      const authStore = useAuthStore()
      const config = useRuntimeConfig()

      const response = await fetch(`${config.public.apiBase}/dignitaires/${dignitaireId}/documents`, {
        method: 'POST',
        body: formData,
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json'
        }
      })

      if (!response.ok) {
        const error = await response.json()
        throw error
      }

      return await response.json()
    },
    deleteDignitaireDocument: (id: number) => apiFetch(`/dignitaire-documents/${id}`, { method: 'DELETE' }),

    // Traçabilité / audit log
    getAuditLogs: (params?: any) => apiFetch('/admin/audit-logs', { params }),

    // Rapports périodiques archivés
    getRapports: (params?: any) => apiFetch('/admin/rapports', { params }),

    // Référentiels (toujours en cache)
    getPays: () => cachedFetch('/pays'),
    getVilles: (params?: any) => cachedFetch('/villes', { params }),
    getEntites: () => cachedFetch('/entites'),
    getPostes: () => cachedFetch('/postes'),
    getLangues: () => cachedFetch('/langues'),
    getDomaines: () => cachedFetch('/domaines'),

    // Auth
    login: (credentials: { username: string; password: string }) => 
      apiFetch('/login', { method: 'POST', body: credentials }),
    logout: () => apiFetch('/logout', { method: 'POST' }),
    getUser: () => apiFetch('/user'),

    // Profil
    updateProfile: (data: any) => apiFetch('/profile', { method: 'PUT', body: data }),
    uploadPhoto: async (formData: FormData) => {
      // Utiliser fetch natif pour FormData car $fetch peut avoir des problèmes
      const authStore = useAuthStore()
      const config = useRuntimeConfig()
      
      const response = await fetch(`${config.public.apiBase}/profile/photo`, {
        method: 'POST',
        body: formData,
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json'
          // Ne PAS définir Content-Type, le navigateur le fait automatiquement pour FormData
        }
      })
      
      if (!response.ok) {
        const error = await response.json()
        throw error
      }
      
      return await response.json()
    },
    updatePassword: (data: any) => apiFetch('/profile/password', { method: 'PUT', body: data }),
    
    // Méthode pour vider le cache
    clearCache: () => cache.clear()
  }
}
