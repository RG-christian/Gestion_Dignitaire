export const useApi = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const apiFetch = $fetch.create({
    baseURL: config.public.apiBase as string,
    
    onRequest({ options }) {
      const token = authStore.token
      if (token) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${token}`
        }
      }
    },

    onResponseError({ response }) {
      if (response.status === 401) {
        authStore.logout()
        navigateTo('/login')
      }
    }
  })

  return {
    // Dignitaires
    getDignitaires: (params?: any) => apiFetch('/dignitaires', { params }),
    getDignitaire: (id: number) => apiFetch(`/dignitaires/${id}`),
    createDignitaire: (data: any) => apiFetch('/dignitaires', { method: 'POST', body: data }),
    updateDignitaire: (id: number, data: any) => apiFetch(`/dignitaires/${id}`, { method: 'PUT', body: data }),
    deleteDignitaire: (id: number) => apiFetch(`/dignitaires/${id}`, { method: 'DELETE' }),
    getStats: () => apiFetch('/dignitaires-stats'),

    // Nominations
    getNominations: (params?: any) => apiFetch('/nominations', { params }),
    createNomination: (data: any) => apiFetch('/nominations', { method: 'POST', body: data }),
    updateNomination: (id: number, data: any) => apiFetch(`/nominations/${id}`, { method: 'PUT', body: data }),
    deleteNomination: (id: number) => apiFetch(`/nominations/${id}`, { method: 'DELETE' }),

    // Décorations
    getDecorations: () => apiFetch('/decorations'),
    createDecoration: (data: any) => apiFetch('/decorations', { method: 'POST', body: data }),

    // Référentiels
    getPays: () => apiFetch('/pays'),
    getVilles: (params?: any) => apiFetch('/villes', { params }),
    getEntites: () => apiFetch('/entites'),
    getPostes: () => apiFetch('/postes'),
    getLangues: () => apiFetch('/langues'),
    getDomaines: () => apiFetch('/domaines'),

    // Auth
    login: (credentials: { username: string; password: string }) => 
      apiFetch('/login', { method: 'POST', body: credentials }),
    logout: () => apiFetch('/logout', { method: 'POST' }),
    getUser: () => apiFetch('/user'),
  }
}
