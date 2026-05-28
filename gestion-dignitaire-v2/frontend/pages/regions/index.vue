<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <!-- Header moderne avec gradient gabonais -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Régions & Provinces</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Gérer les régions géographiques et provinces administratives</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <!-- Barre de recherche et filtres -->
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <!-- Recherche -->
          <div class="flex-1 w-full">
            <SearchInput
              v-model="filters.search"
              placeholder="Rechercher une région ou province..."
              @update:modelValue="debouncedLoadRegions"
            />
          </div>

          <!-- Filtre par type -->
          <div class="w-full md:w-48">
            <select
              v-model="filters.type"
              @change="loadRegions"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
            >
              <option value="">Tous les types</option>
              <option value="region">Régions</option>
              <option value="province">Provinces</option>
            </select>
          </div>

          <!-- Filtre par continent (pour régions) -->
          <div class="w-full md:w-64">
            <select
              v-model="filters.continent"
              @change="loadRegions"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
            >
              <option value="">Tous les continents</option>
              <option value="Afrique">Afrique</option>
              <option value="Amérique">Amérique</option>
              <option value="Asie">Asie</option>
              <option value="Europe">Europe</option>
              <option value="Océanie">Océanie</option>
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

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="relative">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-green-600 border-t-transparent absolute top-0 left-0"></div>
        </div>
      </div>

      <!-- Tableau moderne -->
      <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="paginatedRegions.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nom</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Continent/Pays</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="r in paginatedRegions" :key="r.id" class="hover:bg-gabon-green-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-900">{{ r.nom }}</span>
                    <!-- Badge villes pour provinces uniquement -->
                    <span v-if="r.type === 'province' && r.villes_count > 0" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                      {{ r.villes_count }} ville{{ r.villes_count > 1 ? 's' : '' }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="r.type === 'province'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gabon-blue-100 text-gabon-blue-800">
                    Province
                  </span>
                  <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gabon-yellow-100 text-gabon-yellow-800">
                    Région
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="r.type === 'province'" class="text-sm text-gray-700">
                    {{ r.pays_nom || 'N/A' }}
                  </span>
                  <span v-else class="text-sm text-gray-700">
                    {{ r.continent || 'N/A' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="openModal(r)"
                      class="inline-flex items-center gap-1 bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors"
                      title="Modifier"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Modifier
                    </button>
                    <button
                      @click="deleteRegion(r.id)"
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
          </svg>
          <p class="mt-4 text-gray-500 text-lg">Aucune région ou province enregistrée</p>
        </div>

        <!-- Pagination -->
        <Pagination
          v-if="regions.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="regions.length"
          @update:current-page="currentPage = $event"
        />
      </div>
    </section>

    <!-- Modal d'ajout/modification -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Header du modal -->
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            {{ selectedRegion ? 'Modifier' : 'Ajouter' }} une région/province
          </h4>
          <button @click="closeModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="saveRegion" class="p-6">
          <div class="space-y-4">
            <!-- Nom -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Nom <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.nom"
                type="text"
                required
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
                placeholder="Ex: Estuaire, Île-de-France, Europe de l'Ouest..."
              >
            </div>

            <!-- Type -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Type <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.type"
                @change="onTypeChange"
                required
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
                <option value="">-- Sélectionner un type --</option>
                <option value="region">Région (géographique)</option>
                <option value="province">Province (administrative)</option>
              </select>
              <p class="text-xs text-gray-500 mt-1">
                <strong>Région :</strong> Division géographique large (ex: Europe de l'Ouest)<br>
                <strong>Province :</strong> Division administrative d'un pays (ex: Estuaire au Gabon)
              </p>
            </div>

            <!-- Continent (si région) -->
            <div v-if="form.type === 'region'">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Continent <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.continent"
                :required="form.type === 'region'"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
                <option value="">-- Sélectionner un continent --</option>
                <option value="Afrique">Afrique</option>
                <option value="Amérique">Amérique</option>
                <option value="Asie">Asie</option>
                <option value="Europe">Europe</option>
                <option value="Océanie">Océanie</option>
              </select>
            </div>

            <!-- Pays (si province) -->
            <div v-if="form.type === 'province'">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Pays <span class="text-red-500">*</span>
              </label>
              
              <!-- Loader pendant le chargement des pays -->
              <div v-if="loadingPays" class="flex items-center gap-2 text-gray-500 py-3">
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Chargement des pays...</span>
              </div>
              
              <!-- Select des pays -->
              <select
                v-else
                v-model="form.pays_id"
                @change="onPaysChange"
                :required="form.type === 'province'"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
                <option :value="null">-- Sélectionner un pays --</option>
                <option v-for="p in pays" :key="p.id" :value="p.id">
                  {{ p.nom }}
                </option>
              </select>
              
              <!-- Bouton création de pays si introuvable -->
              <div class="mt-3 p-4 bg-gradient-to-r from-gabon-blue-50 to-gabon-green-50 border-2 border-dashed border-gabon-blue-300 rounded-lg">
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-gabon-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700 mb-2">
                      Pays introuvable dans la liste ?
                    </p>
                    <button
                      type="button"
                      @click="goToCreatePays"
                      class="inline-flex items-center gap-2 bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 hover:from-gabon-blue-700 hover:to-gabon-blue-800 text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 text-sm"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                      </svg>
                      Créer un nouveau pays
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
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
              {{ selectedRegion ? 'Modifier' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import Pagination from '~/components/Pagination.vue'

definePageMeta({
  middleware: 'auth'
})

const authStore = useAuthStore()
const { debounce } = useDebounce()

const regions = ref([])
const pays = ref([])
const loading = ref(false)
const loadingPays = ref(false)
const showModal = ref(false)
const selectedRegion = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const form = ref({
  nom: '',
  type: '',
  continent: '',
  pays_id: null,
  pays_nom: ''
})

const filters = ref({
  search: '',
  type: '',
  continent: ''
})

// Pagination
const paginatedRegions = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return regions.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(regions.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage + 1)
const endIndex = computed(() => Math.min(currentPage.value * itemsPerPage, regions.value.length))

// Charger les régions
async function loadRegions() {
  loading.value = true
  try {
    const config = useRuntimeConfig()
    const token = authStore.token
    const params = new URLSearchParams()
    
    if (filters.value.search) params.append('search', filters.value.search)
    if (filters.value.type) params.append('type', filters.value.type)
    if (filters.value.continent) params.append('continent', filters.value.continent)
    
    const response = await $fetch(`${config.public.apiBase}/regions-crud?${params.toString()}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    regions.value = response || []
    currentPage.value = 1
  } catch (error) {
    console.error('Erreur lors du chargement des régions:', error)
    regions.value = []
  } finally {
    loading.value = false
  }
}

// Charger la liste des pays
async function loadPays() {
  loadingPays.value = true
  try {
    const config = useRuntimeConfig()
    const token = authStore.token
    
    const response = await $fetch(`${config.public.apiBase}/pays`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    pays.value = response || []
  } catch (error) {
    console.error('Erreur lors du chargement des pays:', error)
    pays.value = []
  } finally {
    loadingPays.value = false
  }
}

// Version debouncée pour optimiser les requêtes AJAX
const debouncedLoadRegions = debounce(loadRegions, 500)

// Ouvrir le modal
async function openModal(region = null) {
  selectedRegion.value = region
  
  // Charger la liste des pays
  await loadPays()
  
  if (region) {
    form.value = {
      nom: region.nom,
      type: region.type || '',
      continent: region.continent || '',
      pays_id: region.pays_id || null,
      pays_nom: region.pays_nom || ''
    }
  } else {
    form.value = {
      nom: '',
      type: '',
      continent: '',
      pays_id: null,
      pays_nom: ''
    }
  }
  
  showModal.value = true
}

// Fermer le modal
function closeModal() {
  showModal.value = false
  selectedRegion.value = null
  form.value = {
    nom: '',
    type: '',
    continent: '',
    pays_id: null,
    pays_nom: ''
  }
}

// Changement de type
function onTypeChange() {
  if (form.value.type === 'region') {
    form.value.pays_id = null
    form.value.pays_nom = ''
  } else if (form.value.type === 'province') {
    form.value.continent = ''
  }
}

// Changement de pays sélectionné
function onPaysChange() {
  const selectedPays = pays.value.find(p => p.id === form.value.pays_id)
  if (selectedPays) {
    form.value.pays_nom = selectedPays.nom
  }
}

// Rediriger vers la page pays
function goToCreatePays() {
  navigateTo('/pays')
}

// Enregistrer une région
async function saveRegion() {
  try {
    const config = useRuntimeConfig()
    const token = authStore.token
    const url = selectedRegion.value
      ? `${config.public.apiBase}/regions-crud/${selectedRegion.value.id}`
      : `${config.public.apiBase}/regions-crud`
    
    const method = selectedRegion.value ? 'PUT' : 'POST'
    
    // Préparer les données selon le type
    const data = {
      nom: form.value.nom,
      type: form.value.type
    }
    
    if (form.value.type === 'region') {
      data.continent = form.value.continent
      data.pays_nom = null
    } else if (form.value.type === 'province') {
      data.pays_nom = form.value.pays_nom
      data.continent = null
    }
    
    await $fetch(url, {
      method,
      body: data,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedRegion.value ? 'Région/Province modifiée avec succès' : 'Région/Province ajoutée avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeModal()
    await loadRegions()
  } catch (error) {
    console.error('Erreur lors de l\'enregistrement:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Une erreur est survenue lors de l\'enregistrement'
    })
  }
}

// Supprimer une région
async function deleteRegion(id) {
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
      const config = useRuntimeConfig()
      const token = authStore.token
      await $fetch(`${config.public.apiBase}/regions-crud/${id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'La région/province a été supprimée avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      await loadRegions()
    } catch (error) {
      console.error('Erreur lors de la suppression:', error)
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Une erreur est survenue lors de la suppression'
      })
    }
  }
}

// Initialisation
onMounted(async () => {
  await loadRegions()
})
</script>
