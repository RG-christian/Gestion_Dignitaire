<template>
  <div class="min-h-screen bg-gray-100">
    <DashboardLayout>
      <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">Gestion des Décorations</h1>
        <button
          @click="showModal = true"
          class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold"
        >
          + Ajouter une décoration
        </button>
      </div>

      <!-- Grille de cartes -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="decoration in decorations?.data"
          :key="decoration.id"
          class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition"
        >
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h3 class="text-xl font-bold text-gray-900 mb-2">{{ decoration.nom }}</h3>
              <div class="space-y-1 text-sm">
                <p v-if="decoration.type" class="text-gray-600">
                  <span class="font-medium">Type:</span> {{ decoration.type }}
                </p>
                <p v-if="decoration.niveau" class="text-gray-600">
                  <span class="font-medium">Niveau:</span> {{ decoration.niveau }}
                </p>
                <p v-if="decoration.grade" class="text-gray-600">
                  <span class="font-medium">Grade:</span> {{ decoration.grade }}
                </p>
              </div>
            </div>
            <div class="ml-4">
              <span class="inline-flex bg-yellow-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
              </span>
            </div>
          </div>

          <div v-if="decoration.description" class="text-sm text-gray-600 mb-4">
            {{ decoration.description }}
          </div>

          <div class="flex items-center justify-between pt-4 border-t">
            <span class="text-sm text-gray-500">
              {{ decoration.dignitaires?.length || 0 }} attribution(s)
            </span>
            <div class="flex space-x-2">
              <button
                @click="editDecoration(decoration)"
                class="text-blue-600 hover:text-blue-900"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button
                @click="deleteDecoration(decoration.id)"
                class="text-red-600 hover:text-red-900"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Message si vide -->
      <div v-if="!decorations?.data?.length" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune décoration</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter une nouvelle décoration.</p>
      </div>
    </DashboardLayout>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const api = useApi()
const showModal = ref(false)

const { data: decorations, refresh: loadDecorations } = await useAsyncData(
  'decorations',
  () => api.getDecorations()
)

function editDecoration(decoration: any) {
  // TODO: Implémenter modal d'édition
  console.log('Edit', decoration)
}

async function deleteDecoration(id: number) {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette décoration ?')) {
    await api.deleteDecoration(id)
    loadDecorations()
  }
}
</script>
