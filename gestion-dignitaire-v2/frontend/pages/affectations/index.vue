<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Affectations</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Séjours à l'étranger des dignitaires (ambassades, missions, consulats...), distincts de leur nationalité et de leur poste</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <!-- Barre de recherche et filtres -->
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <div class="w-full md:w-64">
            <select
              v-model="filters.dignitaire_id"
              @change="loadAffectations"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
            >
              <option value="">Tous les dignitaires</option>
              <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                {{ dig.prenom }} {{ dig.nom }}
              </option>
            </select>
          </div>
          <div class="flex-1"></div>
          <button
            v-if="permissions.peutEcrire('Dignitaire')"
            @click="openModal()"
            class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter
          </button>
        </div>
      </div>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="relative">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-green-600 border-t-transparent absolute top-0 left-0"></div>
        </div>
      </div>

      <!-- Table -->
      <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="paginatedAffectations.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dignitaire</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pays</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ville</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Période</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="a in paginatedAffectations" :key="a.id" class="hover:bg-gabon-green-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ a.dignitaire_nom }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ a.pays_nom || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ a.ville_nom || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <div>{{ a.type_affectation || 'N/A' }}</div>
                  <div class="flex items-center gap-1 mt-1">
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"
                      :class="a.nature === 'mission_temporaire' ? 'bg-amber-100 text-amber-700' : 'bg-gabon-blue-100 text-gabon-blue-700'"
                    >
                      {{ a.nature === 'mission_temporaire' ? 'Mission temporaire' : 'Affectation principale' }}
                    </span>
                    <span v-if="a.poste_id" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600" title="Générée automatiquement depuis un poste">
                      Auto
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  {{ formatDate(a.date_debut) }} -
                  <span v-if="a.statut === 'terminee'">{{ formatDate(a.date_fin) }}</span>
                  <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">En cours</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button v-if="a.statut !== 'terminee' && permissions.peutEcrire('Dignitaire')" @click="openClotureModal(a)" class="inline-flex items-center gap-1 bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Clôturer">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      Clôturer
                    </button>
                    <button v-if="permissions.peutEcrire('Dignitaire')" @click="openModal(a)" class="inline-flex items-center gap-1 bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Modifier">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Modifier
                    </button>
                    <button v-if="permissions.peutSupprimer()" @click="deleteAffectation(a.id)" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Supprimer">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      Supprimer
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <p class="mt-4 text-gray-500 text-lg">Aucune affectation enregistrée</p>
        </div>
        <Pagination
          v-if="affectations.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="affectations.length"
          @update:current-page="currentPage = $event"
        />
      </div>
    </section>
    </div>

    <!-- Modal Ajout/Modification -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white">{{ selectedAffectation ? 'Modifier' : 'Ajouter' }} une affectation</h4>
          <button @click="closeModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveAffectation" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Dignitaire <span class="text-red-500">*</span></label>
              <select v-model="form.dignitaire_id" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Choisir un dignitaire --</option>
                <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">{{ dig.prenom }} {{ dig.nom }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Pays d'affectation <span class="text-red-500">*</span></label>
              <select v-model="form.pays_id" @change="onPaysChange" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Sélectionner un pays --</option>
                <option v-for="p in pays" :key="p.id" :value="p.id">{{ p.nom }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Ville</label>
              <select v-if="!showCustomVille" v-model="form.ville_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">{{ villesFiltrees.length > 0 ? '-- Sélectionner une ville --' : 'Sélectionnez d\'abord un pays' }}</option>
                <option v-for="v in villesFiltrees" :key="v.id" :value="v.id">{{ v.nom }}</option>
              </select>
              <input v-else v-model="form.ville_custom" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Nom de la ville">
              <button type="button" @click="showCustomVille = !showCustomVille" class="mt-2 text-sm text-gabon-green-600 hover:text-gabon-green-700 font-semibold">
                {{ showCustomVille ? '← Retour à la liste' : 'Ma ville n\'est pas dans la liste' }}
              </button>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Type d'affectation</label>
              <select v-model="form.type_affectation" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Non précisé --</option>
                <option value="Ambassade">Ambassade</option>
                <option value="Consulat">Consulat</option>
                <option value="Mission">Mission</option>
                <option value="Autre">Autre</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nature de l'affectation</label>
              <select
                v-model="form.nature"
                :disabled="!!(selectedAffectation && selectedAffectation.poste_id)"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition disabled:bg-gray-100 disabled:text-gray-500"
              >
                <option value="principale">Affectation principale</option>
                <option value="mission_temporaire">Mission temporaire</option>
              </select>
              <p v-if="selectedAffectation && selectedAffectation.poste_id" class="text-xs text-gray-500 mt-1">
                Générée depuis un poste : reste toujours "Affectation principale".
              </p>
              <p v-else class="text-xs text-gray-500 mt-1">
                Un dignitaire peut avoir une affectation principale et, en plus, une ou plusieurs missions temporaires ailleurs au même moment.
              </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de début <span class="text-red-500">*</span></label>
                <input v-model="form.date_debut" type="date" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
                <input v-model="form.date_fin" type="date" :min="minDateFin(form.date_debut)" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>
          </div>
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
              {{ selectedAffectation ? 'Modifier' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Clôture -->
    <div v-if="showClotureModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeClotureModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4">
          <h4 class="text-xl font-bold text-white">Clôturer l'affectation</h4>
        </div>
        <form @submit.prevent="confirmCloture" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
            <input v-model="clotureForm.date_fin" type="date" :min="minDateFin(affectationToClose?.date_debut)" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
          </div>
          <div class="flex gap-3 pt-2">
            <button type="button" @click="closeClotureModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition">Confirmer</button>
          </div>
        </form>
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
const permissions = usePermissions()
const referentiels = useReferentiels()
const { minDateFin } = useDateHelpers()

const affectations = ref([])
const dignitaires = ref([])
const pays = ref([])
const villes = ref([])
const loading = ref(true)
const showModal = ref(false)
const showClotureModal = ref(false)
const selectedAffectation = ref(null)
const affectationToClose = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  dignitaire_id: ''
})

const form = reactive({
  dignitaire_id: '',
  pays_id: '',
  ville_id: '',
  ville_custom: '',
  type_affectation: '',
  nature: 'principale',
  date_debut: '',
  date_fin: ''
})

const showCustomVille = ref(false)

const villesFiltrees = computed(() => {
  if (!form.pays_id) return []
  return villes.value.filter(v => v.pays_id == form.pays_id)
})

function onPaysChange() {
  form.ville_id = ''
  form.ville_custom = ''
  showCustomVille.value = false
}

const clotureForm = reactive({
  date_fin: new Date().toISOString().slice(0, 10)
})

const totalPages = computed(() => Math.ceil(affectations.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, affectations.value.length))
const paginatedAffectations = computed(() => affectations.value.slice(startIndex.value, endIndex.value))

function formatDate(dateStr) {
  if (!dateStr) return 'N/A'
  return new Date(dateStr).toLocaleDateString('fr-FR')
}

async function loadAffectations() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)

    const response = await $fetch(`${config.public.apiBase}/affectations?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    affectations.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1
  } catch (error) {
    console.error('Erreur chargement affectations:', error)
    affectations.value = []
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

function openModal(affectation = null) {
  selectedAffectation.value = affectation
  showCustomVille.value = false
  if (affectation) {
    form.dignitaire_id = affectation.dignitaire_id
    form.pays_id = affectation.pays_id
    form.ville_id = affectation.ville_id || ''
    form.ville_custom = ''
    form.type_affectation = affectation.type_affectation || ''
    form.nature = affectation.nature || 'principale'
    form.date_debut = affectation.date_debut || ''
    form.date_fin = affectation.date_fin || ''
  } else {
    form.dignitaire_id = ''
    form.pays_id = ''
    form.ville_id = ''
    form.ville_custom = ''
    form.type_affectation = ''
    form.nature = 'principale'
    form.date_debut = ''
    form.date_fin = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedAffectation.value = null
}

function openClotureModal(affectation) {
  affectationToClose.value = affectation
  clotureForm.date_fin = new Date().toISOString().slice(0, 10)
  showClotureModal.value = true
}

function closeClotureModal() {
  showClotureModal.value = false
  affectationToClose.value = null
}

async function confirmCloture() {
  try {
    await $fetch(`${config.public.apiBase}/affectations/${affectationToClose.value.id}/cloturer`, {
      method: 'POST',
      body: clotureForm,
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    const { $swal } = useNuxtApp()
    $swal.fire({ icon: 'success', title: 'Affectation clôturée', timer: 2000, showConfirmButton: false })

    closeClotureModal()
    loadAffectations()
  } catch (error) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || 'Erreur lors de la clôture' })
  }
}

async function saveAffectation() {
  try {
    if (selectedAffectation.value) {
      await $fetch(`${config.public.apiBase}/affectations/${selectedAffectation.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/affectations`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }

    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedAffectation.value ? 'Affectation modifiée avec succès' : 'Affectation ajoutée avec succès',
      timer: 2000,
      showConfirmButton: false
    })

    closeModal()
    loadAffectations()
  } catch (error) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || 'Erreur lors de la sauvegarde' })
  }
}

async function deleteAffectation(id) {
  const { $swal } = useNuxtApp()
  const result = await $swal.fire({
    title: 'Êtes-vous sûr ?',
    text: 'Cette action est irréversible',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#dc2626',
    confirmButtonText: 'Oui, supprimer',
    cancelButtonText: 'Annuler'
  })

  if (result.isConfirmed) {
    try {
      await $fetch(`${config.public.apiBase}/affectations/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })

      $swal.fire({ icon: 'success', title: 'Supprimé', text: 'L\'affectation a été supprimée avec succès', timer: 2000, showConfirmButton: false })

      loadAffectations()
    } catch (error) {
      console.error('Erreur:', error)
      $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || 'Erreur lors de la suppression' })
    }
  }
}

onMounted(async () => {
  villes.value = await referentiels.getVilles()
  pays.value = await referentiels.getPays()
  await loadDignitaires()
  await loadAffectations()
})
</script>
