<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <!-- Header moderne avec gradient gabonais -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Structures</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Gérer les structures organisationnelles et administratives</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <!-- Barre de recherche et bouton -->
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <div class="flex-1 w-full">
            <SearchInput
              v-model="filters.search"
              placeholder="Rechercher une structure..."
              @update:modelValue="debouncedLoadStructures"
            />
          </div>
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
        <div v-if="paginatedStructures.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nom de la Structure</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="s in paginatedStructures" :key="s.id" class="hover:bg-gabon-green-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ s.id }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ s.nom }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="openDetailModal(s)" class="inline-flex items-center gap-1 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Détail">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      Détail
                    </button>
                    <button @click="openModal(s)" class="inline-flex items-center gap-1 bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Modifier">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Modifier
                    </button>
                    <button @click="deleteStructure(s.id)" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Supprimer">
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
          <p class="mt-4 text-gray-500 text-lg">Aucune structure enregistrée</p>
        </div>
        <Pagination
          v-if="structures.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="structures.length"
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            {{ selectedStructure ? 'Modifier' : 'Ajouter' }} une structure
          </h4>
          <button @click="closeModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveStructure" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nom de la structure <span class="text-red-500">*</span></label>
              <input v-model="form.nom" type="text" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Ex: Ministère de l'Intérieur">
            </div>
          </div>
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
              {{ selectedStructure ? 'Modifier' : 'Enregistrer' }}
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
            Détail de la Structure
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
              <p class="text-sm font-semibold text-gray-500 mb-1">ID</p>
              <p class="text-lg font-bold text-gray-900">{{ selectedDetail.id }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Nom de la structure</p>
              <p class="text-lg font-bold text-gray-900">{{ selectedDetail.nom }}</p>
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
const { debounce } = useDebounce()
const fileDownload = useFileDownload()

async function exportListe(format) {
  try {
    await fileDownload.download('/structures-export', {
      search: filters.search,
      format
    }, `structures.${format === 'excel' ? 'xlsx' : 'pdf'}`)
  } catch (error) {
    console.error('Erreur export structures:', error)
  }
}

const structures = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedStructure = ref(null)
const selectedDetail = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: ''
})

const form = reactive({
  nom: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(structures.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, structures.value.length))
const paginatedStructures = computed(() => {
  return structures.value.slice(startIndex.value, endIndex.value)
})

async function loadStructures() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)

    const response = await $fetch(`${config.public.apiBase}/structures?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    structures.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1
  } catch (error) {
    console.error('Erreur chargement structures:', error)
    structures.value = []
  } finally {
    loading.value = false
  }
}

// Version debouncée pour optimiser les requêtes AJAX
const debouncedLoadStructures = debounce(loadStructures, 500)

function openModal(s = null) {
  selectedStructure.value = s
  if (s) {
    form.nom = s.nom
  } else {
    form.nom = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedStructure.value = null
}

function openDetailModal(structure) {
  selectedDetail.value = structure
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveStructure() {
  try {
    if (selectedStructure.value) {
      await $fetch(`${config.public.apiBase}/structures/${selectedStructure.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/structures`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedStructure.value ? 'Structure modifiée avec succès' : 'Structure ajoutée avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeModal()
    loadStructures()
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

async function deleteStructure(id) {
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
      await $fetch(`${config.public.apiBase}/structures/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'La structure a été supprimée avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      loadStructures()
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

onMounted(() => {
  loadStructures()
})
</script>
