<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <!-- Header moderne avec gradient gabonais -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Pays</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Gérer les pays et leurs informations</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <!-- Barre de recherche et bouton d'ajout -->
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <!-- Recherche -->
          <div class="flex-1 w-full">
            <SearchInput
              v-model="filters.search"
              placeholder="Rechercher (nom, code ISO, continent)..."
              @update:modelValue="debouncedLoadPays"
            />
          </div>
          
          <!-- Bouton Ajouter -->
          <button
            @click="openModal()"
            class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un pays
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
        <div v-if="paginatedPays.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Code ISO</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Indicatif</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Continent</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pays</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Drapeau</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Région</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="p in paginatedPays" :key="p.id" class="hover:bg-gabon-green-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="font-mono text-sm font-semibold text-gray-900">{{ p.code_iso || 'N/A' }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm text-gray-700">{{ p.indicatif || 'N/A' }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gabon-blue-100 text-gabon-blue-800">
                    {{ p.continent || 'N/A' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ p.nom }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <img 
                    v-if="p.code_iso" 
                    :src="`https://flagcdn.com/w40/${p.code_iso.toLowerCase()}.png`" 
                    :alt="`${p.nom} flag`"
                    class="inline-block h-6 rounded shadow-sm"
                    @error="$event.target.style.display='none'"
                  >
                  <span v-else class="text-gray-400 text-sm">N/A</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm text-gray-700">{{ p.region_nom || 'N/A' }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="openModal(p)"
                      class="inline-flex items-center gap-1 bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors"
                      title="Modifier"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Modifier
                    </button>
                    <button
                      @click="deletePays(p.id)"
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <p class="mt-4 text-gray-500 text-lg">Aucun pays enregistré</p>
        </div>

        <!-- Pagination -->
        <Pagination
          v-if="pays.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="pays.length"
          @update:current-page="currentPage = $event"
        />
      </div>
    </section>

    <!-- Modal simplifié avec API REST Countries -->
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ selectedPays ? 'Modifier' : 'Ajouter' }} un pays
          </h4>
          <button @click="closeModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="savePays" class="p-6">
          <!-- Sélection par continent et pays (uniquement en mode ajout) -->
          <div v-if="!selectedPays" class="space-y-4 mb-6">
            <!-- Étape 1 : Continent -->
            <div class="p-4 bg-gradient-to-r from-gabon-green-50 to-gabon-blue-50 rounded-lg border-2 border-gabon-green-200">
              <label class="block text-sm font-semibold text-gray-700 mb-3">
                Étape 1 : Sélectionner le continent
              </label>
              <select 
                v-model="selectedContinent"
                @change="loadCountriesByContinent"
                class="w-full border-2 border-gabon-green-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition bg-white"
              >
                <option value="">-- Sélectionner un continent --</option>
                <option value="Africa">Afrique</option>
                <option value="Americas">Amériques</option>
                <option value="Asia">Asie</option>
                <option value="Europe">Europe</option>
                <option value="Oceania">Océanie</option>
              </select>
            </div>

            <!-- Étape 2 : Pays -->
            <div v-if="selectedContinent" class="p-4 bg-white rounded-lg border-2 border-gray-200">
              <label class="block text-sm font-semibold text-gray-700 mb-3">
                Étape 2 : Sélectionner le pays
              </label>
              <div v-if="loadingCountries" class="text-center py-4">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-gabon-green-600 border-t-transparent"></div>
                <p class="text-sm text-gray-600 mt-2">Chargement des pays...</p>
              </div>
              <select 
                v-else
                v-model="selectedCountryFromAPI"
                @change="fillFromAPI"
                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition bg-white"
              >
                <option value="">-- Sélectionner un pays ou choisir "Autre" --</option>
                <option value="custom">Autre (saisie manuelle)</option>
                <option v-for="country in availableCountries" :key="country.cca2" :value="country.cca2">
                  {{ country.translations?.fra?.common || country.name.common }}
                </option>
              </select>
              <p class="text-xs text-gray-600 mt-2">
                Les informations seront remplies automatiquement. Choisissez "Autre" pour saisie manuelle.
              </p>
            </div>
          </div>

          <!-- Champs du formulaire -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Nom du pays <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.nom"
                type="text"
                required
                :disabled="isAutoFilled"
                :class="['w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition', isAutoFilled ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : 'bg-white']"
                placeholder="Ex: Gabon"
              >
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Code ISO <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.code_iso"
                type="text"
                required
                maxlength="2"
                :disabled="isAutoFilled"
                :class="['w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition uppercase', isAutoFilled ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : 'bg-white']"
                placeholder="Ex: GA"
              >
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Indicatif téléphonique
              </label>
              <input
                v-model="form.indicatif"
                type="text"
                :disabled="isAutoFilled"
                :class="['w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition', isAutoFilled ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : 'bg-white']"
                placeholder="Ex: +241"
              >
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Continent <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.continent"
                type="text"
                disabled
                class="w-full border rounded-lg px-4 py-3 bg-gray-100 text-gray-600 cursor-not-allowed"
              >
              <p class="text-xs text-gray-500 mt-1">
                Le continent est défini par votre sélection à l'étape 1
              </p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center justify-between">
                <span>Région / Province</span>
                <button
                  type="button"
                  @click="openRegionModal"
                  class="text-xs bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 px-3 py-1 rounded-lg transition-colors flex items-center gap-1"
                >
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                  Ajouter une région
                </button>
              </label>
              <select
                v-model="form.region_id"
                :disabled="!form.continent"
                :class="['w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition', !form.continent ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : 'bg-white']"
              >
                <option value="">-- Sélectionner une région --</option>
                <option v-for="region in filteredRegions" :key="region.id" :value="region.id">
                  {{ region.nom }}
                </option>
              </select>
              <p v-if="!form.continent" class="text-xs text-gray-500 mt-1">
                Sélectionnez d'abord un continent
              </p>
              <p v-else class="text-xs text-gabon-blue-600 mt-1">
                La région n'existe pas ? Cliquez sur "Ajouter une région" pour la créer
              </p>
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
              {{ selectedPays ? 'Modifier' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal secondaire pour ajouter une région -->
    <div
      v-if="showRegionModal"
      class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[60] p-4"
      @click.self="closeRegionModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <!-- Header -->
        <div class="bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter une région
          </h4>
          <button @click="closeRegionModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Formulaire région -->
        <form @submit.prevent="saveRegion" class="p-6">
          <div class="space-y-4">
            <!-- Type : Région ou Province -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Type <span class="text-red-500">*</span>
              </label>
              <select
                v-model="regionForm.type"
                required
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
              >
                <option value="">-- Sélectionner un type --</option>
                <option value="region">Région (liée au continent)</option>
                <option value="province">Province (liée au pays)</option>
              </select>
              <p class="text-xs text-gray-600 mt-1">
                <span v-if="regionForm.type === 'region'">Une région est associée à un continent (ex: Afrique centrale)</span>
                <span v-else-if="regionForm.type === 'province'">Une province est associée à un pays spécifique (ex: Estuaire pour le Gabon)</span>
                <span v-else>Choisissez le type d'entité géographique</span>
              </p>
            </div>

            <!-- Nom -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Nom de la {{ regionForm.type === 'province' ? 'province' : 'région' }} <span class="text-red-500">*</span>
              </label>
              <input
                v-model="regionForm.nom"
                type="text"
                required
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
                :placeholder="regionForm.type === 'province' ? 'Ex: Estuaire, Haut-Ogooué, Québec...' : 'Ex: Afrique centrale, Europe de l\'Ouest...'"
              >
            </div>

            <!-- Continent (si Région) -->
            <div v-if="regionForm.type === 'region'">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Continent associé <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.continent"
                type="text"
                disabled
                class="w-full border rounded-lg px-4 py-3 bg-gray-100 text-gray-600 cursor-not-allowed"
              >
              <p class="text-xs text-gray-500 mt-1">
                La région sera associée au continent du pays
              </p>
            </div>

            <!-- Pays (si Province) -->
            <div v-if="regionForm.type === 'province'">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Pays associé <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.nom"
                type="text"
                disabled
                class="w-full border rounded-lg px-4 py-3 bg-gray-100 text-gray-600 cursor-not-allowed"
              >
              <p class="text-xs text-gray-500 mt-1">
                La province sera associée au pays sélectionné
              </p>
            </div>
          </div>

          <!-- Boutons -->
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button
              type="button"
              @click="closeRegionModal"
              class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition"
            >
              Annuler
            </button>
            <button
              type="submit"
              class="flex-1 bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 hover:from-gabon-blue-700 hover:to-gabon-blue-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
            >
              Créer {{ regionForm.type === 'province' ? 'la province' : 'la région' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Pagination from '~/components/Pagination.vue'

const authStore = useAuthStore()
const { debounce } = useDebounce()

const pays = ref([])
const regions = ref([])
const loading = ref(false)
const showModal = ref(false)
const selectedPays = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const form = ref({
  nom: '',
  code_iso: '',
  indicatif: '',
  continent: '',
  region_id: ''
})

const filters = ref({
  search: ''
})

// Auto-complétion avec API REST Countries
const selectedContinent = ref('')
const selectedCountryFromAPI = ref('')
const availableCountries = ref([])
const loadingCountries = ref(false)
const isAutoFilled = ref(false)
const filteredRegions = ref([])

// Modal région
const showRegionModal = ref(false)
const regionForm = ref({
  nom: '',
  type: '' // 'region' ou 'province'
})

// Mapping des continents
const continentMapping = {
  'Africa': 'Afrique',
  'Americas': 'Amériques',
  'Asia': 'Asie',
  'Europe': 'Europe',
  'Oceania': 'Océanie'
}

// Pagination
const paginatedPays = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return pays.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(pays.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage + 1)
const endIndex = computed(() => Math.min(currentPage.value * itemsPerPage, pays.value.length))

// Charger les pays
async function loadPays() {
  loading.value = true
  try {
    const config = useRuntimeConfig()
    const token = authStore.token
    const response = await $fetch(`${config.public.apiBase}/pays-crud`, {
      params: { search: filters.value.search },
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
    loading.value = false
  }
}

// Charger les régions
async function loadRegions() {
  try {
    const config = useRuntimeConfig()
    const token = authStore.token
    const response = await $fetch(`${config.public.apiBase}/regions`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    regions.value = response || []
    filteredRegions.value = regions.value
  } catch (error) {
    console.error('Erreur lors du chargement des régions:', error)
    regions.value = []
  }
}

// Version debouncée pour optimiser les requêtes AJAX
const debouncedLoadPays = debounce(loadPays, 500)

// Charger les pays par continent depuis l'API REST Countries
async function loadCountriesByContinent() {
  if (!selectedContinent.value) {
    availableCountries.value = []
    return
  }
  
  loadingCountries.value = true
  try {
    const response = await $fetch(`https://restcountries.com/v3.1/region/${selectedContinent.value}`)
    availableCountries.value = response.sort((a, b) => {
      const nameA = a.translations?.fra?.common || a.name.common
      const nameB = b.translations?.fra?.common || b.name.common
      return nameA.localeCompare(nameB)
    })
  } catch (error) {
    console.error('Erreur lors du chargement des pays:', error)
    availableCountries.value = []
  } finally {
    loadingCountries.value = false
  }
}

// Remplir depuis l'API
async function fillFromAPI() {
  const selected = selectedCountryFromAPI.value
  
  if (selected === 'custom') {
    isAutoFilled.value = false
    form.value = {
      nom: '',
      code_iso: '',
      indicatif: '',
      continent: continentMapping[selectedContinent.value] || '',
      region_id: ''
    }
    filterRegionsByContinent()
  } else if (selected) {
    const country = availableCountries.value.find(c => c.cca2 === selected)
    if (country) {
      isAutoFilled.value = true
      
      const idd = country.idd?.root || ''
      const suffixes = country.idd?.suffixes || []
      const indicatif = suffixes.length > 0 ? `${idd}${suffixes[0]}` : idd
      
      form.value = {
        nom: country.translations?.fra?.common || country.name.common,
        code_iso: country.cca2,
        indicatif: indicatif,
        continent: continentMapping[selectedContinent.value] || '',
        region_id: ''
      }
      filterRegionsByContinent()
    }
  } else {
    isAutoFilled.value = false
    form.value = {
      nom: '',
      code_iso: '',
      indicatif: '',
      continent: continentMapping[selectedContinent.value] || '',
      region_id: ''
    }
    filterRegionsByContinent()
  }
}

// Filtrer les régions par continent
function filterRegionsByContinent() {
  if (!form.value.continent) {
    filteredRegions.value = regions.value
    return
  }
  
  const continentToRegions = {
    'Afrique': ['Afrique centrale', 'Afrique de l\'Est', 'Afrique de l\'Ouest', 'Afrique du Nord', 'Afrique australe'],
    'Amériques': ['Amérique du Nord', 'Amérique du Sud', 'Amérique centrale'],
    'Asie': ['Asie de l\'Est', 'Asie du Sud', 'Asie du Sud-Est', 'Asie centrale', 'Moyen-Orient'],
    'Europe': ['Europe de l\'Est', 'Europe de l\'Ouest', 'Europe du Nord', 'Europe du Sud'],
    'Océanie': ['Océanie']
  }
  
  const allowedRegions = continentToRegions[form.value.continent] || []
  filteredRegions.value = regions.value.filter(r => 
    allowedRegions.some(allowed => r.nom.includes(allowed))
  )
}

// Ouvrir le modal
function openModal(paysItem = null) {
  selectedPays.value = paysItem
  selectedContinent.value = ''
  selectedCountryFromAPI.value = ''
  isAutoFilled.value = false
  
  if (paysItem) {
    // Mode édition - tous les champs sont éditables sauf le continent qui reste en lecture seule
    form.value = {
      nom: paysItem.nom,
      code_iso: paysItem.code_iso,
      indicatif: paysItem.indicatif,
      continent: paysItem.continent,
      region_id: paysItem.region_id || ''
    }
    filterRegionsByContinent()
  } else {
    // Mode ajout
    form.value = {
      nom: '',
      code_iso: '',
      indicatif: '',
      continent: '',
      region_id: ''
    }
    filteredRegions.value = regions.value
  }
  
  showModal.value = true
}

// Fermer le modal
function closeModal() {
  showModal.value = false
  selectedPays.value = null
  selectedContinent.value = ''
  selectedCountryFromAPI.value = ''
  isAutoFilled.value = false
  form.value = {
    nom: '',
    code_iso: '',
    indicatif: '',
    continent: '',
    region_id: ''
  }
}

// Enregistrer un pays
async function savePays() {
  try {
    const config = useRuntimeConfig()
    const token = authStore.token
    const url = selectedPays.value
      ? `${config.public.apiBase}/pays-crud/${selectedPays.value.id}`
      : `${config.public.apiBase}/pays-crud`
    
    const method = selectedPays.value ? 'PUT' : 'POST'
    
    await $fetch(url, {
      method,
      body: form.value,
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
      text: selectedPays.value ? 'Pays modifié avec succès' : 'Pays ajouté avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeModal()
    await loadPays()
  } catch (error) {
    console.error('Erreur lors de l\'enregistrement:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: 'Une erreur est survenue lors de l\'enregistrement'
    })
  }
}

// Supprimer un pays
async function deletePays(id) {
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
      await $fetch(`${config.public.apiBase}/pays-crud/${id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'Le pays a été supprimé avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      await loadPays()
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
  await Promise.all([loadPays(), loadRegions()])
})

// Ouvrir le modal région
function openRegionModal() {
  if (!form.value.continent) {
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'warning',
      title: 'Attention',
      text: 'Veuillez d\'abord sélectionner un continent'
    })
    return
  }
  
  if (!form.value.nom) {
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'warning',
      title: 'Attention',
      text: 'Veuillez d\'abord renseigner le nom du pays'
    })
    return
  }
  
  regionForm.value = {
    nom: '',
    type: ''
  }
  showRegionModal.value = true
}

// Fermer le modal région
function closeRegionModal() {
  showRegionModal.value = false
  regionForm.value = {
    nom: '',
    type: ''
  }
}

// Enregistrer une nouvelle région ou province
async function saveRegion() {
  try {
    const config = useRuntimeConfig()
    const token = authStore.token
    
    const regionData = {
      nom: regionForm.value.nom,
      type: regionForm.value.type,
      // Si c'est une région, on associe au continent
      // Si c'est une province, on associe au pays
      continent: regionForm.value.type === 'region' ? form.value.continent : null,
      pays_nom: regionForm.value.type === 'province' ? form.value.nom : null
    }
    
    const response = await $fetch(`${config.public.apiBase}/regions-crud`, {
      method: 'POST',
      body: regionData,
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
      text: `${regionForm.value.type === 'province' ? 'Province' : 'Région'} créée avec succès`,
      timer: 2000,
      showConfirmButton: false
    })
    
    // Recharger les régions et sélectionner la nouvelle
    await loadRegions()
    filterRegionsByContinent()
    
    // Sélectionner automatiquement la région/province créée
    if (response.id) {
      form.value.region_id = response.id
    }
    
    closeRegionModal()
  } catch (error) {
    console.error('Erreur lors de la création:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: 'Une erreur est survenue lors de la création'
    })
  }
}
</script>
