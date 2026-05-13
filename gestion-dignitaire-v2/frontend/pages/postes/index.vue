<template>
  <DashboardLayout>
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
      <h2 class="mb-6 text-3xl font-bold">Gestion des Postes</h2>

      <!-- Filtres -->
      <div class="mb-6 flex gap-2">
        <input
          v-model="filters.search"
          @input="loadPostes"
          type="text"
          placeholder="Rechercher (intitulé, dignitaire, entité, ville)..."
          class="flex-grow border rounded px-3 py-2"
        >
        <select
          v-model="filters.dignitaire_id"
          @change="loadPostes"
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
        Ajouter un poste
      </button>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table v-if="paginatedPostes.length > 0" class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Intitulé</th>
              <th class="px-4 py-2 text-left">Dignitaire</th>
              <th class="px-4 py-2 text-left">Ville</th>
              <th class="px-4 py-2 text-left">Entité</th>
              <th class="px-4 py-2 text-left">Période</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="poste in paginatedPostes" :key="poste.id">
              <td class="px-4 py-2">{{ poste.intitule }}</td>
              <td class="px-4 py-2">{{ poste.dignitaire_nom }}</td>
              <td class="px-4 py-2">{{ poste.ville_nom || 'N/A' }}</td>
              <td class="px-4 py-2">{{ poste.entite_nom || 'N/A' }}</td>
              <td class="px-4 py-2">
                {{ formatDate(poste.date_debut) }} - {{ poste.date_fin ? formatDate(poste.date_fin) : 'À ce jour' }}
              </td>
              <td class="px-4 py-2 flex justify-center gap-2">
                <button
                  @click="openDetailModal(poste)"
                  class="bg-sky-500 hover:bg-sky-600 text-white rounded px-2 py-1 text-xs"
                >
                  Détail
                </button>
                <button
                  @click="openModal(poste)"
                  class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                >
                  Modifier
                </button>
                <button
                  @click="deletePoste(poste.id)"
                  class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center py-8 text-gray-500">Aucun poste enregistré.</p>

        <!-- Pagination -->
        <Pagination
          v-if="postes.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="postes.length"
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
        <h4 class="text-lg font-bold mb-4">{{ selectedPoste ? 'Modifier' : 'Ajouter' }} un poste</h4>
        <form @submit.prevent="savePoste" class="flex flex-col gap-4">
          <select v-model="form.dignitaire_id" required class="border rounded px-2 py-1">
            <option value="">Choisir un dignitaire</option>
            <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
              {{ dig.prenom }} {{ dig.nom }}
            </option>
          </select>

          <input v-model="form.intitule" placeholder="Intitulé du poste" required class="border rounded px-2 py-1">

          <select v-model="form.entite_id" class="border rounded px-2 py-1">
            <option value="">Entité</option>
            <option v-for="entite in entites" :key="entite.id" :value="entite.id">
              {{ entite.nom }}
            </option>
          </select>

          <select v-model="form.ville_id" class="border rounded px-2 py-1">
            <option value="">Ville</option>
            <option v-for="ville in villes" :key="ville.id" :value="ville.id">
              {{ ville.nom }}
            </option>
          </select>

          <input v-model="form.date_debut" type="date" placeholder="Date de début" class="border rounded px-2 py-1">
          <input v-model="form.date_fin" type="date" placeholder="Date de fin" class="border rounded px-2 py-1">

          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
            {{ selectedPoste ? 'Modifier' : 'Enregistrer' }}
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
        <h4 class="text-lg font-bold mb-4">Détail du Poste</h4>
        <div v-if="selectedDetail" class="space-y-2">
          <p><strong>Intitulé :</strong> {{ selectedDetail.intitule }}</p>
          <p><strong>Dignitaire :</strong> {{ selectedDetail.dignitaire_nom }}</p>
          <p><strong>Entité :</strong> {{ selectedDetail.entite_nom || 'N/A' }}</p>
          <p><strong>Ville :</strong> {{ selectedDetail.ville_nom || 'N/A' }}</p>
          <p><strong>Date de début :</strong> {{ formatDate(selectedDetail.date_debut) }}</p>
          <p><strong>Date de fin :</strong> {{ selectedDetail.date_fin ? formatDate(selectedDetail.date_fin) : 'À ce jour' }}</p>
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

const postes = ref([])
const dignitaires = ref([])
const entites = ref([])
const villes = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedPoste = ref(null)
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
  entite_id: '',
  ville_id: '',
  date_debut: '',
  date_fin: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(postes.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, postes.value.length))
const paginatedPostes = computed(() => {
  return postes.value.slice(startIndex.value, endIndex.value)
})

function formatDate(dateStr) {
  if (!dateStr) return 'N/A'
  const date = new Date(dateStr)
  return date.toLocaleDateString('fr-FR')
}

async function loadPostes() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)

    console.log('Chargement postes depuis:', `${config.public.apiBase}/postes?${params.toString()}`)
    
    const response = await $fetch(`${config.public.apiBase}/postes?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    console.log('Réponse postes:', response)
    postes.value = Array.isArray(response) ? response : (response.data || [])
    console.log('Postes chargés:', postes.value.length)
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (error) {
    console.error('Erreur chargement postes:', error)
    postes.value = []
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

function openModal(poste = null) {
  selectedPoste.value = poste
  if (poste) {
    form.dignitaire_id = poste.dignitaire_id
    form.intitule = poste.intitule
    form.entite_id = poste.entite_id || ''
    form.ville_id = poste.ville_id || ''
    form.date_debut = poste.date_debut || ''
    form.date_fin = poste.date_fin || ''
  } else {
    form.dignitaire_id = ''
    form.intitule = ''
    form.entite_id = ''
    form.ville_id = ''
    form.date_debut = ''
    form.date_fin = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedPoste.value = null
}

function openDetailModal(poste) {
  selectedDetail.value = poste
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function savePoste() {
  try {
    if (selectedPoste.value) {
      await $fetch(`${config.public.apiBase}/postes/${selectedPoste.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/postes`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    closeModal()
    loadPostes()
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deletePoste(id) {
  if (confirm('Supprimer ce poste ?')) {
    try {
      await $fetch(`${config.public.apiBase}/postes/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      loadPostes()
    } catch (error) {
      console.error('Erreur:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(async () => {
  villes.value = await referentiels.getVilles()
  entites.value = await referentiels.getEntites()
  await loadDignitaires()
  await loadPostes()
})
</script>
