<template>
  <DashboardLayout>
    <!-- Header -->
    <header class="bg-white shadow-sm p-6">
      <h2 class="text-3xl font-bold text-gray-800">Tableau de Bord</h2>
      <p class="text-gray-600 mt-1">Vue d'ensemble de la gestion des dignitaires</p>
    </header>

    <div class="p-6">
      <!-- Statistiques -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Dignitaires -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Dignitaires</p>
              <p class="text-3xl font-bold text-blue-600">{{ stats?.totalDignitaires || 0 }}</p>
            </div>
            <div class="bg-blue-100 p-4 rounded-full">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
          </div>
          <NuxtLink to="/dignitaires" class="text-blue-600 text-sm mt-2 inline-block hover:underline">
            Voir tous →
          </NuxtLink>
        </div>

        <!-- Postes -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Postes</p>
              <p class="text-3xl font-bold text-green-600">{{ stats?.totalPostes || 0 }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>
          <NuxtLink to="/postes" class="text-green-600 text-sm mt-2 inline-block hover:underline">
            Voir tous →
          </NuxtLink>
        </div>

        <!-- Décorations -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Décorations</p>
              <p class="text-3xl font-bold text-yellow-600">{{ stats?.totalDecorations || 0 }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-full">
              <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
              </svg>
            </div>
          </div>
          <NuxtLink to="/decorations" class="text-yellow-600 text-sm mt-2 inline-block hover:underline">
            Voir toutes →
          </NuxtLink>
        </div>

        <!-- Villes -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Villes</p>
              <p class="text-3xl font-bold text-purple-600">{{ stats?.totalVilles || 0 }}</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-full">
              <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
          </div>
          <NuxtLink to="/villes" class="text-purple-600 text-sm mt-2 inline-block hover:underline">
            Voir toutes →
          </NuxtLink>
        </div>

        <!-- Pays -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Pays</p>
              <p class="text-3xl font-bold text-red-600">{{ stats?.totalPays || 0 }}</p>
            </div>
            <div class="bg-red-100 p-4 rounded-full">
              <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <NuxtLink to="/pays" class="text-red-600 text-sm mt-2 inline-block hover:underline">
            Voir tous →
          </NuxtLink>
        </div>

        <!-- Régions -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Régions</p>
              <p class="text-3xl font-bold text-indigo-600">{{ stats?.totalRegions || 0 }}</p>
            </div>
            <div class="bg-indigo-100 p-4 rounded-full">
              <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
              </svg>
            </div>
          </div>
          <NuxtLink to="/regions" class="text-indigo-600 text-sm mt-2 inline-block hover:underline">
            Voir toutes →
          </NuxtLink>
        </div>
      </div>

      <!-- Derniers dignitaires -->
      <div v-if="derniersDignitaires && derniersDignitaires.length > 0" class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold mb-4">Derniers Dignitaires</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prénom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="dig in derniersDignitaires" :key="dig.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">{{ dig.matricule }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ dig.nom }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ dig.prenom }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ dig.tel || '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <NuxtLink :to="`/dignitaires/${dig.id}`" class="text-blue-600 hover:underline">
                    Voir
                  </NuxtLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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

// Charger les statistiques
const { data: stats } = await useAsyncData('dashboard-stats', async () => {
  try {
    const response = await $fetch(`${config.public.apiBase}/dashboard/stats`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    return response
  } catch (error) {
    console.error('Erreur chargement stats:', error)
    return {
      totalDignitaires: 0,
      totalPostes: 0,
      totalDecorations: 0,
      totalVilles: 0,
      totalPays: 0,
      totalRegions: 0
    }
  }
})

// Charger les derniers dignitaires
const { data: derniersDignitaires } = await useAsyncData('derniers-dignitaires', async () => {
  try {
    const response = await $fetch(`${config.public.apiBase}/dignitaires`, {
      params: { per_page: 5 },
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    // Laravel retourne { data: [...], current_page, last_page, etc }
    return response.data || []
  } catch (error) {
    console.error('Erreur chargement derniers dignitaires:', error)
    return []
  }
})
</script>
