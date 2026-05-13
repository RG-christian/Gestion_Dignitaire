<template>
  <DashboardLayout>
    <main class="max-w-7xl mx-auto p-6 bg-white shadow rounded-xl mt-8">
      <div class="flex justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-700">Liste des enfants</h2>
        <button
          @click="openModal()"
          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl font-semibold transition"
        >
          + Ajouter un enfant
        </button>
      </div>

      <!-- Filtres -->
      <div class="mb-4 flex gap-4">
        <input
          v-model="filters.search"
          @input="loadEnfants"
          type="text"
          placeholder="Rechercher un enfant..."
          class="border rounded px-4 py-2 flex-1"
        >
        <select
          v-model="filters.dignitaire_id"
          @change="loadEnfants"
          class="border rounded px-4 py-2"
        >
          <option value="">Tous les dignitaires</option>
          <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
            {{ dig.prenom }} {{ dig.nom }}
          </option>
        </select>
      </div>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <!-- Erreur -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
        {{ error }}
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table v-if="paginatedEnfants.length > 0" class="min-w-full text-sm text-gray-700">
          <thead class="bg-gray-100 border-b">
            <tr>
              <th class="py-3 px-4 text-left">Nom</th>
              <th class="py-3 px-4 text-left">Prénom</th>
              <th class="py-3 px-4 text-left">Genre</th>
              <th class="py-3 px-4 text-left">Naissance</th>
              <th class="py-3 px-4 text-left">Lieu</th>
              <th class="py-3 px-4 text-left">Dignitaire</th>
              <th class="py-3 px-4 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="enfant in paginatedEnfants" :key="enfant.id" class="hover:bg-gray-50">
              <td class="py-2 px-4">{{ enfant.nom }}</td>
              <td class="py-2 px-4">{{ enfant.prenom }}</td>
              <td class="py-2 px-4">{{ enfant.genre }}</td>
              <td class="py-2 px-4">{{ formatDate(enfant.date_naissance) }}</td>
              <td class="py-2 px-4">{{ enfant.lieu_naissance_nom || 'N/A' }}</td>
              <td class="py-2 px-4">{{ enfant.dignitaire_nom_complet }}</td>
              <td class="py-2 px-4 flex justify-center space-x-2">
                <button
                  @click="openDetailModal(enfant)"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition"
                >
                  Détails
                </button>
                <button
                  @click="openModal(enfant)"
                  class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded transition"
                >
                  Modifier
                </button>
                <button
                  @click="deleteEnfant(enfant.id)"
                  class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center py-8 text-gray-500">Aucun enfant enregistré.</p>

        <!-- Pagination -->
        <Pagination
          v-if="enfants.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="enfants.length"
          @update:current-page="currentPage = $event"
        />
      </div>
    </main>

    <!-- Modal Ajout/Modification -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative animate-scale-in">
        <h2 class="text-2xl font-bold mb-4" :class="selectedEnfant ? 'text-yellow-600' : 'text-green-700'">
          {{ selectedEnfant ? 'Modifier' : 'Ajouter' }} un enfant
        </h2>
        <form @submit.prevent="saveEnfant" class="space-y-4">
          <input
            v-model="form.nom"
            type="text"
            placeholder="Nom"
            required
            class="w-full border rounded px-3 py-2"
          >
          <input
            v-model="form.prenom"
            type="text"
            placeholder="Prénom"
            required
            class="w-full border rounded px-3 py-2"
          >
          <input
            v-model="form.date_naissance"
            type="date"
            required
            class="w-full border rounded px-3 py-2"
          >
          <select
            v-model="form.lieu_naissance"
            class="w-full border rounded px-3 py-2"
          >
            <option value="">Lieu de naissance</option>
            <option v-for="ville in villes" :key="ville.id" :value="ville.id">
              {{ ville.nom }}
            </option>
          </select>
          <select
            v-model="form.genre"
            required
            class="w-full border rounded px-3 py-2"
          >
            <option value="">Genre</option>
            <option value="M">Masculin</option>
            <option value="F">Féminin</option>
          </select>
          <select
            v-model="form.dignitaire_id"
            required
            class="w-full border rounded px-3 py-2"
          >
            <option value="">Choisir un dignitaire</option>
            <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
              {{ dig.prenom }} {{ dig.nom }}
            </option>
          </select>

          <div class="flex justify-end gap-3 mt-4">
            <button
              type="button"
              @click="closeModal"
              class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded transition"
            >
              Annuler
            </button>
            <button
              type="submit"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition"
            >
              {{ selectedEnfant ? 'Modifier' : 'Ajouter' }}
            </button>
          </div>
        </form>
        <button
          @click="closeModal"
          class="absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-2xl"
        >
          &times;
        </button>
      </div>
    </div>

    <!-- Modal Détail -->
    <div
      v-if="showDetailModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeDetailModal"
    >
      <div class="bg-white rounded-xl shadow-lg w-full max-w-xl p-6 relative animate-fade-in">
        <h2 class="text-2xl font-bold text-blue-700 mb-4">Détails de l'enfant</h2>
        <div v-if="selectedDetail" class="space-y-3">
          <p><strong>Nom complet :</strong> {{ selectedDetail.prenom }} {{ selectedDetail.nom }}</p>
          <p><strong>Genre :</strong> {{ selectedDetail.genre === 'M' ? 'Masculin' : 'Féminin' }}</p>
          <p><strong>Date de naissance :</strong> {{ formatDate(selectedDetail.date_naissance) }}</p>
          <p><strong>Lieu de naissance :</strong> {{ selectedDetail.lieu_naissance_nom || 'N/A' }}</p>
          <p><strong>Dignitaire :</strong> {{ selectedDetail.dignitaire_nom_complet }}</p>
          <p v-if="selectedDetail.age"><strong>Âge :</strong> {{ selectedDetail.age }} ans</p>
        </div>
        <button
          @click="closeDetailModal"
          class="absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-2xl"
        >
          &times;
        </button>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const config = useRuntimeConfig()
