<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Entités</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Organisations, ministères, fédérations : hiérarchie et rattachement administratif</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <div class="flex-1 w-full">
            <input
              v-model="filters.search"
              @input="debouncedLoadEntites"
              type="text"
              placeholder="Rechercher une entité par nom, type ou description..."
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
            >
          </div>
          <button
            @click="exportListe('pdf')"
            class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold px-4 py-3 rounded-lg whitespace-nowrap"
          >
            Exporter PDF
          </button>
          <button
            @click="exportListe('excel')"
            class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold px-4 py-3 rounded-lg whitespace-nowrap"
          >
            Exporter Excel
          </button>
          <button
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

      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="relative">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-green-600 border-t-transparent absolute top-0 left-0"></div>
        </div>
      </div>

      <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="paginatedEntites.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-green-600 to-green-700 text-white">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Nom</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Type</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Entité parente</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Entité de rattachement</th>
                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="e in paginatedEntites" :key="e.id" class="hover:bg-green-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <img v-if="e.logo_url" :src="e.logo_url" class="w-7 h-7 rounded object-cover border" alt="">
                    <span class="text-sm font-bold text-gray-900">{{ e.nom }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="e.type" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ e.type }}</span>
                  <span v-else class="text-sm text-gray-400">—</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ e.parent?.nom || '—' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="e.rattachement?.nom" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ e.rattachement.nom }}</span>
                  <span v-else class="text-sm text-gray-400">—</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="openDetailModal(e)" class="bg-sky-500 hover:bg-sky-600 text-white p-2 rounded-lg shadow transition-all" title="Détail">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button @click="openModal(e)" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg shadow transition-all" title="Modifier">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button @click="deleteEntite(e.id)" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow transition-all" title="Supprimer">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
          <p class="mt-4 text-gray-500 text-lg">Aucune entité enregistrée</p>
        </div>
        <Pagination
          v-if="filteredEntites.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="filteredEntites.length"
          @update:current-page="currentPage = $event"
        />
      </div>
    </section>
    </div>

    <!-- Modal Ajout/Modification -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white">{{ selectedEntite ? 'Modifier' : 'Ajouter' }} une entité</h4>
          <button @click="closeModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveEntite" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nom <span class="text-red-500">*</span></label>
              <input v-model="form.nom" type="text" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Ex: Fédération des Entreprises du Gabon">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
              <input v-model="form.type" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Ex: Ministère, Fédération, Direction...">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Entité parente</label>
                <select v-model="form.id_sup" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                  <option :value="null">-- Aucune --</option>
                  <option v-for="opt in entitesSelectionnables" :key="opt.id" :value="opt.id">{{ opt.nom }}</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Hiérarchie organique (à qui cette entité appartient).</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Entité de rattachement</label>
                <select v-model="form.entite_rattachement_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                  <option :value="null">-- Aucune --</option>
                  <option v-for="opt in entitesSelectionnables" :key="opt.id" :value="opt.id">{{ opt.nom }}</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Rattachement administratif, peut différer de l'entité parente.</p>
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
              <textarea v-model="form.description" rows="2" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                <input v-model="form.telephone" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input v-model="form.email" type="email" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Site web</label>
              <input v-model="form.site_web" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Adresse</label>
              <input v-model="form.adresse" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Logo</label>
              <input @change="onLogoChange" type="file" accept="image/png,image/jpeg" class="w-full border rounded-lg px-4 py-3">
            </div>
          </div>
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
              {{ selectedEntite ? '✓ Modifier' : '+ Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Détail -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeDetailModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-gabon-blue-600 to-sky-600 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white">Détail de l'entité</h4>
          <button @click="closeDetailModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div v-if="selectedDetail" class="p-6 space-y-4">
          <div v-if="selectedDetail.logo_url" class="flex justify-center">
            <img :src="selectedDetail.logo_url" class="h-20 rounded border" alt="">
          </div>
          <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-4 border-l-4 border-green-600">
            <p class="text-sm font-semibold text-gray-500 mb-1">Nom</p>
            <p class="text-xl font-bold text-gray-900">{{ selectedDetail.nom }}</p>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Type</p>
              <p class="text-gray-900">{{ selectedDetail.type || 'Non renseigné' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Entité parente</p>
              <p class="text-gray-900">{{ selectedDetail.parent?.nom || 'Aucune' }}</p>
            </div>
          </div>
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm font-semibold text-gray-500 mb-1">Entité de rattachement</p>
            <p class="text-gray-900">{{ selectedDetail.rattachement?.nom || 'Aucune' }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm font-semibold text-gray-500 mb-1">Contact</p>
            <p class="text-gray-900">{{ selectedDetail.telephone || '—' }} · {{ selectedDetail.email || '—' }}</p>
            <p class="text-gray-900 text-sm">{{ selectedDetail.site_web || '' }}</p>
            <p class="text-gray-900 text-sm">{{ selectedDetail.adresse || '' }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm font-semibold text-gray-500 mb-1">Description</p>
            <p class="text-gray-900">{{ selectedDetail.description || 'Non renseignée' }}</p>
          </div>
          <div class="mt-2 pt-4 border-t">
            <button @click="closeDetailModal" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Fermer</button>
          </div>
        </div>
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
const { debounce } = useDebounce()
const fileDownload = useFileDownload()

async function exportListe(format) {
  try {
    await fileDownload.download('/entites-export', {
      search: filters.search,
      format
    }, `entites.${format === 'excel' ? 'xlsx' : 'pdf'}`)
  } catch (error) {
    console.error('Erreur export entités:', error)
  }
}

const entites = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedEntite = ref(null)
const selectedDetail = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10
const logoFile = ref(null)

const filters = reactive({ search: '' })

const form = reactive({
  nom: '',
  type: '',
  id_sup: null,
  entite_rattachement_id: null,
  description: '',
  telephone: '',
  email: '',
  site_web: '',
  adresse: '',
})

// Une entité ne peut pas se choisir elle-même comme parent/rattachement
const entitesSelectionnables = computed(() =>
  entites.value.filter(e => !selectedEntite.value || e.id !== selectedEntite.value.id)
)

const filteredEntites = computed(() => {
  const result = [...entites.value]
  result.sort((a, b) => a.nom.localeCompare(b.nom))
  return result
})

const totalPages = computed(() => Math.ceil(filteredEntites.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, filteredEntites.value.length))
const paginatedEntites = computed(() => filteredEntites.value.slice(startIndex.value, endIndex.value))

async function loadEntites() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)

    const response = await $fetch(`${config.public.apiBase}/entites?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    entites.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1
  } catch (error) {
    console.error('Erreur chargement entités:', error)
    entites.value = []
  } finally {
    loading.value = false
  }
}

const debouncedLoadEntites = debounce(loadEntites, 500)

function onLogoChange(event) {
  logoFile.value = event.target.files[0] || null
}

function openModal(entite = null) {
  selectedEntite.value = entite
  logoFile.value = null
  if (entite) {
    form.nom = entite.nom
    form.type = entite.type || ''
    form.id_sup = entite.id_sup || null
    form.entite_rattachement_id = entite.entite_rattachement_id || null
    form.description = entite.description || ''
    form.telephone = entite.telephone || ''
    form.email = entite.email || ''
    form.site_web = entite.site_web || ''
    form.adresse = entite.adresse || ''
  } else {
    form.nom = ''
    form.type = ''
    form.id_sup = null
    form.entite_rattachement_id = null
    form.description = ''
    form.telephone = ''
    form.email = ''
    form.site_web = ''
    form.adresse = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedEntite.value = null
}

function openDetailModal(entite) {
  selectedDetail.value = entite
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveEntite() {
  try {
    const formData = new FormData()
    Object.entries(form).forEach(([key, value]) => {
      if (value !== null && value !== '') formData.append(key, value)
    })
    if (logoFile.value) formData.append('logo', logoFile.value)

    const url = selectedEntite.value
      ? `${config.public.apiBase}/entites/${selectedEntite.value.id}`
      : `${config.public.apiBase}/entites`

    if (selectedEntite.value) formData.append('_method', 'PUT')

    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: { Authorization: `Bearer ${authStore.token}`, Accept: 'application/json' }
    })

    if (!response.ok) throw await response.json()

    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedEntite.value ? 'Entité modifiée avec succès' : 'Entité ajoutée avec succès',
      timer: 2000,
      showConfirmButton: false
    })

    closeModal()
    loadEntites()
  } catch (error) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error?.message || 'Erreur lors de la sauvegarde'
    })
  }
}

async function deleteEntite(id) {
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
      await $fetch(`${config.public.apiBase}/entites/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })

      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'L\'entité a été supprimée avec succès',
        timer: 2000,
        showConfirmButton: false
      })

      loadEntites()
    } catch (error) {
      console.error('Erreur:', error)
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: error.data?.message || 'Erreur lors de la suppression'
      })
    }
  }
}

onMounted(() => {
  loadEntites()
})
</script>
