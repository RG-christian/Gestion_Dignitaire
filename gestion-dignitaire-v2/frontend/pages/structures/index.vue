<template>
  <DashboardLayout>
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
      <h2 class="mb-6 text-3xl font-bold">Gestion des Structures</h2>

      <div class="mb-6 flex gap-2">
        <input
          v-model="filters.search"
          @input="loadStructures"
          type="text"
          placeholder="Rechercher une structure..."
          class="flex-grow border rounded px-3 py-2"
        >
      </div>

      <button
        @click="openModal()"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4"
      >
        Ajouter une structure
      </button>

      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table v-if="paginatedStructures.length > 0" class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">ID</th>
              <th class="px-4 py-2 text-left">Nom</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="s in paginatedStructures" :key="s.id">
              <td class="px-4 py-2">{{ s.id }}</td>
              <td class="px-4 py-2">{{ s.nom }}</td>
              <td class="px-4 py-2 flex justify-center gap-2">
                <button
                  @click="openModal(s)"
                  class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                >
                  Modifier
                </button>
                <button
                  @click="deleteStructure(s.id)"
                  class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center py-8 text-gray-500">Aucune structure enregistrée.</p>

        <!-- Pagination -->
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

    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
        <h4 class="text-lg font-bold mb-4">{{ selectedStructure ? 'Modifier' : 'Ajouter' }} une structure</h4>
        <form @submit.prevent="saveStructure" class="flex flex-col gap-4">
          <input v-model="form.nom" placeholder="Nom de la structure" required class="border rounded px-2 py-1">
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
            {{ selectedStructure ? 'Modifier' : 'Enregistrer' }}
          </button>
        </form>
        <span @click="closeModal" class="absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-2xl cursor-pointer">&times;</span>
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

const structures = ref([])
const loading = ref(true)
const showModal = ref(false)
const selectedStructure = ref(null)
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
    console.error('Erreur:', error)
    structures.value = []
  } finally {
    loading.value = false
  }
}

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
    closeModal()
    loadStructures()
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deleteStructure(id) {
  if (confirm('Supprimer cette structure ?')) {
    try {
      await $fetch(`${config.public.apiBase}/structures/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      loadStructures()
    } catch (error) {
      console.error('Erreur:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(() => {
  loadStructures()
})
</script>
