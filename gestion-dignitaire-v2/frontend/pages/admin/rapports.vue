<template>
  <DashboardLayout>
    <div class="max-w-7xl mx-auto p-6">
      <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
          <i class="fas fa-file-export text-blue-500"></i>
          Rapports &amp; Exports
        </h1>
        <p class="text-gray-600 text-sm mt-1">Générez un export ponctuel filtré, ou consultez les rapports périodiques générés automatiquement</p>
      </header>

      <!-- Générateur de rapport -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Générateur de rapport</h2>

        <div class="grid md:grid-cols-4 gap-4 mb-4">
          <div>
            <label class="text-xs font-semibold text-gray-600 block mb-1">Module</label>
            <select v-model="selectedModuleKey" @change="resetModuleFilters" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
              <option v-for="mod in modules" :key="mod.key" :value="mod.key">{{ mod.label }}</option>
            </select>
          </div>

          <div v-if="selectedModule.filters.includes('search')">
            <label class="text-xs font-semibold text-gray-600 block mb-1">Recherche</label>
            <input v-model="moduleFilters.search" type="text" placeholder="Rechercher..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
          </div>

          <div v-if="selectedModule.filters.includes('genre')">
            <label class="text-xs font-semibold text-gray-600 block mb-1">Genre</label>
            <select v-model="moduleFilters.genre" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
              <option value="">Tous</option>
              <option value="Homme">Homme</option>
              <option value="Femme">Femme</option>
            </select>
          </div>

          <div v-if="selectedModule.filters.includes('statut')">
            <label class="text-xs font-semibold text-gray-600 block mb-1">Statut</label>
            <select v-model="moduleFilters.statut" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
              <option value="">Tous</option>
              <option value="actif">Actif</option>
              <option value="retraite">Retraité</option>
              <option value="non_localise">Non localisé</option>
            </select>
          </div>

          <div v-if="selectedModule.filters.includes('ville_id')">
            <label class="text-xs font-semibold text-gray-600 block mb-1">Ville</label>
            <select v-model="moduleFilters.ville_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
              <option value="">Toutes</option>
              <option v-for="ville in villes" :key="ville.id" :value="ville.id">{{ ville.nom }}</option>
            </select>
          </div>

          <div v-if="selectedModule.filters.includes('entite_id')">
            <label class="text-xs font-semibold text-gray-600 block mb-1">Entité</label>
            <select v-model="moduleFilters.entite_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
              <option value="">Toutes</option>
              <option v-for="entite in entites" :key="entite.id" :value="entite.id">{{ entite.nom }}</option>
            </select>
          </div>

          <div v-if="selectedModule.filters.includes('dignitaire_id')">
            <label class="text-xs font-semibold text-gray-600 block mb-1">Dignitaire</label>
            <select v-model="moduleFilters.dignitaire_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
              <option value="">Tous</option>
              <option v-for="d in dignitaires" :key="d.id" :value="d.id">{{ d.prenom }} {{ d.nom }}</option>
            </select>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <button @click="generer('pdf')" class="bg-gabon-green-600 hover:bg-gabon-green-700 text-white font-semibold px-5 py-2 rounded-lg text-sm">
            Générer en PDF
          </button>
          <button @click="generer('excel')" class="bg-gabon-blue-600 hover:bg-gabon-blue-700 text-white font-semibold px-5 py-2 rounded-lg text-sm">
            Générer en Excel
          </button>
          <span v-if="generating" class="text-sm text-gray-500">Génération en cours...</span>
        </div>
      </div>

      <!-- Rapports périodiques archivés -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
          <h2 class="text-lg font-bold text-gray-900">Rapports périodiques archivés</h2>
          <select v-model="typeFilter" @change="loadRapports(1)" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">Tous les types</option>
            <option value="mensuel">Mensuel</option>
            <option value="trimestriel">Trimestriel</option>
            <option value="annuel">Annuel</option>
          </select>
        </div>

        <div v-if="loadingRapports" class="flex justify-center items-center py-16">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
        </div>

        <table v-else-if="rapports.length > 0" class="min-w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3 text-left">Type</th>
              <th class="px-4 py-3 text-left">Période</th>
              <th class="px-4 py-3 text-left">Généré le</th>
              <th class="px-4 py-3 text-left">Taille</th>
              <th class="px-4 py-3 text-center">Téléchargement</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="rapport in rapports" :key="rapport.id" class="border-b hover:bg-gray-50">
              <td class="px-4 py-3">
                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ rapport.type }}</span>
              </td>
              <td class="px-4 py-3 text-gray-700">{{ formatDate(rapport.periode_debut) }} — {{ formatDate(rapport.periode_fin) }}</td>
              <td class="px-4 py-3 text-gray-600">{{ formatDateTime(rapport.genere_le) }}</td>
              <td class="px-4 py-3 text-gray-600">{{ formatTaille(rapport.taille_octets) }}</td>
              <td class="px-4 py-3 text-center">
                <button @click="telechargerRapport(rapport)" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                  Télécharger
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-else class="text-center py-16 text-gray-500">
          Aucun rapport périodique généré pour l'instant
        </div>

        <div v-if="lastPage > 1" class="flex items-center justify-between px-4 py-3 border-t bg-gray-50">
          <span class="text-sm text-gray-600">{{ total }} rapport(s) au total</span>
          <div class="flex gap-2">
            <button @click="loadRapports(currentPage - 1)" :disabled="currentPage <= 1" class="px-3 py-1.5 border rounded-lg text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100">Précédent</button>
            <span class="px-3 py-1.5 text-sm">Page {{ currentPage }} / {{ lastPage }}</span>
            <button @click="loadRapports(currentPage + 1)" :disabled="currentPage >= lastPage" class="px-3 py-1.5 border rounded-lg text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100">Suivant</button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const api = useApi()
