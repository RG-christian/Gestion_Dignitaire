<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <!-- Header moderne avec gradient gabonais -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Enfants</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Gérer les enfants des dignitaires</p>
      </div>
    </header>

    <main class="max-w-full mx-auto px-2 pb-8">
      <!-- Barre de recherche et filtres modernisée -->
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <!-- Recherche -->
          <div class="flex-1 w-full">
            <SearchInput
              v-model="filters.search"
              placeholder="Rechercher un enfant..."
              @update:modelValue="debouncedLoadEnfants"
            />
          </div>

          <!-- Filtre dignitaire -->
          <div class="w-full md:w-64">
            <select
              v-model="filters.dignitaire_id"
              @change="loadEnfants"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
            >
              <option value="">Tous les dignitaires</option>
              <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                {{ dig.prenom }} {{ dig.nom }}
              </option>
            </select>
          </div>

          <!-- Bouton Ajouter -->
          <button
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

      <!-- Loader modernisé -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="relative">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-green-600 border-t-transparent absolute top-0 left-0"></div>
        </div>
      </div>

      <!-- Erreur -->
      <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg shadow mb-4">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <span class="font-semibold">{{ error }}</span>
        </div>
      </div>

      <!-- Table modernisée -->
      <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="paginatedEnfants.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nom</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Prénom</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Genre</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Naissance</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lieu</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dignitaire</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="enfant in paginatedEnfants" :key="enfant.id" class="hover:bg-gabon-green-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ enfant.nom }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  {{ enfant.prenom }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="enfant.genre === 'M'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gabon-blue-100 text-gabon-blue-800">
                    Masculin
                  </span>
                  <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                    Féminin
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  {{ formatDate(enfant.date_naissance) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  {{ enfant.lieu_naissance_nom || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  {{ enfant.dignitaire_nom_complet }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="openDetailModal(enfant)"
                      class="inline-flex items-center gap-1 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold px-3 py-2 rounded-lg transition-colors"
                      title="Détails"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      Détails
                    </button>
                    <button
                      @click="openModal(enfant)"
                      class="inline-flex items-center gap-1 bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors"
                      title="Modifier"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Modifier
                    </button>
                    <button
                      @click="deleteEnfant(enfant.id)"
                      class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 font-semibold px-3 py-2 rounded-lg transition-colors"
                      title="Supprimer"
                    >
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
        
        <!-- Message vide -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          <p class="mt-4 text-gray-500 text-lg">Aucun enfant enregistré</p>
        </div>

        <!-- Pagination -->
        <Pagination
          v-if="enfants.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="enfants.length"
          @update:current-page="currentPage = $event"
        />
      </div>
    </main>
    </div>

    <!-- Modal Ajout/Modification -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-scale-in">
        <!-- Header du modal -->
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            {{ selectedEnfant ? 'Modifier' : 'Ajouter' }} un enfant
          </h4>
          <button @click="closeModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="saveEnfant" class="p-6">
          <div class="space-y-4">
            <!-- Dignitaire -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Dignitaire <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.dignitaire_id"
                required
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
                <option value="">-- Choisir un dignitaire --</option>
                <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                  {{ dig.prenom }} {{ dig.nom }}
                </option>
              </select>
            </div>

            <!-- Nom et Prénom -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Nom <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.nom"
                  type="text"
                  required
                  class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
                  placeholder="Nom de famille"
                >
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Prénom <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.prenom"
                  type="text"
                  required
                  class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
                  placeholder="Prénom"
                >
              </div>
            </div>

            <!-- Genre -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Genre <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.genre"
                required
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
                <option value="">-- Sélectionner un genre --</option>
                <option value="M">Masculin</option>
                <option value="F">Féminin</option>
              </select>
            </div>

            <!-- Date de naissance -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Date de naissance <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.date_naissance"
                type="date"
                required
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
            </div>

            <!-- Lieu de naissance -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Lieu de naissance
              </label>
              <select
                v-model="form.lieu_naissance"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
                <option value="">-- Sélectionner une ville --</option>
                <option v-for="ville in villes" :key="ville.id" :value="ville.id">
                  {{ ville.nom }}
                </option>
              </select>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition"
            >
              Annuler
            </button>
            <button
              type="submit"
              class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
            >
              {{ selectedEnfant ? 'Modifier' : 'Ajouter' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Détail -->
    <div
      v-if="showDetailModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeDetailModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in">
        <!-- Header du modal -->
        <div class="bg-gradient-to-r from-gabon-blue-600 to-sky-600 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Détails de l'enfant
          </h4>
          <button @click="closeDetailModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Contenu -->
        <div v-if="selectedDetail" class="p-6">
          <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Nom complet</p>
              <p class="text-lg font-bold text-gray-900">{{ selectedDetail.prenom }} {{ selectedDetail.nom }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Genre</p>
                <div class="mt-2">
                  <span v-if="selectedDetail.genre === 'M'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gabon-blue-100 text-gabon-blue-800">
                    Masculin
                  </span>
                  <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-pink-100 text-pink-800">
                    Féminin
                  </span>
                </div>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Date de naissance</p>
                <p class="text-gray-900">{{ formatDate(selectedDetail.date_naissance) }}</p>
                <p v-if="selectedDetail.age" class="text-sm text-gray-600 mt-1">{{ selectedDetail.age }} ans</p>
              </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Lieu de naissance</p>
              <p class="text-gray-900">{{ selectedDetail.lieu_naissance_nom || 'N/A' }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Dignitaire</p>
              <p class="text-lg text-gray-900">{{ selectedDetail.dignitaire_nom_complet }}</p>
            </div>
          </div>

          <!-- Bouton fermer -->
          <div class="mt-6 pt-4 border-t">
            <button
              @click="closeDetailModal"
              class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition"
            >
              Fermer
            </button>
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

const config = useRuntimeConfig()
const authStore = useAuthStore()
const referentiels = useReferentiels()
const { debounce } = useDebounce()

const enfants = ref([])
const dignitaires = ref([])
const villes = ref([])
const loading = ref(true)
const error = ref('')
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedEnfant = ref(null)
const selectedDetail = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: '',
  dignitaire_id: ''
})

const form = reactive({
  nom: '',
  prenom: '',
  date_naissance: '',
  lieu_naissance: '',
  genre: '',
  dignitaire_id: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(enfants.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, enfants.value.length))
const paginatedEnfants = computed(() => {
  return enfants.value.slice(startIndex.value, endIndex.value)
})

async function loadEnfants() {
  loading.value = true
  error.value = ''
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)

    const response = await $fetch(`${config.public.apiBase}/enfants?${params.toString()}`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    console.log('Enfants chargés:', response)
    enfants.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (err: any) {
    console.error('Erreur chargement enfants:', err)
    error.value = err.message || 'Erreur lors du chargement des enfants'
    enfants.value = []
  } finally {
    loading.value = false
  }
}

async function loadDignitaires() {
  try {
    const response = await $fetch(`${config.public.apiBase}/dignitaires?per_page=1000`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    dignitaires.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement dignitaires:', error)
  }
}

// Version debouncée pour optimiser les requêtes AJAX
const debouncedLoadEnfants = debounce(loadEnfants, 500)

function openModal(enfant: any = null) {
  selectedEnfant.value = enfant
  if (enfant) {
    form.nom = enfant.nom
    form.prenom = enfant.prenom
    form.date_naissance = enfant.date_naissance
    form.lieu_naissance = enfant.lieu_naissance || ''
    form.genre = enfant.genre
    form.dignitaire_id = enfant.dignitaire_id
  } else {
    form.nom = ''
    form.prenom = ''
    form.date_naissance = ''
    form.lieu_naissance = ''
    form.genre = ''
    form.dignitaire_id = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedEnfant.value = null
}

function openDetailModal(enfant: any) {
  selectedDetail.value = enfant
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveEnfant() {
  try {
    if (selectedEnfant.value) {
      await $fetch(`${config.public.apiBase}/enfants/${selectedEnfant.value.id}`, {
        method: 'PUT',
        body: form,
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
    } else {
      await $fetch(`${config.public.apiBase}/enfants`, {
        method: 'POST',
        body: form,
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
    }
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedEnfant.value ? 'Enfant modifié avec succès' : 'Enfant ajouté avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeModal()
    loadEnfants()
  } catch (error) {
    console.error('Erreur sauvegarde:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de la sauvegarde'
    })
  }
}

async function deleteEnfant(id: number) {
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
      await $fetch(`${config.public.apiBase}/enfants/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'L\'enfant a été supprimé avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      loadEnfants()
    } catch (error) {
      console.error('Erreur suppression:', error)
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Erreur lors de la suppression'
      })
    }
  }
}

function formatDate(date: string | null) {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR')
}

onMounted(async () => {
  villes.value = await referentiels.getVilles()
  await loadDignitaires()
  await loadEnfants()
})
</script>

<style scoped>
.animate-scale-in {
  animation: scaleIn 0.3s ease forwards;
}
.animate-fade-in {
  animation: fadeIn 0.3s ease forwards;
}
@keyframes scaleIn {
  from { transform: scale(0.8); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>
