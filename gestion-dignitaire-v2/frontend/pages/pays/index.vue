<template>
  <DashboardLayout>
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
      <h2 class="mb-6 text-3xl font-bold">Gestion des Pays</h2>

      <div class="mb-6 flex gap-2">
        <input
          v-model="filters.search"
          @input="loadPays"
          type="text"
          placeholder="Rechercher (nom, code ISO, continent)..."
          class="flex-grow border rounded px-3 py-2"
        >
      </div>

      <button
        @click="openModal()"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4"
      >
        Ajouter un pays
      </button>

      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table v-if="paginatedPays.length > 0" class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">ID</th>
              <th class="px-4 py-2 text-left">Code ISO</th>
              <th class="px-4 py-2 text-left">Indicatif</th>
              <th class="px-4 py-2 text-left">Continent</th>
              <th class="px-4 py-2 text-left">Pays</th>
              <th class="px-4 py-2 text-left">Drapeau</th>
              <th class="px-4 py-2 text-left">Région</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="p in paginatedPays" :key="p.id">
              <td class="px-4 py-2">{{ p.id }}</td>
              <td class="px-4 py-2">{{ p.code_iso || 'N/A' }}</td>
              <td class="px-4 py-2">{{ p.indicatif || 'N/A' }}</td>
              <td class="px-4 py-2">{{ p.continent || 'N/A' }}</td>
              <td class="px-4 py-2">{{ p.nom }}</td>
              <td class="px-4 py-2">
                <img 
                  v-if="p.code_iso" 
                  :src="`https://flagcdn.com/w20/${p.code_iso.toLowerCase()}.png`" 
                  :alt="`${p.nom} flag`"
                  class="inline-block"
                  @error="$event.target.style.display='none'"
                >
                <span v-else class="text-gray-400">N/A</span>
              </td>
              <td class="px-4 py-2">{{ p.region_nom || 'N/A' }}</td>
              <td class="px-4 py-2 flex justify-center gap-2">
                <button
                  @click="openModal(p)"
                  class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                >
                  Modifier
                </button>
                <button
                  @click="deletePays(p.id)"
                  class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center py-8 text-gray-500">Aucun pays enregistré.</p>

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

    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
        <h4 class="text-lg font-bold mb-4">{{ selectedPays ? 'Modifier' : 'Ajouter' }} un pays</h4>
        <form @submit.prevent="savePays" class="flex flex-col gap-4">
          <input v-model="form.nom" placeholder="Nom du pays" required class="border rounded px-2 py-1">
          <input v-model="form.code_iso" placeholder="Code ISO (ex: FR, US)" class="border rounded px-2 py-1" maxlength="2">
          <input v-model="form.indicatif" placeholder="Indicatif téléphonique (ex: +33)" class="border rounded px-2 py-1">
          <input v-model="form.continent" placeholder="Continent" class="border rounded px-2 py-1">
          <select v-model="form.region_id" class="border rounded px-2 py-1">
            <option value="">Région</option>
            <option v-for="region in regions" :key="region.id" :value="region.id">
              {{ region.nom }}
            </option>
          </select>
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
            {{ selectedPays ? 'Modifier' : 'Enregistrer' }}
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

const pays = ref([])
const regions = ref([])
const loading = ref(true)
const showModal = ref(false)
const selectedPays = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: ''
})

const form = reactive({
  nom: '',
  code_iso: '',
  indicatif: '',
  continent: '',
  region_id: ''
})

// Pagination calculée
const totalPages = computed(() => Math.ceil(pays.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, pays.value.length))
const paginatedPays = computed(() => {
  return pays.value.slice(startIndex.value, endIndex.value)
})

async function loadPays() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)

    const response = await $fetch(`${config.public.apiBase}/pays-crud?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    pays.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (error) {
    console.error('Erreur:', error)
    pays.value = []
  } finally {
    loading.value = false
  }
}

function openModal(p = null) {
  selectedPays.value = p
  if (p) {
    form.nom = p.nom
    form.code_iso = p.code_iso || ''
    form.indicatif = p.indicatif || ''
    form.continent = p.continent || ''
    form.region_id = p.region_id || ''
  } else {
    form.nom = ''
    form.code_iso = ''
    form.indicatif = ''
    form.continent = ''
    form.region_id = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedPays.value = null
}

async function savePays() {
  try {
    if (selectedPays.value) {
      await $fetch(`${config.public.apiBase}/pays-crud/${selectedPays.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/pays-crud`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    closeModal()
    loadPays()
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deletePays(id) {
  if (confirm('Supprimer ce pays ?')) {
    try {
      await $fetch(`${config.public.apiBase}/pays-crud/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      loadPays()
    } catch (error) {
      console.error('Erreur:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(async () => {
  regions.value = await referentiels.getRegions()
  await loadPays()
})
</script>