const authStore = useAuthStore()
const referentiels = useReferentiels()

const enfants = ref([])
const dignitaires = ref([])
const villes = ref([])
const loading = ref(true)
const error = ref('')
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedEnfant = ref(null)
const selectedDetail = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: '',
  dignitaire_id: ''
})

const form = reactive({
  nom: '',
  prenom: '',
  date_naissance: '',
  lieu_naissance: '',
  genre: '',
  dignitaire_id: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(enfants.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, enfants.value.length))
const paginatedEnfants = computed(() => {
  return enfants.value.slice(startIndex.value, endIndex.value)
})

async function loadEnfants() {
  loading.value = true
  error.value = ''
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)

    const response = await $fetch(`${config.public.apiBase}/enfants?${params.toString()}`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    console.log('Enfants chargés:', response)
    enfants.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (err: any) {
    console.error('Erreur chargement enfants:', err)
    error.value = err.message || 'Erreur lors du chargement des enfants'
    enfants.value = []
  } finally {
    loading.value = false
  }
}

async function loadDignitaires() {
  try {
    const response = await $fetch(`${config.public.apiBase}/dignitaires?per_page=1000`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    dignitaires.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement dignitaires:', error)
  }
}

function openModal(enfant: any = null) {
  selectedEnfant.value = enfant
  if (enfant) {
    form.nom = enfant.nom
    form.prenom = enfant.prenom
    form.date_naissance = enfant.date_naissance
    form.lieu_naissance = enfant.lieu_naissance || ''
    form.genre = enfant.genre
    form.dignitaire_id = enfant.dignitaire_id
  } else {
    form.nom = ''
    form.prenom = ''
    form.date_naissance = ''
    form.lieu_naissance = ''
    form.genre = ''
    form.dignitaire_id = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedEnfant.value = null
}

function openDetailModal(enfant: any) {
  selectedDetail.value = enfant
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveEnfant() {
  try {
    if (selectedEnfant.value) {
      await $fetch(`${config.public.apiBase}/enfants/${selectedEnfant.value.id}`, {
        method: 'PUT',
        body: form,
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
    } else {
      await $fetch(`${config.public.apiBase}/enfants`, {
        method: 'POST',
        body: form,
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
    }
    closeModal()
    loadEnfants()
  } catch (error) {
    console.error('Erreur sauvegarde:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deleteEnfant(id: number) {
  if (confirm('Supprimer cet enfant ?')) {
    try {
      await $fetch(`${config.public.apiBase}/enfants/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
      loadEnfants()
    } catch (error) {
      console.error('Erreur suppression:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

function formatDate(date: string | null) {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR')
}

onMounted(async () => {
  villes.value = await referentiels.getVilles()
  await loadDignitaires()
  await loadEnfants()
})
</script>

<style scoped>
.animate-scale-in {
  animation: scaleIn 0.3s ease forwards;
}
.animate-fade-in {
  animation: fadeIn 0.3s ease forwards;
}
@keyframes scaleIn {
  from { transform: scale(0.8); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>
