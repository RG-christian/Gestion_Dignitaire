<template>
  <div class="min-h-screen bg-gray-100">
    <DashboardLayout>
      <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">Gestion des Nominations</h1>
        <button
          @click="showModal = true"
          class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold"
        >
          + Ajouter une nomination
        </button>
      </div>

      <!-- Filtres -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <select v-model="filters.dignitaire_id" class="border rounded-lg px-4 py-2">
            <option value="">Tous les dignitaires</option>
            <option v-for="d in dignitaires" :key="d.id" :value="d.id">
              {{ d.nom_complet }}
            </option>
          </select>
          <select v-model="filters.entite_id" class="border rounded-lg px-4 py-2">
            <option value="">Toutes les entités</option>
            <option v-for="e in entites" :key="e.id" :value="e.id">
              {{ e.nom }}
            </option>
          </select>
          <button
            @click="loadNominations"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
          >
            Filtrer
          </button>
        </div>
      </div>

      <!-- Tableau -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dignitaire</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entité</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fonction</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date début</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date fin</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="!nominations?.data?.length">
              <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucune nomination trouvée</td>
            </tr>
            <tr v-for="n in nominations?.data" :key="n.id">
              <td class="px-6 py-4">{{ n.dignitaire?.nom_complet }}</td>
              <td class="px-6 py-4">{{ n.entite?.nom || 'N/A' }}</td>
              <td class="px-6 py-4">{{ n.fonction || 'N/A' }}</td>
              <td class="px-6 py-4">{{ formatDate(n.date_debut) }}</td>
              <td class="px-6 py-4">{{ n.date_fin ? formatDate(n.date_fin) : 'En cours' }}</td>
              <td class="px-6 py-4">
                <button @click="editNomination(n)" class="text-blue-600 hover:text-blue-900 mr-3">
                  Modifier
                </button>
                <button @click="deleteNomination(n.id)" class="text-red-600 hover:text-red-900">
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </DashboardLayout>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const api = useApi()
const filters = reactive({
  dignitaire_id: '',
  entite_id: ''
})

const { data: nominations, refresh: loadNominations } = await useAsyncData(
  'nominations',
  () => api.getNominations(filters)
)

const { data: dignitaires } = await useAsyncData('dignitaires-list', () => api.getDignitaires())
const { data: entites } = await useAsyncData('entites', () => api.getEntites())

const showModal = ref(false)

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('fr-FR')
}

function editNomination(nomination: any) {
  // TODO: Implémenter modal d'édition
  console.log('Edit', nomination)
}

async function deleteNomination(id: number) {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette nomination ?')) {
    await api.deleteNomination(id)
    loadNominations()
  }
}
</script>
