<template>
  <DashboardLayout>
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
      <h2 class="mb-6 text-3xl font-bold">Gestion des Expériences Professionnelles</h2>

      <!-- Filtres -->
      <div class="mb-6 flex gap-2">
        <input
          v-model="filters.search"
          @input="loadExperiences"
          type="text"
          placeholder="Rechercher (intitulé, dignitaire, structure)..."
          class="flex-grow border rounded px-3 py-2"
        >
        <select
          v-model="filters.dignitaire_id"
          @change="loadExperiences"
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
        Ajouter une expérience
      </button>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table v-if="experiences.length > 0" class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Poste occupé</th>
              <th class="px-4 py-2 text-left">Dignitaire</th>
              <th class="px-4 py-2 text-left">Structure</th>
              <th class="px-4 py-2 text-left">Date début</th>
              <th class="px-4 py-2 text-left">Date fin</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="exp in paginatedExperiences" :key="exp.id">
              <td class="px-4 py-2">{{ exp.intitule }}</td>
              <td class="px-4 py-2">{{ exp.dignitaire_nom }}</td>
              <td class="px-4 py-2">{{ exp.structure_nom || 'N/A' }}</td>
              <td class="px-4 py-2">{{ formatDate(exp.date_debut) }}</td>
              <td class="px-4 py-2">{{ exp.date_fin ? formatDate(exp.date_fin) : 'À ce jour' }}</td>
              <td class="px-4 py-2 flex justify-center gap-2">
                <button
                  @click="openDetailModal(exp)"
                  class="bg-sky-500 hover:bg-sky-600 text-white rounded px-2 py-1 text-xs"
                >
                  Détail
                </button>
                <button
                  @click="openModal(exp)"
                  class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                >
                  Modifier
                </button>
                <button
                  @click="deleteExperience(exp.id)"
                  class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center py-8 text-gray-500">Aucune expérience enregistrée.</p>

        <!-- Pagination -->
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

    <!-- Modal Ajout/Modification -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
        <h4 class="text-lg font-bold mb-4">{{ selectedExperience ? 'Modifier' : 'Ajouter' }} une expérience</h4>
        <form @submit.prevent="saveExperience" class="flex flex-col gap-4">
          <select v-model="form.dignitaire_id" required class="border rounded px-2 py-1">
            <option value="">Choisir un dignitaire</option>
            <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
              {{ dig.prenom }} {{ dig.nom }}
            </option>
          </select>

          <input v-model="form.intitule" placeholder="Poste occupé" required class="border rounded px-2 py-1">

          <select v-model="form.structure_id" class="border rounded px-2 py-1">
            <option value="">Institution</option>
            <option v-for="structure in structures" :key="structure.id" :value="structure.id">
              {{ structure.nom }}
            </option>
          </select>

          <input v-model="form.date_debut" type="date" placeholder="Date de début" class="border rounded px-2 py-1">
          <input v-model="form.date_fin" type="date" placeholder="Date de fin" class="border rounded px-2 py-1">

          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
            {{ selectedExperience ? 'Modifier' : 'Enregistrer' }}
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
        <h4 class="text-lg font-bold mb-4">Détail de l'Expérience</h4>
        <div v-if="selectedDetail" class="space-y-2">
          <p><strong>Poste occupé :</strong> {{ selectedDetail.intitule }}</p>
          <p><strong>Dignitaire :</strong> {{ selectedDetail.dignitaire_nom }}</p>
          <p><strong>Institution :</strong> {{ selectedDetail.structure_nom || 'N/A' }}</p>
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

    console.log('Chargement expériences depuis:', `${config.public.apiBase}/experiences?${params.toString()}`)
    
    const response = await $fetch(`${config.public.apiBase}/experiences?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    console.log('Réponse expériences:', response)
    experiences.value = Array.isArray(response) ? response : (response.data || [])
    console.log('Expériences chargées:', experiences.value.length)
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (error) {
    console.error('Erreur chargement expériences:', error)
    experiences.value = []
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
    closeModal()
    loadExperiences()
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deleteExperience(id) {
  if (confirm('Supprimer cette expérience ?')) {
    try {
      await $fetch(`${config.public.apiBase}/experiences/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      loadExperiences()
    } catch (error) {
      console.error('Erreur:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(async () => {
  structures.value = await referentiels.getStructures()
  await loadDignitaires()
  await loadExperiences()
})
</script>
