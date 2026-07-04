<template>
  <DashboardLayout>
    <div class="max-w-7xl mx-auto p-6">
      <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
          <i class="fas fa-history text-blue-500"></i>
          Journal des actions
        </h1>
        <p class="text-gray-600 text-sm mt-1">Historique de toutes les créations, modifications et suppressions effectuées sur la plateforme</p>
      </header>

      <!-- Filtres -->
      <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid md:grid-cols-4 gap-4">
          <div>
            <label class="text-xs font-semibold text-gray-600 block mb-1">Type d'entité</label>
            <select v-model="filters.auditable_type" @change="loadLogs(1)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
              <option value="">Toutes</option>
              <option v-for="type in entityTypes" :key="type" :value="type">{{ type }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600 block mb-1">Action</label>
            <select v-model="filters.action" @change="loadLogs(1)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
              <option value="">Toutes</option>
              <option value="created">Créé</option>
              <option value="updated">Modifié</option>
              <option value="deleted">Supprimé</option>
              <option value="validated">Validé</option>
              <option value="refused">Refusé</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600 block mb-1">Recherche</label>
            <input v-model="filters.search" @input="debouncedSearch" type="text" placeholder="Nom, utilisateur..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
          </div>
          <div class="flex items-end">
            <button @click="resetFilters" class="text-sm text-gray-500 hover:text-gray-700 underline">Réinitialiser les filtres</button>
          </div>
        </div>
      </div>

      <!-- Tableau -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div v-if="loading" class="flex justify-center items-center py-16">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
        </div>

        <table v-else-if="logs.length > 0" class="min-w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3 text-left">Date</th>
              <th class="px-4 py-3 text-left">Utilisateur</th>
              <th class="px-4 py-3 text-left">Action</th>
              <th class="px-4 py-3 text-left">Entité</th>
              <th class="px-4 py-3 text-center">Détail</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="log in logs" :key="log.id">
              <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ formatDate(log.created_at) }}</td>
                <td class="px-4 py-3 font-medium text-gray-900">{{ log.causer_label || 'Système' }}</td>
                <td class="px-4 py-3">
                  <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="actionBadgeClass(log.action)">
                    {{ actionLabel(log.action) }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span class="font-semibold text-gray-800">{{ log.auditable_type }}</span>
                  <span v-if="log.auditable_label" class="text-gray-500"> — {{ log.auditable_label }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <button @click="toggleDetail(log.id)" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                    {{ openDetail === log.id ? 'Masquer' : 'Voir' }}
                  </button>
                </td>
              </tr>
              <tr v-if="openDetail === log.id" class="bg-gray-50 border-b">
                <td colspan="5" class="px-4 py-4">
                  <div class="grid md:grid-cols-2 gap-4 text-xs">
                    <div>
                      <p class="font-bold text-gray-600 mb-1">Avant</p>
                      <pre class="bg-white border border-gray-200 rounded p-3 overflow-x-auto">{{ formatValues(log.old_values) }}</pre>
                    </div>
                    <div>
                      <p class="font-bold text-gray-600 mb-1">Après</p>
                      <pre class="bg-white border border-gray-200 rounded p-3 overflow-x-auto">{{ formatValues(log.new_values) }}</pre>
                    </div>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>

        <div v-else class="text-center py-16 text-gray-500">
          Aucune action journalisée pour ces filtres
        </div>

        <!-- Pagination -->
        <div v-if="lastPage > 1" class="flex items-center justify-between px-4 py-3 border-t bg-gray-50">
          <span class="text-sm text-gray-600">{{ total }} action(s) au total</span>
          <div class="flex gap-2">
            <button @click="loadLogs(currentPage - 1)" :disabled="currentPage <= 1" class="px-3 py-1.5 border rounded-lg text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100">Précédent</button>
            <span class="px-3 py-1.5 text-sm">Page {{ currentPage }} / {{ lastPage }}</span>
            <button @click="loadLogs(currentPage + 1)" :disabled="currentPage >= lastPage" class="px-3 py-1.5 border rounded-lg text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100">Suivant</button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const api = useApi()

const logs = ref<any[]>([])
const loading = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const openDetail = ref<number | null>(null)

const entityTypes = ['Dignitaire', 'Nomination', 'Poste', 'Decoration', 'Diplome', 'LangueParlee', 'Experience', 'Enfant', 'Conjoint', 'Entite', 'User', 'Candidat']

const filters = reactive({
  auditable_type: '',
  action: '',
  search: ''
})

const actionLabel = (action: string) => {
  const labels: Record<string, string> = {
    created: 'Créé',
    updated: 'Modifié',
    deleted: 'Supprimé',
    validated: 'Validé',
    refused: 'Refusé'
  }
  return labels[action] || action
}

const actionBadgeClass = (action: string) => {
  const classes: Record<string, string> = {
    created: 'bg-green-100 text-green-700',
    updated: 'bg-blue-100 text-blue-700',
    deleted: 'bg-red-100 text-red-700',
    validated: 'bg-green-100 text-green-700',
    refused: 'bg-yellow-100 text-yellow-700'
  }
  return classes[action] || 'bg-gray-100 text-gray-700'
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleString('fr-FR', {
    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
  })
}

const formatValues = (values: any) => {
  if (!values) return '—'
  return JSON.stringify(values, null, 2)
}

const toggleDetail = (id: number) => {
  openDetail.value = openDetail.value === id ? null : id
}

const resetFilters = () => {
  filters.auditable_type = ''
  filters.action = ''
  filters.search = ''
  loadLogs(1)
}

let searchTimeout: ReturnType<typeof setTimeout>
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => loadLogs(1), 400)
}

const loadLogs = async (page: number) => {
  loading.value = true
  try {
    const response: any = await api.getAuditLogs({
      page,
      per_page: 25,
      auditable_type: filters.auditable_type || undefined,
      action: filters.action || undefined,
      search: filters.search || undefined
    })

    logs.value = response.logs.data
    currentPage.value = response.logs.current_page
    lastPage.value = response.logs.last_page
    total.value = response.logs.total
  } catch (error) {
    console.error('Erreur chargement audit logs:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => loadLogs(1))

useHead({ title: 'Journal des actions - Gestion Dignitaires' })
</script>
