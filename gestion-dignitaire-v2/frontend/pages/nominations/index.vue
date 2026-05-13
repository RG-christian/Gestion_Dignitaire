<template>
  <DashboardLayout>
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
      <h2 class="mb-6 text-3xl font-bold">Gestion des Nominations</h2>

      <!-- Filtres -->
      <div class="mb-6 flex gap-2">
        <input
          v-model="filters.search"
          @input="loadNominations"
          type="text"
          placeholder="Rechercher (fonction, dignitaire, entité)..."
          class="flex-grow border rounded px-3 py-2"
        >
        <select
          v-model="filters.dignitaire_id"
          @change="loadNominations"
          class="border rounded px-3 py-2"
        >
          <option value="">Tous les dignitaires</option>
          <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
            {{ dig.prenom }} {{ dig.nom }}
          </option>
        </select>
        <select
          v-model="filters.entite_id"
          @change="loadNominations"
          class="border rounded px-3 py-2"
        >
          <option value="">Toutes les entités</option>
          <option v-for="entite in entites" :key="entite.id" :value="entite.id">
            {{ entite.nom }}
          </option>
        </select>
      </div>

      <!-- Bouton Ajout -->
      <button
        @click="openModal()"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4"
      >
        Ajouter une nomination
      </button>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table v-if="nominations.length > 0" class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Dignitaire</th>
              <th class="px-4 py-2 text-left">Entité</th>
              <th class="px-4 py-2 text-left">Poste</th>
              <th class="px-4 py-2 text-left">Fonction</th>
              <th class="px-4 py-2 text-left">Date début</th>
              <th class="px-4 py-2 text-left">Date fin</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="nom in paginatedNominations" :key="nom.id">
              <td class="px-4 py-2">{{ nom.dignitaire_nom }}</td>
              <td class="px-4 py-2">{{ nom.entite_nom || 'N/A' }}</td>
              <td class="px-4 py-2">{{ nom.fonction || nom.poste_nom || 'N/A' }}</td>
              <td class="px-4 py-2">{{ nom.fonction || 'N/A' }}</td>
              <td class="px-4 py-2">{{ formatDate(nom.date_debut) }}</td>
              <td class="px-4 py-2">{{ nom.date_fin ? formatDate(nom.date_fin) : 'En cours' }}</td>
              <td class="px-4 py-2 flex justify-center gap-2">
                <button
                  @click="openDetailModal(nom)"
                  class="bg-sky-500 hover:bg-sky-600 text-white rounded px-2 py-1 text-xs"
                >
                  Détail
                </button>
                <button
                  @click="openModal(nom)"
                  class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                >
                  Modifier
                </button>
                <button
                  @click="deleteNomination(nom.id)"
                  class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center py-8 text-gray-500">Aucune nomination enregistrée.</p>

        <!-- Pagination -->
        <Pagination
          v-if="nominations.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="nominations.length"
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
        <h4 class="text-lg font-bold mb-4">{{ selectedNomination ? 'Modifier' : 'Ajouter' }} une nomination</h4>
        <form @submit.prevent="saveNomination" class="flex flex-col gap-4">
          <select v-model="form.dignitaire_id" required class="border rounded px-2 py-1">
            <option value="">Choisir un dignitaire</option>
            <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
              {{ dig.prenom }} {{ dig.nom }}
            </option>
          </select>

          <select v-model="form.entite_id" class="border rounded px-2 py-1">
            <option value="">Entité</option>
            <option v-for="entite in entites" :key="entite.id" :value="entite.id">
              {{ entite.nom }}
            </option>
          </select>

          <select v-model="form.poste_id" class="border rounded px-2 py-1">
            <option value="">Poste</option>
            <option v-for="poste in postes" :key="poste.id" :value="poste.id">
              {{ poste.intitule }}
            </option>
          </select>

          <input v-model="form.fonction" placeholder="Fonction" class="border rounded px-2 py-1">
          <input v-model="form.numero_decret" placeholder="Numéro de décret" class="border rounded px-2 py-1">
          <input v-model="form.date_debut" type="date" placeholder="Date de début" class="border rounded px-2 py-1">
          <input v-model="form.date_fin" type="date" placeholder="Date de fin" class="border rounded px-2 py-1">

          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
            {{ selectedNomination ? 'Modifier' : 'Enregistrer' }}
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
        <h4 class="text-lg font-bold mb-4">Détail de la Nomination</h4>
        <div v-if="selectedDetail" class="space-y-2">
          <p><strong>Dignitaire :</strong> {{ selectedDetail.dignitaire_nom }}</p>
          <p><strong>Entité :</strong> {{ selectedDetail.entite_nom || 'N/A' }}</p>
          <p><strong>Poste :</strong> {{ selectedDetail.poste_nom || 'N/A' }}</p>
          <p><strong>Fonction :</strong> {{ selectedDetail.fonction || 'N/A' }}</p>
          <p><strong>Numéro de décret :</strong> {{ selectedDetail.numero_decret || 'N/A' }}</p>
          <p><strong>Date de début :</strong> {{ formatDate(selectedDetail.date_debut) }}</p>
          <p><strong>Date de fin :</strong> {{ selectedDetail.date_fin ? formatDate(selectedDetail.date_fin) : 'En cours' }}</p>
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

