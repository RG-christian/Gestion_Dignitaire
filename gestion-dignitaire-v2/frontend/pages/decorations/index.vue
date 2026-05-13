<template>
  <DashboardLayout>
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
      <h2 class="mb-6 text-3xl font-bold">Gestion des Décorations</h2>

      <!-- Filtres -->
      <div class="mb-6 flex gap-2">
        <input
          v-model="filters.search"
          @input="loadDecorations"
          type="text"
          placeholder="Rechercher (nom, type, niveau, grade)..."
          class="flex-grow border rounded px-3 py-2"
        >
      </div>

      <!-- Bouton Ajout -->
      <button
        @click="openModal()"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4"
      >
        Ajouter une décoration
      </button>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table v-if="paginatedDecorations.length > 0" class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">ID</th>
              <th class="px-4 py-2 text-left">Nom</th>
              <th class="px-4 py-2 text-left">Type</th>
              <th class="px-4 py-2 text-left">Niveau</th>
              <th class="px-4 py-2 text-left">Grade</th>
              <th class="px-4 py-2 text-left">Date</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="deco in paginatedDecorations" :key="deco.id">
              <td class="px-4 py-2">{{ deco.id }}</td>
              <td class="px-4 py-2">{{ deco.nom }}</td>
              <td class="px-4 py-2">{{ deco.type || 'N/A' }}</td>
              <td class="px-4 py-2">{{ deco.niveau || 'N/A' }}</td>
              <td class="px-4 py-2">{{ deco.grade || 'N/A' }}</td>
              <td class="px-4 py-2">{{ formatDate(deco.date_obtention) }}</td>
              <td class="px-4 py-2 flex justify-center gap-2">
                <button
                  @click="openDetailModal(deco)"
                  class="bg-sky-500 hover:bg-sky-600 text-white rounded px-2 py-1 text-xs"
                >
                  Détail
                </button>
                <button
                  @click="openModal(deco)"
                  class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                >
                  Modifier
                </button>
                <button
                  @click="deleteDecoration(deco.id)"
                  class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center py-8 text-gray-500">Aucune décoration enregistrée.</p>

        <!-- Pagination -->
        <Pagination
          v-if="decorations.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="decorations.length"
          @update:current-page="currentPage = $event"
        />
      </div>
    </section>

    <!-- Modal Ajout/Modification -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
        <h4 class="text-lg font-bold mb-4">{{ selectedDecoration ? 'Modifier' : 'Ajouter' }} une décoration</h4>
        <form @submit.prevent="saveDecoration" class="flex flex-col gap-4">
          <input v-model="form.nom" placeholder="Nom de la décoration" required class="border rounded px-2 py-1">
          <input v-model="form.type" placeholder="Type" class="border rounded px-2 py-1">
          <input v-model="form.niveau" placeholder="Niveau" class="border rounded px-2 py-1">
          <input v-model="form.grade" placeholder="Grade" class="border rounded px-2 py-1">
          <input v-model="form.date_obtention" type="date" placeholder="Date d'obtention" class="border rounded px-2 py-1">
          <input v-model="form.autorite" placeholder="Autorité délivrant" class="border rounded px-2 py-1">
          <textarea v-model="form.motif" placeholder="Motif" rows="2" class="border rounded px-2 py-1"></textarea>
          <textarea v-model="form.description" placeholder="Description" rows="2" class="border rounded px-2 py-1"></textarea>
          <input v-model="form.fichier_attestation" placeholder="Fichier attestation" class="border rounded px-2 py-1">

          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
            {{ selectedDecoration ? 'Modifier' : 'Enregistrer' }}
          </button>
        </form>
        <span @click="closeModal" class="absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-2xl cursor-pointer">&times;</span>
      </div>
    </div>

    <!-- Modal Détail -->
    <div
      v-if="showDetailModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeDetailModal"
    >
      <div class="bg-white rounded-xl shadow-lg w-full max-w-xl p-6 relative">
        <h4 class="text-lg font-bold mb-4">Détail de la Décoration</h4>
        <div v-if="selectedDetail" class="space-y-2">
          <p><strong>Nom :</strong> {{ selectedDetail.nom }}</p>
          <p><strong>Type :</strong> {{ selectedDetail.type || 'N/A' }}</p>
          <p><strong>Niveau :</strong> {{ selectedDetail.niveau || 'N/A' }}</p>
          <p><strong>Grade :</strong> {{ selectedDetail.grade || 'N/A' }}</p>
          <p><strong>Date d'obtention :</strong> {{ formatDate(selectedDetail.date_obtention) }}</p>
          <p><strong>Autorité :</strong> {{ selectedDetail.autorite || 'N/A' }}</p>
          <p><strong>Motif :</strong> {{ selectedDetail.motif || 'N/A' }}</p>
          <p><strong>Description :</strong> {{ selectedDetail.description || 'N/A' }}</p>
          <p><strong>Fichier attestation :</strong> {{ selectedDetail.fichier_attestation || 'N/A' }}</p>
        </div>
        <span @click="closeDetailModal" class="absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-2xl cursor-pointer">&times;</span>
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

const decorations = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedDecoration = ref(null)
const selectedDetail = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: ''
})

const form = reactive({
  nom: '',
  type: '',
  niveau: '',
  grade: '',
  date_obtention: '',
  autorite: '',
  motif: '',
  description: '',
  fichier_attestation: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(decorations.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, decorations.value.length))
const paginatedDecorations = computed(() => {
  return decorations.value.slice(startIndex.value, endIndex.value)
})

function formatDate(dateStr) {
  if (!dateStr) return 'N/A'
  const date = new Date(dateStr)
  return date.toLocaleDateString('fr-FR')
}

async function loadDecorations() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)

    const response = await $fetch(`${config.public.apiBase}/decorations?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    decorations.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (error) {
    console.error('Erreur chargement décorations:', error)
    decorations.value = []
  } finally {
    loading.value = false
  }
}

function openModal(decoration = null) {
  selectedDecoration.value = decoration
  if (decoration) {
    form.nom = decoration.nom
    form.type = decoration.type || ''
    form.niveau = decoration.niveau || ''
    form.grade = decoration.grade || ''
    form.date_obtention = decoration.date_obtention || ''
    form.autorite = decoration.autorite || ''
    form.motif = decoration.motif || ''
    form.description = decoration.description || ''
    form.fichier_attestation = decoration.fichier_attestation || ''
  } else {
    form.nom = ''
    form.type = ''
    form.niveau = ''
    form.grade = ''
    form.date_obtention = ''
    form.autorite = ''
    form.motif = ''
    form.description = ''
    form.fichier_attestation = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedDecoration.value = null
}

function openDetailModal(decoration) {
  selectedDetail.value = decoration
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveDecoration() {
  try {
    if (selectedDecoration.value) {
      await $fetch(`${config.public.apiBase}/decorations/${selectedDecoration.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/decorations`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    closeModal()
    loadDecorations()
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deleteDecoration(id) {
  if (confirm('Supprimer cette décoration ?')) {
    try {
      await $fetch(`${config.public.apiBase}/decorations/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      loadDecorations()
    } catch (error) {
      console.error('Erreur:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(() => {
  loadDecorations()
})
</script>
