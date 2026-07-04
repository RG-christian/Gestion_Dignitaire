<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <!-- Header moderne avec gradient gabonais -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Nominations</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Gérer les nominations, affectations et décrets</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <!-- Barre de recherche et filtres -->
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <div class="flex-1 w-full">
            <SearchInput
              v-model="filters.search"
              placeholder="Rechercher (fonction, dignitaire, entité)..."
              @update:modelValue="debouncedLoadNominations"
            />
          </div>
          <div class="w-full md:w-64">
            <select
              v-model="filters.dignitaire_id"
              @change="loadNominations"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
            >
              <option value="">Tous les dignitaires</option>
              <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                {{ dig.prenom }} {{ dig.nom }}
              </option>
            </select>
          </div>
          <div class="w-full md:w-64">
            <select
              v-model="filters.entite_id"
              @change="loadNominations"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
            >
              <option value="">Toutes les entités</option>
              <option v-for="entite in entites" :key="entite.id" :value="entite.id">
                {{ entite.nom }}
              </option>
            </select>
          </div>
          <button
            v-if="permissions.peutEcrire('Nomination')"
            @click="openModal()"
            class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter
          </button>
        </div>
      </div>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="relative">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-green-600 border-t-transparent absolute top-0 left-0"></div>
        </div>
      </div>

      <!-- Table -->
      <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="paginatedNominations.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dignitaire</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Entité</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fonction</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date début</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date fin</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="nom in paginatedNominations" :key="nom.id" class="hover:bg-gabon-green-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ nom.dignitaire_nom }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ nom.entite_nom || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ nom.fonction || nom.poste_nom || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ formatDate(nom.date_debut) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="nom.statut === 'terminee'" class="text-sm text-gray-700">
                    {{ formatDate(nom.date_fin) }}
                    <span class="block text-xs text-gray-500">{{ motifFinLabel(nom.motif_fin) }}</span>
                  </span>
                  <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">En cours</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="openDetailModal(nom)" class="inline-flex items-center gap-1 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Détail">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      Détail
                    </button>
                    <button v-if="nom.statut !== 'terminee' && permissions.peutEcrire('Nomination')" @click="openClotureModal(nom)" class="inline-flex items-center gap-1 bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Clôturer">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      Clôturer
                    </button>
                    <button v-if="permissions.peutEcrire('Nomination')" @click="openModal(nom)" class="inline-flex items-center gap-1 bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Modifier">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Modifier
                    </button>
                    <button v-if="permissions.peutSupprimer()" @click="deleteNomination(nom.id)" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Supprimer">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      Supprimer
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <p class="mt-4 text-gray-500 text-lg">Aucune nomination enregistrée</p>
        </div>
        <Pagination
          v-if="nominations.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="nominations.length"
          @update:current-page="currentPage = $event"
        />
      </div>
    </section>
    </div>

    <!-- Modal Ajout/Modification -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            {{ selectedNomination ? 'Modifier' : 'Ajouter' }} une nomination
          </h4>
          <button @click="closeModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveNomination" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Dignitaire <span class="text-red-500">*</span></label>
              <select v-model="form.dignitaire_id" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Choisir un dignitaire --</option>
                <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                  {{ dig.prenom }} {{ dig.nom }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Entité</label>
              <select v-model="form.entite_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Sélectionner une entité --</option>
                <option v-for="entite in entites" :key="entite.id" :value="entite.id">
                  {{ entite.nom }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Poste</label>
              <select v-model="form.poste_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Sélectionner un poste --</option>
                <option v-for="poste in postes" :key="poste.id" :value="poste.id">
                  {{ poste.intitule }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Fonction</label>
              <input v-model="form.fonction" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Ex: Directeur Général">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Numéro de décret</label>
              <input v-model="form.numero_decret" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Ex: 001/PR/2024">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de début</label>
                <input v-model="form.date_debut" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
                <input v-model="form.date_fin" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>
          </div>
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
              {{ selectedNomination ? 'Modifier' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Détail -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeDetailModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-gabon-blue-600 to-sky-600 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Détail de la Nomination
          </h4>
          <button @click="closeDetailModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div v-if="selectedDetail" class="p-6">
          <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Dignitaire</p>
              <p class="text-lg font-bold text-gray-900">{{ selectedDetail.dignitaire_nom }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Entité</p>
                <p class="text-gray-900">{{ selectedDetail.entite_nom || 'N/A' }}</p>
              </div>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Poste</p>
                <p class="text-gray-900">{{ selectedDetail.poste_nom || 'N/A' }}</p>
              </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Fonction</p>
              <p class="text-gray-900">{{ selectedDetail.fonction || 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Numéro de décret</p>
              <p class="text-gray-900">{{ selectedDetail.numero_decret || 'N/A' }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Date de début</p>
                <p class="text-gray-900">{{ formatDate(selectedDetail.date_debut) }}</p>
              </div>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Statut</p>
                <p class="text-gray-900">
                  {{ selectedDetail.statut === 'terminee' ? `Terminée (${formatDate(selectedDetail.date_fin)})` : 'En cours' }}
                  <span v-if="selectedDetail.statut === 'terminee'" class="block text-sm text-gray-500">{{ motifFinLabel(selectedDetail.motif_fin) }}</span>
                </p>
              </div>
            </div>
          </div>
          <div class="mt-6 pt-4 border-t">
            <button @click="closeDetailModal" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Fermer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Clôture -->
    <div v-if="showClotureModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeClotureModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
          <h4 class="text-xl font-bold text-white">Clôturer la nomination</h4>
        </div>
        <form @submit.prevent="confirmCloture" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Motif <span class="text-red-500">*</span></label>
            <div class="space-y-2">
              <label class="flex items-center gap-2 border rounded-lg px-4 py-3 cursor-pointer hover:bg-gray-50">
                <input type="radio" v-model="clotureForm.motif_fin" value="fin_fonction" required>
                <span>Fin de fonction formelle</span>
              </label>
              <label class="flex items-center gap-2 border rounded-lg px-4 py-3 cursor-pointer hover:bg-gray-50">
                <input type="radio" v-model="clotureForm.motif_fin" value="mise_a_disposition" required>
                <span>Mise à disposition de l'administration d'origine</span>
              </label>
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
            <input v-model="clotureForm.date_fin" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
          </div>
          <div class="flex gap-3 pt-2">
            <button type="button" @click="closeClotureModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition">Confirmer</button>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
definePageMeta({
  middleware: 'auth'
})

const config = useRuntimeConfig()
const authStore = useAuthStore()
const permissions = usePermissions()
const referentiels = useReferentiels()
const { debounce } = useDebounce()

const nominations = ref([])
const dignitaires = ref([])
const entites = ref([])
const postes = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const showClotureModal = ref(false)
const selectedNomination = ref(null)
const selectedDetail = ref(null)
const nominationToClose = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: '',
  dignitaire_id: '',
  entite_id: ''
})

const form = reactive({
  dignitaire_id: '',
  entite_id: '',
  poste_id: '',
  fonction: '',
  numero_decret: '',
  date_debut: '',
  date_fin: ''
})

const clotureForm = reactive({
  motif_fin: '',
  date_fin: new Date().toISOString().slice(0, 10)
})

function motifFinLabel(motif) {
  if (motif === 'mise_a_disposition') return 'Mise à disposition'
  if (motif === 'fin_fonction') return 'Fin de fonction'
  return ''
}

// Pagination
const totalPages = computed(() => Math.ceil(nominations.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, nominations.value.length))
const paginatedNominations = computed(() => {
  return nominations.value.slice(startIndex.value, endIndex.value)
})

function formatDate(dateStr) {
  if (!dateStr) return 'N/A'
  const date = new Date(dateStr)
  return date.toLocaleDateString('fr-FR')
}

async function loadNominations() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)
    if (filters.entite_id) params.append('entite_id', filters.entite_id)
    
    const response = await $fetch(`${config.public.apiBase}/nominations?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    nominations.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1
  } catch (error) {
    console.error('Erreur chargement nominations:', error)
    nominations.value = []
  } finally {
    loading.value = false
  }
}

// Version debouncée pour optimiser les requêtes AJAX
const debouncedLoadNominations = debounce(loadNominations, 500)

async function loadDignitaires() {
  try {
    const response = await $fetch(`${config.public.apiBase}/dignitaires?per_page=1000`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    dignitaires.value = response.data || []
  } catch (error) {
    console.error('Erreur:', error)
  }
}

async function loadPostes() {
  try {
    const response = await $fetch(`${config.public.apiBase}/postes`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    postes.value = Array.isArray(response) ? response : (response.data || [])
  } catch (error) {
    console.error('Erreur:', error)
  }
}

function openModal(nomination = null) {
  selectedNomination.value = nomination
  if (nomination) {
    form.dignitaire_id = nomination.dignitaire_id
    form.entite_id = nomination.entite_id || ''
    form.poste_id = nomination.poste_id || ''
    form.fonction = nomination.fonction || ''
    form.numero_decret = nomination.numero_decret || ''
    form.date_debut = nomination.date_debut || ''
    form.date_fin = nomination.date_fin || ''
  } else {
    form.dignitaire_id = ''
    form.entite_id = ''
    form.poste_id = ''
    form.fonction = ''
    form.numero_decret = ''
    form.date_debut = ''
    form.date_fin = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedNomination.value = null
}

function openDetailModal(nomination) {
  selectedDetail.value = nomination
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

function openClotureModal(nomination) {
  nominationToClose.value = nomination
  clotureForm.motif_fin = ''
  clotureForm.date_fin = new Date().toISOString().slice(0, 10)
  showClotureModal.value = true
}

function closeClotureModal() {
  showClotureModal.value = false
  nominationToClose.value = null
}

async function confirmCloture() {
  try {
    await $fetch(`${config.public.apiBase}/nominations/${nominationToClose.value.id}/cloturer`, {
      method: 'POST',
      body: clotureForm,
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Nomination clôturée',
      timer: 2000,
      showConfirmButton: false
    })

    closeClotureModal()
    loadNominations()
  } catch (error) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de la clôture'
    })
  }
}

async function saveNomination() {
  try {
    if (selectedNomination.value) {
      await $fetch(`${config.public.apiBase}/nominations/${selectedNomination.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/nominations`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedNomination.value ? 'Nomination modifiée avec succès' : 'Nomination ajoutée avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeModal()
    loadNominations()
  } catch (error) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de la sauvegarde'
    })
  }
}

async function deleteNomination(id) {
  const { $swal } = useNuxtApp()
  const result = await $swal.fire({
    title: 'Êtes-vous sûr ?',
    text: 'Cette action est irréversible',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#dc2626',
    confirmButtonText: 'Oui, supprimer',
    cancelButtonText: 'Annuler'
  })
  
  if (result.isConfirmed) {
    try {
      await $fetch(`${config.public.apiBase}/nominations/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'La nomination a été supprimée avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      loadNominations()
    } catch (error) {
      console.error('Erreur:', error)
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Erreur lors de la suppression'
      })
    }
  }
}

onMounted(async () => {
  entites.value = await referentiels.getEntites()
  await loadPostes()
  await loadDignitaires()
  await loadNominations()
})
</script>