const nominations = ref([])
const dignitaires = ref([])
const entites = ref([])
const postes = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedNomination = ref(null)
const selectedDetail = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: '',
  dignitaire_id: '',
  entite_id: ''
})

const form = reactive({
  dignitaire_id: '',
  entite_id: '',
  poste_id: '',
  fonction: '',
  numero_decret: '',
  date_debut: '',
  date_fin: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(nominations.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, nominations.value.length))
const paginatedNominations = computed(() => {
  return nominations.value.slice(startIndex.value, endIndex.value)
})

function formatDate(dateStr) {
  if (!dateStr) return 'N/A'
  const date = new Date(dateStr)
  return date.toLocaleDateString('fr-FR')
}

async function loadNominations() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)
    if (filters.entite_id) params.append('entite_id', filters.entite_id)

    console.log('Chargement nominations depuis:', `${config.public.apiBase}/nominations?${params.toString()}`)
    
    const response = await $fetch(`${config.public.apiBase}/nominations?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    console.log('Réponse nominations:', response)
    nominations.value = Array.isArray(response) ? response : (response.data || [])
    console.log('Nominations chargées:', nominations.value.length)
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (error) {
    console.error('Erreur chargement nominations:', error)
    nominations.value = []
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

async function loadPostes() {
  try {
    const response = await $fetch(`${config.public.apiBase}/postes`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    postes.value = Array.isArray(response) ? response : (response.data || [])
  } catch (error) {
    console.error('Erreur:', error)
  }
}

function openModal(nomination = null) {
  selectedNomination.value = nomination
  if (nomination) {
    form.dignitaire_id = nomination.dignitaire_id
    form.entite_id = nomination.entite_id || ''
    form.poste_id = nomination.poste_id || ''
    form.fonction = nomination.fonction || ''
    form.numero_decret = nomination.numero_decret || ''
    form.date_debut = nomination.date_debut || ''
    form.date_fin = nomination.date_fin || ''
  } else {
    form.dignitaire_id = ''
    form.entite_id = ''
    form.poste_id = ''
    form.fonction = ''
    form.numero_decret = ''
    form.date_debut = ''
    form.date_fin = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedNomination.value = null
}

function openDetailModal(nomination) {
  selectedDetail.value = nomination
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveNomination() {
  try {
    if (selectedNomination.value) {
      await $fetch(`${config.public.apiBase}/nominations/${selectedNomination.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/nominations`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    closeModal()
    loadNominations()
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deleteNomination(id) {
  if (confirm('Supprimer cette nomination ?')) {
    try {
      await $fetch(`${config.public.apiBase}/nominations/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      loadNominations()
    } catch (error) {
      console.error('Erreur:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(async () => {
  entites.value = await referentiels.getEntites()
  await loadPostes()
  await loadDignitaires()
  await loadNominations()
})
</script>
