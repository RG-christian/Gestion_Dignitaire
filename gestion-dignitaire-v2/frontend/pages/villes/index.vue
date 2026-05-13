<template>
  <DashboardLayout>
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
      <h2 class="mb-6 text-3xl font-bold">Gestion des Villes</h2>

      <div class="mb-6 flex gap-2">
        <input
          v-model="filters.search"
          @input="loadVilles"
          type="text"
          placeholder="Rechercher (ville, pays)..."
          class="flex-grow border rounded px-3 py-2"
        >
        <select
          v-model="filters.pays_id"
          @change="loadVilles"
          class="border rounded px-3 py-2"
        >
          <option value="">Tous les pays</option>
          <option v-for="p in pays" :key="p.id" :value="p.id">
            {{ p.nom }}
          </option>
        </select>
      </div>

      <button
        @click="openModal()"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4"
      >
        Ajouter une ville
      </button>

      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table v-if="paginatedVilles.length > 0" class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Nom</th>
              <th class="px-4 py-2 text-left">Pays</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="v in paginatedVilles" :key="v.id">
              <td class="px-4 py-2">{{ v.nom }}</td>
              <td class="px-4 py-2">{{ v.pays_nom || 'N/A' }}</td>
              <td class="px-4 py-2 flex justify-center gap-2">
                <button
                  @click="openModal(v)"
                  class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                >
                  Modifier
                </button>
                <button
                  @click="deleteVille(v.id)"
                  class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center py-8 text-gray-500">Aucune ville enregistrée.</p>

        <!-- Pagination -->
        <Pagination
          v-if="villes.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="villes.length"
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
        <h4 class="text-lg font-bold mb-4">{{ selectedVille ? 'Modifier' : 'Ajouter' }} une ville</h4>
        <form @submit.prevent="saveVille" class="flex flex-col gap-4">
          <input v-model="form.nom" placeholder="Nom de la ville" required class="border rounded px-2 py-1">
          <select v-model="form.pays_id" class="border rounded px-2 py-1">
            <option value="">Pays</option>
            <option v-for="p in pays" :key="p.id" :value="p.id">
              {{ p.nom }}
            </option>
          </select>
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
            {{ selectedVille ? 'Modifier' : 'Enregistrer' }}
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
const referentiels = useReferentiels()

const villes = ref([])
const pays = ref([])
const loading = ref(true)
const showModal = ref(false)
const selectedVille = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: '',
  pays_id: ''
})

const form = reactive({
  nom: '',
  pays_id: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(villes.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, villes.value.length))
const paginatedVilles = computed(() => {
  return villes.value.slice(startIndex.value, endIndex.value)
})

async function loadVilles() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.pays_id) params.append('pays_id', filters.pays_id)

    const response = await $fetch(`${config.public.apiBase}/villes-crud?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    villes.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (error) {
    console.error('Erreur:', error)
    villes.value = []
  } finally {
    loading.value = false
  }
}

function openModal(v = null) {
  selectedVille.value = v
  if (v) {
    form.nom = v.nom
    form.pays_id = v.pays_id || ''
  } else {
    form.nom = ''
    form.pays_id = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedVille.value = null
}

async function saveVille() {
  try {
    if (selectedVille.value) {
      await $fetch(`${config.public.apiBase}/villes-crud/${selectedVille.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/villes-crud`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    closeModal()
    loadVilles()
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deleteVille(id) {
  if (confirm('Supprimer cette ville ?')) {
    try {
      await $fetch(`${config.public.apiBase}/villes-crud/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      loadVilles()
    } catch (error) {
      console.error('Erreur:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(async () => {
  pays.value = await referentiels.getPays()
  await loadVilles()
})
</script>
