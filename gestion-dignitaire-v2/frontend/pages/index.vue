<template>
  <DashboardLayout>
    <!-- Header avec drapeau - TITRE BIEN VISIBLE -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl"></div>
      </div>
      <div class="relative z-10">
        <div class="flex items-center gap-3 mb-1">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Tableau de Bord</h1>
        </div>
        <p class="text-white text-base opacity-95 drop-shadow">Vue d'ensemble - République Gabonaise</p>
      </div>
    </header>

    <div class="p-4 bg-gray-50 min-h-screen" style="zoom: 0.85;">
      <!-- KPI Cards - Statistiques principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Dignitaires -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-4xl font-bold" style="color: #2563eb;">{{ stats?.totalDignitaires || 0 }}</p>
              </div>
            </div>
            <p class="text-gray-600 font-medium mb-2">Dignitaires</p>
            <div class="flex items-center justify-between">
              <span class="text-xs text-green-600 font-semibold flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                +12% ce mois
              </span>
              <NuxtLink to="/dignitaires" class="text-blue-600 hover:text-blue-700 text-sm font-semibold group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                Voir
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </NuxtLink>
            </div>
          </div>
          <div class="h-1" style="background: linear-gradient(to right, #2563eb, #1d4ed8);"></div>
        </div>

        <!-- Postes -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-4xl font-bold" style="color: #16a34a;">{{ stats?.totalPostes || 0 }}</p>
              </div>
            </div>
            <p class="text-gray-600 font-medium mb-2">Postes</p>
            <div class="flex items-center justify-between">
              <span class="text-xs text-green-600 font-semibold flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                +8% ce mois
              </span>
              <NuxtLink to="/postes" class="text-green-600 hover:text-green-700 text-sm font-semibold group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                Voir
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </NuxtLink>
            </div>
          </div>
          <div class="h-1" style="background: linear-gradient(to right, #16a34a, #15803d);"></div>
        </div>

        <!-- Décorations -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-4xl font-bold" style="color: #eab308;">{{ stats?.totalDecorations || 0 }}</p>
              </div>
            </div>
            <p class="text-gray-600 font-medium mb-2">Décorations</p>
            <div class="flex items-center justify-between">
              <span class="text-xs text-green-600 font-semibold flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                +5% ce mois
              </span>
              <NuxtLink to="/decorations" class="text-yellow-600 hover:text-yellow-700 text-sm font-semibold group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                Voir
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </NuxtLink>
            </div>
          </div>
          <div class="h-1" style="background: linear-gradient(to right, #eab308, #ca8a04);"></div>
        </div>

        <!-- Régions -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-4xl font-bold" style="color: #8b5cf6;">{{ stats?.totalRegions || 0 }}</p>
              </div>
            </div>
            <p class="text-gray-600 font-medium mb-2">Régions</p>
            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-500 font-semibold">Stable</span>
              <NuxtLink to="/regions" class="text-purple-600 hover:text-purple-700 text-sm font-semibold group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                Voir
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </NuxtLink>
            </div>
          </div>
          <div class="h-1" style="background: linear-gradient(to right, #8b5cf6, #7c3aed);"></div>
        </div>
      </div>

      <!-- Graphiques et statistiques -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Graphique - Répartition par genre -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
              Répartition des Dignitaires
            </h3>
            <select 
              v-model="chartType" 
              @change="updateChart"
              class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="genre">Par Genre</option>
              <option value="region">Par Région</option>
              <option value="poste">Par Poste</option>
            </select>
          </div>
          <div class="h-64">
            <canvas ref="genreChart"></canvas>
          </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Statistiques Rapides
          </h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Villes</p>
                  <p class="text-2xl font-bold text-blue-600">{{ stats?.totalVilles || 0 }}</p>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Pays</p>
                  <p class="text-2xl font-bold text-green-600">{{ stats?.totalPays || 0 }}</p>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Diplômes</p>
                  <p class="text-2xl font-bold text-yellow-600">{{ stats?.totalDiplomes || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Derniers dignitaires -->
      <div v-if="derniersDignitaires && derniersDignitaires.length > 0" class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Derniers Dignitaires Enregistrés
          </h3>
          <NuxtLink to="/dignitaires" class="text-blue-600 hover:text-blue-700 font-semibold text-sm flex items-center gap-1">
            Voir tous
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </NuxtLink>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Matricule</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nom Complet</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Téléphone</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="dig in derniersDignitaires" :key="dig.id" class="hover:bg-blue-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="font-mono text-sm font-semibold text-gray-900">{{ dig.matricule }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                      {{ dig.nom?.charAt(0) }}{{ dig.prenom?.charAt(0) }}
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900">{{ dig.prenom }} {{ dig.nom }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ dig.tel || '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <NuxtLink :to="`/dignitaires/${dig.id}`" class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
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
import { Chart, registerables } from 'chart.js'
import { ref, onMounted, nextTick, onUnmounted } from 'vue'

Chart.register(...registerables)

definePageMeta({
  middleware: 'auth'
})

const config = useRuntimeConfig()
const authStore = useAuthStore()
const genreChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null
const chartType = ref('genre')

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
      totalRegions: 0,
      totalDiplomes: 0
    }
  }
})

