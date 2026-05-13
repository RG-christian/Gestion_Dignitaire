<template>
  <DashboardLayout>
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
      <h2 class="mb-6 text-3xl font-bold">Gestion des Langues Parlées</h2>

      <!-- Filtres -->
      <div class="mb-6 flex gap-2">
        <input
          v-model="filters.search"
          @input="loadLanguesParlees"
          type="text"
          placeholder="Rechercher (langue, dignitaire, niveau)..."
          class="flex-grow border rounded px-3 py-2"
        >
        <select
          v-model="filters.dignitaire_id"
          @change="loadLanguesParlees"
          class="border rounded px-3 py-2"
        >
          <option value="">Tous les dignitaires</option>
          <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
            {{ dig.prenom }} {{ dig.nom }}
          </option>
        </select>
      </div>

      <!-- Bouton Ajout -->
      <button
        @click="openModal()"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4"
      >
        Ajouter une langue parlée
      </button>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table v-if="paginatedLanguesParlees.length > 0" class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Dignitaire</th>
              <th class="px-4 py-2 text-left">Langue</th>
              <th class="px-4 py-2 text-left">Niveau</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="lp in paginatedLanguesParlees" :key="lp.id">
              <td class="px-4 py-2">{{ lp.dignitaire_nom }}</td>
              <td class="px-4 py-2">{{ lp.langue_nom }}</td>
              <td class="px-4 py-2">{{ lp.niveau || 'N/A' }}</td>
              <td class="px-4 py-2 flex justify-center gap-2">
                <button
                  @click="openDetailModal(lp)"
                  class="bg-sky-500 hover:bg-sky-600 text-white rounded px-2 py-1 text-xs"
                >
                  Détail
                </button>
                <button
                  @click="openModal(lp)"
                  class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                >
                  Modifier
                </button>
                <button
                  @click="deleteLangueParlee(lp.id)"
                  class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center py-8 text-gray-500">Aucune langue parlée enregistrée.</p>

        <!-- Pagination -->
        <Pagination
          v-if="languesParlees.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="languesParlees.length"
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
        <h4 class="text-lg font-bold mb-4">{{ selectedLangueParlee ? 'Modifier' : 'Ajouter' }} une langue parlée</h4>
        <form @submit.prevent="saveLangueParlee" class="flex flex-col gap-4">
          <select v-model="form.dignitaire_id" required class="border rounded px-2 py-1">
            <option value="">Choisir un dignitaire</option>
            <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
              {{ dig.prenom }} {{ dig.nom }}
            </option>
          </select>

          <select v-model="form.langue_id" required class="border rounded px-2 py-1">
            <option value="">Choisir une langue</option>
            <option v-for="langue in langues" :key="langue.id" :value="langue.id">
              {{ langue.nom }}
            </option>
          </select>

          <input v-model="form.niveau" placeholder="Niveau (ex: Courant, Moyen, Débutant)" class="border rounded px-2 py-1">

          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
            {{ selectedLangueParlee ? 'Modifier' : 'Enregistrer' }}
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
        <h4 class="text-lg font-bold mb-4">Détail de la Langue Parlée</h4>
        <div v-if="selectedDetail" class="space-y-2">
          <p><strong>Dignitaire :</strong> {{ selectedDetail.dignitaire_nom }}</p>
          <p><strong>Langue :</strong> {{ selectedDetail.langue_nom }}</p>
          <p><strong>Niveau :</strong> {{ selectedDetail.niveau || 'N/A' }}</p>
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
const referentiels = useReferentiels()

const languesParlees = ref([])
const dignitaires = ref([])
const langues = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedLangueParlee = ref(null)
const selectedDetail = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: '',
  dignitaire_id: ''
})

const form = reactive({
  dignitaire_id: '',
  langue_id: '',
  niveau: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(languesParlees.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, languesParlees.value.length))
const paginatedLanguesParlees = computed(() => {
  return languesParlees.value.slice(startIndex.value, endIndex.value)
})

async function loadLanguesParlees() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)

    console.log('Chargement langues parlées depuis:', `${config.public.apiBase}/langues-parlees?${params.toString()}`)
    
    const response = await $fetch(`${config.public.apiBase}/langues-parlees?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    console.log('Réponse langues parlées:', response)
    languesParlees.value = Array.isArray(response) ? response : (response.data || [])
    console.log('Langues parlées chargées:', languesParlees.value.length)
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (error) {
    console.error('Erreur chargement langues parlées:', error)
    languesParlees.value = []
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

function openModal(langueParlee = null) {
  selectedLangueParlee.value = langueParlee
  if (langueParlee) {
    form.dignitaire_id = langueParlee.dignitaire_id
    form.langue_id = langueParlee.langue_id
    form.niveau = langueParlee.niveau || ''
  } else {
    form.dignitaire_id = ''
    form.langue_id = ''
    form.niveau = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedLangueParlee.value = null
}

function openDetailModal(langueParlee) {
  selectedDetail.value = langueParlee
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveLangueParlee() {
  try {
    if (selectedLangueParlee.value) {
      await $fetch(`${config.public.apiBase}/langues-parlees/${selectedLangueParlee.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/langues-parlees`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    closeModal()
    loadLanguesParlees()
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deleteLangueParlee(id) {
  if (confirm('Supprimer cette langue parlée ?')) {
    try {
      await $fetch(`${config.public.apiBase}/langues-parlees/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      loadLanguesParlees()
    } catch (error) {
      console.error('Erreur:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(async () => {
  langues.value = await referentiels.getLangues()
  await loadDignitaires()
  await loadLanguesParlees()
})
</script>
