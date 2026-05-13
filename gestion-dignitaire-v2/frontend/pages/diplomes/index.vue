<template>
  <DashboardLayout>
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
      <h2 class="mb-6 text-3xl font-bold">Gestion des Diplômes</h2>

      <!-- Filtres -->
      <div class="mb-6 flex gap-2">
        <input
          v-model="filters.search"
          @input="loadDiplomes"
          type="text"
          placeholder="Rechercher (intitulé, établissement, année)..."
          class="flex-grow border rounded px-3 py-2"
        >
        <select
          v-model="filters.dignitaire_id"
          @change="loadDiplomes"
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
        Ajouter un diplôme
      </button>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table v-if="paginatedDiplomes.length > 0" class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Dignitaire</th>
              <th class="px-4 py-2 text-left">Intitulé</th>
              <th class="px-4 py-2 text-left">Établissement</th>
              <th class="px-4 py-2 text-left">Année</th>
              <th class="px-4 py-2 text-left">Domaine</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="dip in paginatedDiplomes" :key="dip.id">
              <td class="px-4 py-2">{{ dip.dignitaire_nom }}</td>
              <td class="px-4 py-2">{{ dip.intitule }}</td>
              <td class="px-4 py-2">{{ dip.etablissement_nom || 'N/A' }}</td>
              <td class="px-4 py-2">{{ dip.annee }}</td>
              <td class="px-4 py-2">{{ dip.domaine_nom || 'N/A' }}</td>
              <td class="px-4 py-2 flex justify-center gap-2">
                <button
                  @click="openDetailModal(dip)"
                  class="bg-sky-500 hover:bg-sky-600 text-white rounded px-2 py-1 text-xs"
                >
                  Détail
                </button>
                <button
                  @click="openModal(dip)"
                  class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                >
                  Modifier
                </button>
                <button
                  @click="deleteDiplome(dip.id)"
                  class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center py-8 text-gray-500">Aucun diplôme enregistré.</p>

        <!-- Pagination -->
        <Pagination
          v-if="diplomes.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="diplomes.length"
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
        <h4 class="text-lg font-bold mb-4">{{ selectedDiplome ? 'Modifier' : 'Ajouter' }} un diplôme</h4>
        <form @submit.prevent="saveDiplome" class="flex flex-col gap-4">
          <select v-model="form.dignitaire_id" required class="border rounded px-2 py-1">
            <option value="">Choisir un dignitaire</option>
            <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
              {{ dig.prenom }} {{ dig.nom }}
            </option>
          </select>

          <input v-model="form.intitule" placeholder="Intitulé" required class="border rounded px-2 py-1">

          <select v-model="form.etablissement_id" class="border rounded px-2 py-1">
            <option value="">Établissement</option>
            <option v-for="etab in etablissements" :key="etab.id" :value="etab.id">
              {{ etab.nom }}
            </option>
          </select>

          <input v-model="form.annee" placeholder="Année" class="border rounded px-2 py-1">

          <select v-model="form.ville_id" class="border rounded px-2 py-1">
            <option value="">Ville</option>
            <option v-for="ville in villes" :key="ville.id" :value="ville.id">
              {{ ville.nom }}
            </option>
          </select>

          <select v-model="form.domaine_id" class="border rounded px-2 py-1">
            <option value="">Domaine</option>
            <option v-for="dom in domaines" :key="dom.id" :value="dom.id">
              {{ dom.nom }}
            </option>
          </select>

          <input v-model="form.code" placeholder="Code" class="border rounded px-2 py-1">
          <input v-model="form.type" placeholder="Type" class="border rounded px-2 py-1">

          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
            {{ selectedDiplome ? 'Modifier' : 'Enregistrer' }}
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
        <h4 class="text-lg font-bold mb-4">Détail du Diplôme</h4>
        <div v-if="selectedDetail" class="space-y-2">
          <p><strong>Intitulé :</strong> {{ selectedDetail.intitule }}</p>
          <p><strong>Dignitaire :</strong> {{ selectedDetail.dignitaire_nom }}</p>
          <p><strong>Établissement :</strong> {{ selectedDetail.etablissement_nom || 'N/A' }}</p>
          <p><strong>Année :</strong> {{ selectedDetail.annee }}</p>
          <p><strong>Ville :</strong> {{ selectedDetail.ville_nom || 'N/A' }}</p>
          <p><strong>Domaine :</strong> {{ selectedDetail.domaine_nom || 'N/A' }}</p>
          <p><strong>Code :</strong> {{ selectedDetail.code || 'N/A' }}</p>
          <p><strong>Type :</strong> {{ selectedDetail.type || 'N/A' }}</p>
        </div>
        <span @click="closeDetailModal" class="absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-2xl cursor-pointer">&times;</span>
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

const diplomes = ref([])
const dignitaires = ref([])
const etablissements = ref([])
const villes = ref([])
const domaines = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedDiplome = ref(null)
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
  etablissement_id: '',
  annee: '',
  ville_id: '',
  domaine_id: '',
  code: '',
  type: ''
})

// Pagination
const totalPages = computed(() => Math.ceil(diplomes.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, diplomes.value.length))
const paginatedDiplomes = computed(() => {
  return diplomes.value.slice(startIndex.value, endIndex.value)
})

async function loadDiplomes() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)

    console.log('Chargement diplomes depuis:', `${config.public.apiBase}/diplomes?${params.toString()}`)
    
    const response = await $fetch(`${config.public.apiBase}/diplomes?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    console.log('Réponse diplomes:', response)
    console.log('Type de réponse:', typeof response, Array.isArray(response))
    
    diplomes.value = Array.isArray(response) ? response : (response.data || [])
    console.log('Diplomes chargés:', diplomes.value.length)
    currentPage.value = 1 // Reset à la page 1 après recherche
  } catch (error) {
    console.error('Erreur chargement diplomes:', error)
    diplomes.value = []
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

async function loadEtablissements() {
  try {
    const response = await $fetch(`${config.public.apiBase}/etablissements`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    etablissements.value = response || []
  } catch (error) {
    console.error('Erreur:', error)
  }
}

function openModal(diplome: any = null) {
  selectedDiplome.value = diplome
  if (diplome) {
    form.dignitaire_id = diplome.dignitaire_id
    form.intitule = diplome.intitule
    form.etablissement_id = diplome.etablissement_id || ''
    form.annee = diplome.annee || ''
    form.ville_id = diplome.ville_id || ''
    form.domaine_id = diplome.domaine_id || ''
    form.code = diplome.code || ''
    form.type = diplome.type || ''
  } else {
    form.dignitaire_id = ''
    form.intitule = ''
    form.etablissement_id = ''
    form.annee = ''
    form.ville_id = ''
    form.domaine_id = ''
    form.code = ''
    form.type = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedDiplome.value = null
}

function openDetailModal(diplome: any) {
  selectedDetail.value = diplome
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveDiplome() {
  try {
    if (selectedDiplome.value) {
      await $fetch(`${config.public.apiBase}/diplomes/${selectedDiplome.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/diplomes`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    closeModal()
    loadDiplomes()
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deleteDiplome(id: number) {
  if (confirm('Supprimer ce diplôme ?')) {
    try {
      await $fetch(`${config.public.apiBase}/diplomes/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      loadDiplomes()
    } catch (error) {
      console.error('Erreur:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(async () => {
  villes.value = await referentiels.getVilles()
  domaines.value = await referentiels.getDomaines()
  await loadEtablissements()
  await loadDignitaires()
  await loadDiplomes()
})
</script>