const fileDownload = useFileDownload()
const referentiels = useReferentiels()

const modules = [
  { key: 'dignitaires', label: 'Dignitaires', exportPath: '/dignitaires-export', filters: ['search', 'genre', 'statut', 'ville_id', 'entite_id'] },
  { key: 'nominations', label: 'Nominations', exportPath: '/nominations-export', filters: ['search', 'dignitaire_id', 'entite_id'] },
  { key: 'decorations', label: 'Décorations', exportPath: '/decorations-export', filters: ['search'] },
  { key: 'diplomes', label: 'Diplômes', exportPath: '/diplomes-export', filters: ['search', 'dignitaire_id'] },
  { key: 'postes', label: 'Postes', exportPath: '/postes-export', filters: ['search', 'dignitaire_id'] },
  { key: 'entites', label: 'Entités', exportPath: '/entites-export', filters: ['search'] },
  { key: 'structures', label: 'Structures', exportPath: '/structures-export', filters: ['search'] },
]

const selectedModuleKey = ref(modules[0].key)
const selectedModule = computed(() => modules.find(m => m.key === selectedModuleKey.value) || modules[0])

const moduleFilters = reactive<Record<string, any>>({
  search: '',
  genre: '',
  statut: '',
  ville_id: '',
  entite_id: '',
  dignitaire_id: ''
})

function resetModuleFilters() {
  moduleFilters.search = ''
  moduleFilters.genre = ''
  moduleFilters.statut = ''
  moduleFilters.ville_id = ''
  moduleFilters.entite_id = ''
  moduleFilters.dignitaire_id = ''
}

const villes = ref<any[]>([])
const entites = ref<any[]>([])
const dignitaires = ref<any[]>([])

const generating = ref(false)

async function generer(format: 'pdf' | 'excel') {
  generating.value = true
  try {
    const params: Record<string, any> = { format }
    selectedModule.value.filters.forEach(key => {
      if (moduleFilters[key]) params[key] = moduleFilters[key]
    })

    await fileDownload.download(
      selectedModule.value.exportPath,
      params,
      `${selectedModule.value.key}.${format === 'excel' ? 'xlsx' : 'pdf'}`
    )
  } catch (error) {
    console.error('Erreur génération rapport:', error)
  } finally {
    generating.value = false
  }
}

const rapports = ref<any[]>([])
const loadingRapports = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const typeFilter = ref('')

async function loadRapports(page: number) {
  loadingRapports.value = true
  try {
    const response: any = await api.getRapports({
      page,
      per_page: 20,
      type: typeFilter.value || undefined
    })

    rapports.value = response.data
    currentPage.value = response.current_page
    lastPage.value = response.last_page
    total.value = response.total
  } catch (error) {
    console.error('Erreur chargement rapports:', error)
  } finally {
    loadingRapports.value = false
  }
}

async function telechargerRapport(rapport: any) {
  try {
    await fileDownload.download(`/admin/rapports/${rapport.id}/download`, {}, rapport.nom_fichier)
  } catch (error) {
    console.error('Erreur téléchargement rapport:', error)
  }
}

function formatDate(date: string) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('fr-FR')
}

function formatDateTime(date: string) {
  if (!date) return '—'
  return new Date(date).toLocaleString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function formatTaille(octets: number | null) {
  if (!octets) return '—'
  if (octets < 1024) return `${octets} o`
  if (octets < 1024 * 1024) return `${(octets / 1024).toFixed(1)} Ko`
  return `${(octets / (1024 * 1024)).toFixed(1)} Mo`
}

onMounted(async () => {
  loadRapports(1)
  villes.value = await referentiels.getVilles()
  entites.value = await referentiels.getEntites()
  try {
    dignitaires.value = await $fetch(`${useRuntimeConfig().public.apiBase}/dignitaires`, {
      headers: { Authorization: `Bearer ${useAuthStore().token}` },
      query: { per_page: 500 }
    }).then((r: any) => r.data || [])
  } catch (error) {
    console.error('Erreur chargement dignitaires:', error)
  }
})

useHead({ title: 'Rapports & Exports - Gestion Dignitaires' })
</script>
