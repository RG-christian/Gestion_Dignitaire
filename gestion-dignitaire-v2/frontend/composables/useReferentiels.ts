// Cache pour les référentiels
const villesCache = ref<any[]>([])
const entitesCache = ref<any[]>([])
const paysCache = ref<any[]>([])
const regionsCache = ref<any[]>([])
const languesCache = ref<any[]>([])
const domainesCache = ref<any[]>([])
const structuresCache = ref<any[]>([])
const etablissementsCache = ref<any[]>([])
const dignitairesCache = ref<any[]>([])
let dignitairesCacheAt = 0
let dignitairesPending: Promise<any[]> | null = null

const DIGNITAIRES_CACHE_TTL = 60_000

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

  const getDignitaires = async () => {
    const cacheEstValide = dignitairesCache.value.length > 0
      && Date.now() - dignitairesCacheAt < DIGNITAIRES_CACHE_TTL

    if (cacheEstValide) return dignitairesCache.value
    if (dignitairesPending) return dignitairesPending

    dignitairesPending = $fetch<any[]>(`${config.public.apiBase}/dignitaires/options`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    }).then((response) => {
      dignitairesCache.value = response || []
      dignitairesCacheAt = Date.now()
      return dignitairesCache.value
    }).catch((error) => {
      console.error('Erreur /dignitaires/options:', error)
      return []
    }).finally(() => {
      dignitairesPending = null
    })

    return dignitairesPending
  }

  const getBundle = async (cles: string[]) => {
    const caches: Record<string, any> = {
      villes: villesCache,
      entites: entitesCache,
      pays: paysCache,
      regions: regionsCache,
      langues: languesCache,
      domaines: domainesCache,
      structures: structuresCache,
      etablissements: etablissementsCache,
      dignitaires: dignitairesCache
    }

    const clesValides = [...new Set(cles)].filter((cle) => caches[cle])
    const manquantes = clesValides.filter((cle) => {
      if (cle === 'dignitaires') {
        return dignitairesCache.value.length === 0
          || Date.now() - dignitairesCacheAt >= DIGNITAIRES_CACHE_TTL
      }
      return caches[cle].value.length === 0
    })

    if (manquantes.length > 0) {
      try {
        const response = await $fetch<Record<string, any[]>>(`${config.public.apiBase}/referentiels/bundle`, {
          params: { include: manquantes.join(',') },
          headers: { Authorization: `Bearer ${authStore.token}` }
        })

        for (const cle of manquantes) {
          caches[cle].value = response[cle] || []
        }
        if (manquantes.includes('dignitaires')) dignitairesCacheAt = Date.now()
      } catch (error) {
        console.error('Erreur /referentiels/bundle:', error)
      }
    }

    return Object.fromEntries(clesValides.map((cle) => [cle, caches[cle].value]))
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
    getDignitaires,
    getBundle,
    invalidateDignitaires: () => {
      dignitairesCache.value = []
      dignitairesCacheAt = 0
    },

    // Permet aux endpoints agrégés d'alimenter le même cache que les pages
    // classiques, afin d'éviter de retélécharger ces listes à la navigation.
    primeVilles: (villes: any[]) => {
      villesCache.value = villes || []
    },
    primeEntites: (entites: any[]) => {
      entitesCache.value = entites || []
    },
    
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
      dignitairesCache.value = []
      dignitairesCacheAt = 0
    }
  }
}
