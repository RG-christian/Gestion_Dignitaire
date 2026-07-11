<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Candidatures</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Consulter, valider ou refuser les candidatures, et laisser des recommandations</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <!-- Statistiques -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-gray-500 p-5">
          <div class="text-gray-500 text-sm font-medium mb-1">Total</div>
          <div class="text-3xl font-bold text-gray-800">{{ stats.total ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-yellow-500 p-5">
          <div class="text-gray-500 text-sm font-medium mb-1">En attente</div>
          <div class="text-3xl font-bold text-gray-800">{{ stats.en_attente ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-green-600 p-5">
          <div class="text-gray-500 text-sm font-medium mb-1">Validées</div>
          <div class="text-3xl font-bold text-gray-800">{{ stats.valides ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-5">
          <div class="text-gray-500 text-sm font-medium mb-1">Refusées</div>
          <div class="text-3xl font-bold text-gray-800">{{ stats.refuses ?? 0 }}</div>
        </div>
      </div>

      <!-- Filtres -->
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <input
              v-model="filters.search"
              @input="debouncedLoad"
              type="text"
              placeholder="Rechercher par nom, prénom, email, matricule..."
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
            >
          </div>
          <select v-model="filters.statut" @change="loadCandidatures" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">Tous les statuts</option>
            <option value="en_attente">En attente</option>
            <option value="valide">Validées</option>
            <option value="refuse">Refusées</option>
          </select>
        </div>
      </div>

      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="candidatures.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-green-600 to-green-700 text-white">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Candidat</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Contact</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Date candidature</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Statut</th>
                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="c in candidatures" :key="c.id" class="hover:bg-green-50 transition-colors">
                <td class="px-6 py-4">
                  <div class="font-bold text-gray-900">{{ c.prenom }} {{ c.nom }}</div>
                  <div class="text-xs text-gray-500">{{ c.matricule || 'Sans matricule' }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  <div>{{ c.email }}</div>
                  <div class="text-xs text-gray-500">{{ c.telephone }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ c.date_candidature ? new Date(c.date_candidature).toLocaleDateString('fr-FR') : '—' }}
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="statutBadgeClass(c.statut)">
                    {{ statutLabel(c.statut) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <NuxtLink
                    :to="`/admin/candidatures/${c.id}`"
                    class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors"
                  >
                    Consulter
                  </NuxtLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-12 text-gray-500">
          <p class="text-lg font-medium">Aucune candidature trouvée</p>
        </div>
      </div>
    </section>
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

const candidatures = ref([])
const stats = ref({})
const loading = ref(true)

const filters = reactive({
  search: '',
  statut: ''
})

function statutLabel(statut) {
  return { en_attente: 'En attente', valide: 'Validée', refuse: 'Refusée' }[statut] || statut
}

function statutBadgeClass(statut) {
  const classes = {
    en_attente: 'bg-yellow-100 text-yellow-800',
    valide: 'bg-green-100 text-green-700',
    refuse: 'bg-red-100 text-red-700'
  }
  return classes[statut] || 'bg-gray-100 text-gray-700'
}

async function loadCandidatures() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.statut) params.append('statut', filters.statut)
    params.append('per_page', '100')

    const response = await $fetch(`${config.public.apiBase}/admin/candidats?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    candidatures.value = response.candidats?.data || []
  } catch (error) {
    console.error('Erreur chargement candidatures:', error)
    candidatures.value = []
  } finally {
    loading.value = false
  }
}

const debouncedLoad = debounce(loadCandidatures, 500)

async function loadStats() {
  try {
    const response = await $fetch(`${config.public.apiBase}/admin/candidats/stats`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    stats.value = response.stats || {}
  } catch (error) {
    console.error('Erreur chargement stats candidatures:', error)
  }
}

onMounted(() => {
  loadCandidatures()
  loadStats()
})
</script>
