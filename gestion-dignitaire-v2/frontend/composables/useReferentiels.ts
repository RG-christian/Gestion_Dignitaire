// Cache pour les référentiels
const villesCache = ref<any[]>([])
const entitesCache = ref<any[]>([])
const paysCache = ref<any[]>([])
const regionsCache = ref<any[]>([])
const languesCache = ref<any[]>([])
const domainesCache = ref<any[]>([])
const structuresCache = ref<any[]>([])
const etablissementsCache = ref<any[]>([])

export const useReferentiels = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const fetchWithCache = async (endpoint: string, cache: any) => {
    if (cache.value.length > 0) {
      return cache.value
    }

    try {
      const response = await $fetch(`${config.public.apiBase}${endpoint}`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
      cache.value = response || []
      return cache.value
    } catch (error) {
      console.error(`Erreur ${endpoint}:`, error)
      return []
    }
  }

  return {
    getVilles: () => fetchWithCache('/villes', villesCache),
    getEntites: () => fetchWithCache('/entites', entitesCache),
    getPays: () => fetchWithCache('/pays', paysCache),
    getRegions: () => fetchWithCache('/regions', regionsCache),
    getLangues: () => fetchWithCache('/langues', languesCache),
    getDomaines: () => fetchWithCache('/domaines', domainesCache),
    getStructures: () => fetchWithCache('/structures', structuresCache),
    getEtablissements: () => fetchWithCache('/etablissements', etablissementsCache),
    
    // Méthode pour vider le cache si nécessaire
    clearCache: () => {
      villesCache.value = []
      entitesCache.value = []
      paysCache.value = []
      regionsCache.value = []
      languesCache.value = []
      domainesCache.value = []
      structuresCache.value = []
      etablissementsCache.value = []
    }
  }
}
