<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <!-- Header moderne avec gradient gabonais -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Expériences Professionnelles</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Gérer les parcours professionnels et expériences des dignitaires</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <!-- Barre de recherche et filtres -->
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <div class="flex-1 w-full">
            <SearchInput
              v-model="filters.search"
              placeholder="Rechercher (intitulé, dignitaire, structure)..."
              @update:modelValue="debouncedLoadExperiences"
            />
          </div>
          <div class="w-full md:w-64">
            <select
              v-model="filters.dignitaire_id"
              @change="loadExperiences"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
            >
              <option value="">Tous les dignitaires</option>
              <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                {{ dig.prenom }} {{ dig.nom }}
              </option>
            </select>
          </div>
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

      <!-- Table -->
      <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="paginatedExperiences.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Poste occupé</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dignitaire</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Structure</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date début</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date fin</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="exp in paginatedExperiences" :key="exp.id" class="hover:bg-gabon-green-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ exp.intitule }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ exp.dignitaire_nom }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ exp.structure_nom || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ formatDate(exp.date_debut) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="exp.date_fin" class="text-sm text-gray-700">{{ formatDate(exp.date_fin) }}</span>
                  <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">À ce jour</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="openDetailModal(exp)" class="inline-flex items-center gap-1 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Détail">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      Détail
                    </button>
                    <button @click="openModal(exp)" class="inline-flex items-center gap-1 bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Modifier">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Modifier
                    </button>
                    <button @click="deleteExperience(exp.id)" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Supprimer">
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <p class="mt-4 text-gray-500 text-lg">Aucune expérience enregistrée</p>
        </div>
        <Pagination
          v-if="experiences.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="experiences.length"
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            {{ selectedExperience ? 'Modifier' : 'Ajouter' }} une expérience
          </h4>
          <button @click="closeModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveExperience" class="p-6">
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
              <label class="block text-sm font-semibold text-gray-700 mb-2">Poste occupé <span class="text-red-500">*</span></label>
              <input v-model="form.intitule" type="text" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Ex: Directeur Général">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Institution</label>
              <select v-model="form.structure_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Sélectionner une institution --</option>
                <option v-for="structure in structures" :key="structure.id" :value="structure.id">
                  {{ structure.nom }}
                </option>
              </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de début</label>
                <input v-model="form.date_debut" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
                <input v-model="form.date_fin" type="date" :min="minDateFin(form.date_debut)" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>
          </div>
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
              {{ selectedExperience ? 'Modifier' : 'Enregistrer' }}
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
            Détail de l'Expérience
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
              <p class="text-sm font-semibold text-gray-500 mb-1">Poste occupé</p>
              <p class="text-lg font-bold text-gray-900">{{ selectedDetail.intitule }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Dignitaire</p>
              <p class="text-gray-900">{{ selectedDetail.dignitaire_nom }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Institution</p>
              <p class="text-gray-900">{{ selectedDetail.structure_nom || 'N/A' }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Date de début</p>
                <p class="text-gray-900">{{ formatDate(selectedDetail.date_debut) }}</p>
              </div>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Date de fin</p>
                <p class="text-gray-900">{{ selectedDetail.date_fin ? formatDate(selectedDetail.date_fin) : 'À ce jour' }}</p>
              </div>
            </div>
          </div>
          <div class="mt-6 pt-4 border-t">
            <button @click="closeDetailModal" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Fermer</button>
          </div>
        </div>
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
const referentiels = useReferentiels()
const { debounce } = useDebounce()
const { minDateFin } = useDateHelpers()

const experiences = ref([])
const dignitaires = ref([])
const structures = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedExperience = ref(null)
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
  structure_id: '',
  date_debut: '',
  date_fin: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(experiences.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, experiences.value.length))
const paginatedExperiences = computed(() => {
  return experiences.value.slice(startIndex.value, endIndex.value)
})

function formatDate(dateStr) {
  if (!dateStr) return 'N/A'
  const date = new Date(dateStr)
  return date.toLocaleDateString('fr-FR')
}

async function loadExperiences() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)
    
    const response = await $fetch(`${config.public.apiBase}/experiences?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    experiences.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1
  } catch (error) {
    console.error('Erreur chargement expériences:', error)
    experiences.value = []
  } finally {
    loading.value = false
  }
}

// Version debouncée pour optimiser les requêtes AJAX
const debouncedLoadExperiences = debounce(loadExperiences, 500)

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

function openModal(experience = null) {
  selectedExperience.value = experience
  if (experience) {
    form.dignitaire_id = experience.dignitaire_id
    form.intitule = experience.intitule
    form.structure_id = experience.structure_id || ''
    form.date_debut = experience.date_debut || ''
    form.date_fin = experience.date_fin || ''
  } else {
    form.dignitaire_id = ''
    form.intitule = ''
    form.structure_id = ''
    form.date_debut = ''
    form.date_fin = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedExperience.value = null
}

function openDetailModal(experience) {
  selectedDetail.value = experience
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveExperience() {
  try {
    if (selectedExperience.value) {
      await $fetch(`${config.public.apiBase}/experiences/${selectedExperience.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/experiences`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedExperience.value ? 'Expérience modifiée avec succès' : 'Expérience ajoutée avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeModal()
    loadExperiences()
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

async function deleteExperience(id) {
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
      await $fetch(`${config.public.apiBase}/experiences/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'L\'expérience a été supprimée avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      loadExperiences()
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
  structures.value = await referentiels.getStructures()
  await loadDignitaires()
  await loadExperiences()
})
</script>