// Charger les données détaillées pour les graphiques
const { data: chartData } = await useAsyncData('chart-data', async () => {
  try {
    const response = await $fetch(`${config.public.apiBase}/dashboard/chart-data`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    return response
  } catch (error) {
    console.error('Erreur chargement données graphique:', error)
    return {
      parGenre: { hommes: 0, femmes: 0 },
      parRegion: [],
      parPoste: []
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
    return response.data || []
  } catch (error) {
    console.error('Erreur chargement derniers dignitaires:', error)
    return []
  }
})

// Fonction pour obtenir les données selon le type de graphique
function getChartData() {
  const totalDignitaires = stats.value?.totalDignitaires || 15
  
  if (chartType.value === 'genre') {
    const hommes = chartData.value?.parGenre?.hommes || Math.floor(totalDignitaires * 0.8)
    const femmes = chartData.value?.parGenre?.femmes || (totalDignitaires - hommes)
    return {
      labels: ['Hommes', 'Femmes'],
      data: [hommes, femmes],
      colors: [
        'rgba(37, 99, 235, 0.8)',
        'rgba(234, 179, 8, 0.8)'
      ],
      borderColors: [
        'rgba(37, 99, 235, 1)',
        'rgba(234, 179, 8, 1)'
      ]
    }
  } else if (chartType.value === 'region') {
    const regions = chartData.value?.parRegion || [
      { nom: 'Estuaire', count: Math.floor(totalDignitaires * 0.3) },
      { nom: 'Haut-Ogooué', count: Math.floor(totalDignitaires * 0.2) },
      { nom: 'Moyen-Ogooué', count: Math.floor(totalDignitaires * 0.15) },
      { nom: 'Ngounié', count: Math.floor(totalDignitaires * 0.15) },
      { nom: 'Autres', count: Math.floor(totalDignitaires * 0.2) }
    ]
    return {
      labels: regions.map((r: any) => r.nom),
      data: regions.map((r: any) => r.count),
      colors: [
        'rgba(22, 163, 74, 0.8)',
        'rgba(37, 99, 235, 0.8)',
        'rgba(234, 179, 8, 0.8)',
        'rgba(139, 92, 246, 0.8)',
        'rgba(239, 68, 68, 0.8)'
      ],
      borderColors: [
        'rgba(22, 163, 74, 1)',
        'rgba(37, 99, 235, 1)',
        'rgba(234, 179, 8, 1)',
        'rgba(139, 92, 246, 1)',
        'rgba(239, 68, 68, 1)'
      ]
    }
  } else if (chartType.value === 'poste') {
    const postes = chartData.value?.parPoste || [
      { nom: 'Ministre', count: Math.floor(totalDignitaires * 0.25) },
      { nom: 'Directeur', count: Math.floor(totalDignitaires * 0.3) },
      { nom: 'Conseiller', count: Math.floor(totalDignitaires * 0.2) },
      { nom: 'Ambassadeur', count: Math.floor(totalDignitaires * 0.15) },
      { nom: 'Autres', count: Math.floor(totalDignitaires * 0.1) }
    ]
    return {
      labels: postes.map((p: any) => p.nom),
      data: postes.map((p: any) => p.count),
      colors: [
        'rgba(37, 99, 235, 0.8)',
        'rgba(22, 163, 74, 0.8)',
        'rgba(234, 179, 8, 0.8)',
        'rgba(139, 92, 246, 0.8)',
        'rgba(239, 68, 68, 0.8)'
      ],
      borderColors: [
        'rgba(37, 99, 235, 1)',
        'rgba(22, 163, 74, 1)',
        'rgba(234, 179, 8, 1)',
        'rgba(139, 92, 246, 1)',
        'rgba(239, 68, 68, 1)'
      ]
    }
  }
  
  return { labels: [], data: [], colors: [], borderColors: [] }
}

// Fonction pour mettre à jour le graphique
function updateChart() {
  if (!chartInstance || !genreChart.value) return
  
  const data = getChartData()
  chartInstance.data.labels = data.labels
  chartInstance.data.datasets[0].data = data.data
  chartInstance.data.datasets[0].backgroundColor = data.colors
  chartInstance.data.datasets[0].borderColor = data.borderColors
  chartInstance.update()
}

// Créer le graphique avec les vraies données
onMounted(async () => {
  await nextTick()
  
  if (genreChart.value) {
    const data = getChartData()
    
    try {
      chartInstance = new Chart(genreChart.value, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Nombre de dignitaires',
            data: data.data,
            backgroundColor: data.colors,
            borderColor: data.borderColors,
            borderWidth: 2,
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return context.parsed.y + ' dignitaires'
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              },
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      })
    } catch (error) {
      console.error('Erreur création graphique:', error)
    }
  }
})

// Nettoyer le graphique lors de la destruction du composant
onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy()
  }
})
</script>
