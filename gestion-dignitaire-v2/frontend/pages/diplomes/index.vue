<template>
  <DashboardLayout>
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
      <!-- En-tête avec titre et bouton -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <svg class="w-8 h-8 text-gabon-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
            Gestion des Diplômes
          </h2>
          <p class="text-gray-600 mt-1">{{ diplomes.length }} diplôme(s) enregistré(s)</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            @click="exportListe('pdf')"
            class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold px-4 py-3 rounded-lg whitespace-nowrap"
          >
            Exporter PDF
          </button>
          <button
            @click="exportListe('excel')"
            class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold px-4 py-3 rounded-lg whitespace-nowrap"
          >
            Exporter Excel
          </button>
          <button
            @click="openModal()"
            class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un diplôme
          </button>
        </div>
      </div>

      <!-- Filtres améliorés -->
      <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <SearchInput
              v-model="filters.search"
              placeholder="Rechercher par intitulé, établissement, année..."
              @update:modelValue="debouncedLoadDiplomes"
            />
          </div>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <select
              v-model="filters.dignitaire_id"
              @change="loadDiplomes"
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all appearance-none bg-white"
            >
              <option value="">Tous les dignitaires</option>
              <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                {{ dig.prenom }} {{ dig.nom }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loader -->
      <div v-if="loading" class="flex flex-col justify-center items-center py-20">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-gabon-green-600"></div>
        <p class="text-gray-500 mt-4 font-medium">Chargement des diplômes...</p>
      </div>

      <!-- Grille de cards -->
      <div v-else>
        <div v-if="paginatedDiplomes.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="dip in paginatedDiplomes"
            :key="dip.id"
            class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-gabon-green-200"
          >
            <!-- En-tête de la card avec gradient -->
            <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 p-4">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <h3 class="text-white font-bold text-lg mb-1 line-clamp-2">{{ dip.intitule }}</h3>
                  <p class="text-gabon-green-100 text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ dip.dignitaire_nom }}
                  </p>
                </div>
                <span class="bg-gabon-yellow-500 text-gray-900 text-xs font-bold px-3 py-1 rounded-full">
                  {{ dip.annee }}
                </span>
              </div>
            </div>

            <!-- Contenu de la card -->
            <div class="p-4 space-y-3">
              <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-gabon-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <div class="flex-1 min-w-0">
                  <p class="text-xs text-gray-500 uppercase tracking-wide">Établissement</p>
                  <p class="text-sm font-medium text-gray-800 truncate">{{ dip.etablissement_nom || 'Non spécifié' }}</p>
                </div>
              </div>

              <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-gabon-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div class="flex-1 min-w-0">
                  <p class="text-xs text-gray-500 uppercase tracking-wide">Domaine</p>
                  <p class="text-sm font-medium text-gray-800 truncate">{{ dip.domaine_nom || 'Non spécifié' }}</p>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="px-4 pb-4 flex gap-2">
              <button
                @click="openDetailModal(dip)"
                class="flex-1 bg-sky-50 hover:bg-sky-100 text-sky-700 font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center gap-1"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Détail
              </button>
              <button
                @click="openModal(dip)"
                class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-2 px-3 rounded-lg transition-colors text-sm flex items-center justify-center gap-1"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
              </button>
              <button
                @click="deleteDiplome(dip.id)"
                class="bg-red-50 hover:bg-red-100 text-red-700 font-medium py-2 px-3 rounded-lg transition-colors text-sm"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Message vide avec illustration -->
        <div v-else class="bg-white rounded-xl shadow-md p-12 text-center">
          <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
          </svg>
          <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucun diplôme enregistré</h3>
          <p class="text-gray-500 mb-6">Commencez par ajouter un diplôme pour un dignitaire</p>
          <button
            @click="openModal()"
            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-3 rounded-lg inline-flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter le premier diplôme
          </button>
        </div>

        <!-- Pagination -->
        <div v-if="diplomes.length > 0" class="mt-8">
          <Pagination
            :current-page="currentPage"
            :total-pages="totalPages"
            :start-index="startIndex"
            :end-index="endIndex"
            :total="diplomes.length"
            @update:current-page="currentPage = $event"
          />
        </div>
      </div>
    </section>

    <!-- Modal Ajout/Modification amélioré -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <!-- En-tête du modal -->
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
            </svg>
            {{ selectedDiplome ? 'Modifier le diplôme' : 'Ajouter un diplôme' }}
          </h4>
          <button @click="closeModal" class="text-white hover:text-gabon-green-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Contenu du modal -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
          <form @submit.prevent="saveDiplome" class="space-y-5">
            <!-- Dignitaire -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Dignitaire <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <select v-model="form.dignitaire_id" required class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent">
                  <option value="">Choisir un dignitaire</option>
                  <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                    {{ dig.prenom }} {{ dig.nom }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Intitulé -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Intitulé du diplôme <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.intitule"
                placeholder="Ex: Master en Informatique"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent"
              >
            </div>

            <!-- Grille 2 colonnes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Établissement -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Établissement</label>
                <select v-model="form.etablissement_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent">
                  <option value="">Sélectionner</option>
                  <option v-for="etab in etablissements" :key="etab.id" :value="etab.id">
                    {{ etab.nom }}
                  </option>
                </select>
              </div>

              <!-- Année -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Année d'obtention</label>
                <input
                  v-model="form.annee"
                  placeholder="Ex: 2020"
                  type="number"
                  min="1950"
                  max="2030"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent"
                >
              </div>

              <!-- Ville -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Ville</label>
                <select v-model="form.ville_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent">
                  <option value="">Sélectionner</option>
                  <option v-for="ville in villes" :key="ville.id" :value="ville.id">
                    {{ ville.nom }}
                  </option>
                </select>
              </div>

              <!-- Domaine -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Domaine</label>
                <select v-model="form.domaine_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent">
                  <option value="">Sélectionner</option>
                  <option v-for="dom in domaines" :key="dom.id" :value="dom.id">
                    {{ dom.nom }}
                  </option>
                </select>
              </div>

              <!-- Code -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Code</label>
                <input
                  v-model="form.code"
                  placeholder="Code du diplôme"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent"
                >
              </div>

              <!-- Type -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                <input
                  v-model="form.type"
                  placeholder="Ex: Master, Licence, Doctorat"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent"
                >
              </div>
            </div>

            <!-- Boutons -->
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-colors"
              >
                Annuler
              </button>
              <button
                type="submit"
                class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-lg hover:shadow-xl"
              >
                {{ selectedDiplome ? 'Mettre à jour' : 'Enregistrer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Détail amélioré -->
    <div
      v-if="showDetailModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeDetailModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <!-- En-tête du modal -->
        <div class="bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Détail du Diplôme
          </h4>
          <button @click="closeDetailModal" class="text-white hover:text-gabon-blue-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Contenu du modal -->
        <div v-if="selectedDetail" class="p-6">
          <div class="space-y-4">
            <!-- Intitulé -->
            <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-purple-500">
              <p class="text-xs text-purple-600 font-semibold uppercase tracking-wide mb-1">Intitulé</p>
              <p class="text-lg font-bold text-gray-800">{{ selectedDetail.intitule }}</p>
            </div>

            <!-- Grille d'informations -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Dignitaire</p>
                <p class="text-sm font-medium text-gray-800">{{ selectedDetail.dignitaire_nom }}</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Année</p>
                <p class="text-sm font-medium text-gray-800">{{ selectedDetail.annee || 'Non spécifié' }}</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Établissement</p>
                <p class="text-sm font-medium text-gray-800">{{ selectedDetail.etablissement_nom || 'Non spécifié' }}</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Ville</p>
                <p class="text-sm font-medium text-gray-800">{{ selectedDetail.ville_nom || 'Non spécifié' }}</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Domaine</p>
                <p class="text-sm font-medium text-gray-800">{{ selectedDetail.domaine_nom || 'Non spécifié' }}</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Type</p>
                <p class="text-sm font-medium text-gray-800">{{ selectedDetail.type || 'Non spécifié' }}</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Code</p>
                <p class="text-sm font-medium text-gray-800">{{ selectedDetail.code || 'Non spécifié' }}</p>
              </div>
            </div>
          </div>

          <!-- Bouton fermer -->
          <div class="mt-6">
            <button
              @click="closeDetailModal"
              class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-colors"
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
const fileDownload = useFileDownload()

async function exportListe(format: 'pdf' | 'excel') {
  try {
    await fileDownload.download('/diplomes-export', {
      search: filters.search,
      dignitaire_id: filters.dignitaire_id,
      format
    }, `diplomes.${format === 'excel' ? 'xlsx' : 'pdf'}`)
  } catch (error) {
    console.error('Erreur export diplômes:', error)
  }
}

const diplomes = ref([])
const dignitaires = ref([])
const etablissements = ref([])
const villes = ref([])
const domaines = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedDiplome = ref(null)
const selectedDetail = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: '',
  dignitaire_id: ''
})

const form = reactive({
  dignitaire_id: '',
  intitule: '',
  etablissement_id: '',
  annee: '',
  ville_id: '',
  domaine_id: '',
  code: '',
  type: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(diplomes.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, diplomes.value.length))
const paginatedDiplomes = computed(() => {
  return diplomes.value.slice(startIndex.value, endIndex.value)
})

async function loadDiplomes() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)

    console.log('Chargement diplomes depuis:', `${config.public.apiBase}/diplomes?${params.toString()}`)
    
    const response = await $fetch(`${config.public.apiBase}/diplomes?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    console.log('Réponse diplomes:', response)
    console.log('Type de réponse:', typeof response, Array.isArray(response))
    
    diplomes.value = Array.isArray(response) ? response : (response.data || [])
    console.log('Diplomes chargés:', diplomes.value.length)
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (error) {
    console.error('Erreur chargement diplomes:', error)
    diplomes.value = []
  } finally {
    loading.value = false
  }
}

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

async function loadEtablissements() {
  try {
    const response = await $fetch(`${config.public.apiBase}/etablissements`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    etablissements.value = response || []
  } catch (error) {
    console.error('Erreur:', error)
  }
}

// Version debouncée pour optimiser les requêtes AJAX
const debouncedLoadDiplomes = debounce(loadDiplomes, 500)

function openModal(diplome: any = null) {
  selectedDiplome.value = diplome
  if (diplome) {
    form.dignitaire_id = diplome.dignitaire_id
    form.intitule = diplome.intitule
    form.etablissement_id = diplome.etablissement_id || ''
    form.annee = diplome.annee || ''
    form.ville_id = diplome.ville_id || ''
    form.domaine_id = diplome.domaine_id || ''
    form.code = diplome.code || ''
    form.type = diplome.type || ''
  } else {
    form.dignitaire_id = ''
    form.intitule = ''
    form.etablissement_id = ''
    form.annee = ''
    form.ville_id = ''
    form.domaine_id = ''
    form.code = ''
    form.type = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedDiplome.value = null
}

function openDetailModal(diplome: any) {
  selectedDetail.value = diplome
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveDiplome() {
  try {
    if (selectedDiplome.value) {
      await $fetch(`${config.public.apiBase}/diplomes/${selectedDiplome.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/diplomes`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedDiplome.value ? 'Diplôme modifié avec succès' : 'Diplôme ajouté avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeModal()
    loadDiplomes()
  } catch (error) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: 'Une erreur est survenue lors de la sauvegarde'
    })
  }
}

async function deleteDiplome(id: number) {
  const { $swal } = useNuxtApp()
  const result = await $swal.fire({
    title: 'Êtes-vous sûr ?',
    text: 'Cette action supprimera définitivement ce diplôme',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#dc2626',
    confirmButtonText: 'Oui, supprimer',
    cancelButtonText: 'Annuler'
  })
  
  if (result.isConfirmed) {
    try {
      await $fetch(`${config.public.apiBase}/diplomes/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'Le diplôme a été supprimé avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      loadDiplomes()
    } catch (error) {
      console.error('Erreur:', error)
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Une erreur est survenue lors de la suppression'
      })
    }
  }
}

onMounted(async () => {
  villes.value = await referentiels.getVilles()
  domaines.value = await referentiels.getDomaines()
  await loadEtablissements()
  await loadDignitaires()
  await loadDiplomes()
})
</script>
