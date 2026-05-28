<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <!-- Header moderne avec gradient gabonais -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-1m-2.5-4a4 4 0 11-8 0 4 4 0 018 0zm-7.5 8h-5v-2a3 3 0 013-3h1"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Dignitaires</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Gérer les dignitaires et leurs informations</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <!-- Dashboard Statistiques modernisé -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Nombre de dignitaires -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-gabon-green-600 p-5 hover:shadow-xl transition-shadow">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="text-gray-500 text-sm font-medium mb-1">Nombre de dignitaires</div>
              <div class="text-3xl font-bold text-gray-800">{{ stats?.totalDignitaires || 0 }}</div>
            </div>
            <div class="bg-gabon-green-100 p-3 rounded-full">
              <svg class="w-8 h-8 text-gabon-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-3-3h-1m-2.5-4a4 4 0 11-8 0 4 4 0 018 0zm-7.5 8h-5v-2a3 3 0 013-3h1"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Nombres de postes -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-gabon-yellow-500 p-5 hover:shadow-xl transition-shadow">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="text-gray-500 text-sm font-medium mb-1">Nombres de postes</div>
              <div class="text-3xl font-bold text-gray-800">{{ stats?.totalPostes || 0 }}</div>
            </div>
            <div class="bg-gabon-yellow-100 p-3 rounded-full">
              <svg class="w-8 h-8 text-gabon-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Décorations données -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-gabon-blue-600 p-5 hover:shadow-xl transition-shadow">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="text-gray-500 text-sm font-medium mb-1">Décorations données</div>
              <div class="text-3xl font-bold text-gray-800">{{ stats?.totalDecorations || 0 }}</div>
            </div>
            <div class="bg-gabon-blue-100 p-3 rounded-full">
              <svg class="w-8 h-8 text-gabon-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Villes d'affectation -->
        <div class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-5 hover:shadow-xl transition-shadow">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="text-gray-500 text-sm font-medium mb-1">Villes d'affectation</div>
              <div class="text-3xl font-bold text-gray-800">{{ stats?.totalVilles || 0 }}</div>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
              <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Boutons Mode d'affichage -->
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-3xl font-bold">Gestion des Dignitaires</h2>
        <div class="flex gap-2">
          <button
            @click="viewMode = 'grille'"
            :class="viewMode === 'grille' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
            class="px-3 py-1 rounded flex items-center gap-1"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM13 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2z"/>
            </svg>
            Grille
          </button>
          <button
            @click="viewMode = 'liste'"
            :class="viewMode === 'liste' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
            class="px-3 py-1 rounded flex items-center gap-1"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
            </svg>
            Liste
          </button>
        </div>
      </div>

      <!-- Bouton d'ajout -->
      <button
        @click="openModal()"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-6"
      >
        Ajouter un dignitaire
      </button>

      <!-- MODE GRILLE -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>
      <div v-else-if="viewMode === 'grille'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 w-full">
        <div v-for="d in dignitaires" :key="d.id" class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center">
          <div class="relative group w-full flex flex-col items-center">
            <img
              :src="d.photo ? `/uploads/photos/${d.photo}` : '/default-avatar.svg'"
              :alt="`Photo de ${d.prenom}`"
              class="w-24 h-24 rounded-full object-cover border-4 border-green-200 shadow mb-2"
              @error="(e) => (e.target as HTMLImageElement).src = '/default-avatar.svg'"
            >
            <h4 class="text-base font-semibold mb-0.5">{{ d.prenom }} {{ d.nom }}</h4>
            
            <!-- Postes -->
            <div v-if="d.postes && d.postes.length > 0" class="mt-1 mb-1 text-center">
              <div v-for="poste in d.postes" :key="poste.id" class="mb-2">
                <div class="font-semibold text-green-700 text-sm flex items-center gap-1 justify-center">
                  <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 7v4m0 0v4m0-4h4m-4 0H8m12 0a2 2 0 002-2V7a2 2 0 00-2-2h-2.586A2 2 0 0015 3.586L13.414 2H10.586L9 3.586A2 2 0 007.586 5H5a2 2 0 00-2 2v2a2 2 0 002 2h16z"/>
                  </svg>
                  {{ poste.intitule }}
                </div>
                <div class="text-gray-400 text-xs flex items-center gap-1 justify-center">
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M8 7V3m8 4V3m-9 8h10m-4 4h4m-4 4h4m1 1a2 2 0 002-2V7a2 2 0 00-2-2h-1.5"/>
                  </svg>
                  {{ formatDateRange(poste.date_debut, poste.date_fin) }}
                </div>
              </div>
            </div>

            <!-- Actions flottantes au hover -->
            <div class="absolute top-3 right-3 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition">
              <NuxtLink
                :to="`/dignitaires/${d.id}`"
                class="bg-sky-100 hover:bg-sky-200 text-sky-800 p-1.5 rounded-full shadow"
                title="Voir"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 0a9 9 0 1118 0 9 9 0 01-18 0z"/>
                </svg>
              </NuxtLink>
              <button
                @click="openModal(d)"
                class="bg-blue-100 hover:bg-blue-200 text-blue-800 p-1.5 rounded-full shadow"
                title="Modifier"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M15.232 5.232l3.536 3.536M9 13l6 6M9 13l-3-3a2 2 0 112.828-2.828l3 3z"/>
                </svg>
              </button>
              <button
                @click="deleteDignitaire(d.id)"
                class="bg-red-100 hover:bg-red-200 text-red-700 p-1.5 rounded-full shadow"
                title="Supprimer"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- MODE LISTE -->
      <div v-else-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>
      <div v-else>
        <!-- Header avec recherche -->
        <header class="bg-green-600 text-white p-4 shadow-md rounded-t-lg mb-4">
          <div class="max-w-5xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
              <svg class="w-7 h-7 text-white mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <path d="M8 12l2 2 4-4" stroke="white" stroke-width="2" fill="none"/>
              </svg>
              Gestion des Dignitaires
            </h1>
            <form @submit.prevent="loadDignitaires" class="flex items-center w-full sm:w-auto gap-0.5">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Rechercher dignitaire..."
                class="border-none rounded-l-lg px-4 py-2 text-gray-700 focus:ring-2 focus:ring-yellow-400 focus:outline-none w-52 sm:w-64"
              >
              <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-green-900 px-4 py-2 rounded-r-lg font-semibold shadow">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="8" />
                  <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
              </button>
            </form>
          </div>
        </header>

        <!-- Filtres -->
        <form @submit.prevent="loadDignitaires" class="bg-white p-4 mb-6 rounded-lg shadow-md">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Ville</label>
              <select v-model="filters.ville_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
                <option value="">Toutes les villes</option>
                <option v-for="ville in villes" :key="ville.id" :value="ville.id">
                  {{ ville.nom }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Entité</label>
              <select v-model="filters.entite_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
                <option value="">Toutes les entités</option>
                <option v-for="entite in entites" :key="entite.id" :value="entite.id">
                  {{ entite.nom }}
                </option>
              </select>
            </div>
            <div class="flex items-end">
              <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Filtrer
              </button>
            </div>
          </div>
        </form>

        <!-- Cartes liste -->
        <main class="container mx-auto p-6">
          <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <div v-if="dignitaires.length === 0" class="col-span-full text-center py-6 text-gray-500">
              Aucun dignitaire trouvé.
            </div>
            <div
              v-for="dignitaire in dignitaires"
              :key="dignitaire.id"
              class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-all duration-300 relative group"
            >
              <h3 class="text-lg font-bold text-green-800 mb-1">
                {{ dignitaire.prenom }} {{ dignitaire.nom }}
              </h3>
              <p class="text-sm text-gray-600 mb-1">
                <span class="font-medium">Lieu de naissance :</span>
                {{ dignitaire.lieu_naissance_nom || 'N/A' }}
              </p>
              <p class="text-sm text-gray-600 mb-1">
                <span class="font-medium">Ville d'affectation :</span>
                {{ dignitaire.ville_poste || 'Aucune' }}
              </p>
              <p class="text-sm text-gray-600 mb-1">
                <span class="font-medium">Poste actuel :</span>
                {{ dignitaire.poste_actuel || 'Aucun poste actuel' }}
              </p>
              <p class="text-sm text-gray-600 mb-1">
                <span class="font-medium">Entité :</span>
                {{ dignitaire.nom_entite || 'Aucune entité actuelle' }}
              </p>

              <!-- Actions flottantes -->
              <div class="absolute top-3 right-3 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition">
                <NuxtLink
                  :to="`/dignitaires/${dignitaire.id}`"
                  class="bg-sky-100 hover:bg-sky-200 text-sky-800 p-1.5 rounded-full shadow"
                  title="Voir"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 0a9 9 0 1118 0 9 9 0 01-18 0z"/>
                  </svg>
                </NuxtLink>
                <button
                  @click="openModal(dignitaire)"
                  class="bg-blue-100 hover:bg-blue-200 text-blue-800 p-1.5 rounded-full shadow"
                  title="Modifier"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15.232 5.232l3.536 3.536M9 13l6 6M9 13l-3-3a2 2 0 112.828-2.828l3 3z"/>
                  </svg>
                </button>
                <button
                  @click="deleteDignitaire(dignitaire.id)"
                  class="bg-red-100 hover:bg-red-200 text-red-700 p-1.5 rounded-full shadow"
                  title="Supprimer"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </main>
      </div>
    </section>
    </div>

    <!-- Modal Ajout/Modification -->
    <div v-if="showModal" class="fixed z-50 inset-0 bg-black bg-opacity-30 flex items-center justify-center" @click.self="closeModal">
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 text-2xl" @click="closeModal">&times;</button>
        <h4 class="text-lg font-bold mb-4">{{ selectedDignitaire ? 'Modifier' : 'Ajouter' }} un dignitaire</h4>
        <form @submit.prevent="saveDignitaire" class="flex flex-col gap-2">
          <input v-model="form.nip" class="border rounded px-2 py-1" placeholder="NIP" required>
          <input v-model="form.matricule" class="border rounded px-2 py-1" placeholder="Matricule" required>
          <input v-model="form.nom" class="border rounded px-2 py-1" placeholder="Nom" required>
          <input v-model="form.prenom" class="border rounded px-2 py-1" placeholder="Prénom" required>
          <input v-model="form.date_naissance" class="border rounded px-2 py-1" type="date" required>
          
          <!-- Select pour lieu de naissance -->
          <select v-model="form.lieu_naissance" class="border rounded px-2 py-1" required>
            <option value="">Sélectionner le lieu de naissance</option>
            <option v-for="ville in villes" :key="ville.id" :value="ville.id">
              {{ ville.nom }}
            </option>
          </select>
          
          <select v-model="form.genre" class="border rounded px-2 py-1" required>
            <option value="">Genre</option>
            <option value="Homme">Homme</option>
            <option value="Femme">Femme</option>
          </select>
          <input v-model="form.etat_civil" class="border rounded px-2 py-1" placeholder="État civil" required>
          <input v-model="form.photo" class="border rounded px-2 py-1" placeholder="Nom du fichier photo">
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">
            {{ selectedDignitaire ? 'Modifier' : 'Enregistrer' }}
          </button>
        </form>
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
const showModal = ref(false)
const selectedDignitaire = ref(null)
const viewMode = ref('grille')

const filters = reactive({
  search: '',
  ville_id: '',
  entite_id: ''
})

const form = reactive({
  nip: '',
  matricule: '',
  nom: '',
  prenom: '',
  date_naissance: '',
  lieu_naissance: '',
  genre: '',
  etat_civil: '',
  photo: ''
})

// Charger les statistiques (lazy loading)
const stats = ref({ totalDignitaires: 0, totalPostes: 0, totalDecorations: 0, totalVilles: 0 })

onMounted(async () => {
  // Charger les stats en arrière-plan
  try {
    const response = await $fetch(`${config.public.apiBase}/dashboard/stats`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    stats.value = response
  } catch (error) {
    console.error('Erreur stats:', error)
  }
})

// Charger les dignitaires (sans bloquer le rendu)
const dignitaires = ref([])
const loading = ref(true)

async function loadDignitaires() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.ville_id) params.append('ville_id', filters.ville_id)
    if (filters.entite_id) params.append('entite_id', filters.entite_id)
    params.append('per_page', '50')
    
    const response = await $fetch(`${config.public.apiBase}/dignitaires?${params.toString()}`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    dignitaires.value = response.data || []
  } catch (error) {
    console.error('Erreur dignitaires:', error)
    dignitaires.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadDignitaires()
})

// Charger les villes et entités avec cache
const referentiels = useReferentiels()
const villes = ref([])
const entites = ref([])

onMounted(async () => {
  villes.value = await referentiels.getVilles()
  entites.value = await referentiels.getEntites()
})

function formatDateRange(dateDebut: string, dateFin: string | null) {
  const anneeDebut = dateDebut ? new Date(dateDebut).getFullYear() : '—'
  const anneeFin = dateFin ? new Date(dateFin).getFullYear() : 'à ce jour'
  return `${anneeDebut} - ${anneeFin}`
}

function openModal(dignitaire: any = null) {
  selectedDignitaire.value = dignitaire
  if (dignitaire) {
    form.nip = dignitaire.nip
    form.matricule = dignitaire.matricule
    form.nom = dignitaire.nom
    form.prenom = dignitaire.prenom
    form.date_naissance = dignitaire.date_naissance
    form.lieu_naissance = dignitaire.lieu_naissance
    form.genre = dignitaire.genre
    form.etat_civil = dignitaire.etat_civil
    form.photo = dignitaire.photo || ''
  } else {
    // Reset form
    form.nip = ''
    form.matricule = ''
    form.nom = ''
    form.prenom = ''
    form.date_naissance = ''
    form.lieu_naissance = ''
    form.genre = ''
    form.etat_civil = ''
    form.photo = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedDignitaire.value = null
}

async function saveDignitaire() {
  try {
    if (selectedDignitaire.value) {
      await $fetch(`${config.public.apiBase}/dignitaires/${selectedDignitaire.value.id}`, {
        method: 'PUT',
        body: form,
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
    } else {
      await $fetch(`${config.public.apiBase}/dignitaires`, {
        method: 'POST',
        body: form,
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
    }
    closeModal()
    loadDignitaires()
  } catch (error) {
    console.error('Erreur sauvegarde:', error)
    alert('Erreur lors de la sauvegarde')
  }
}

async function deleteDignitaire(id: number) {
  if (confirm('Supprimer ce dignitaire ?')) {
    try {
      await $fetch(`${config.public.apiBase}/dignitaires/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
      loadDignitaires()
    } catch (error) {
      console.error('Erreur suppression:', error)
      alert('Erreur lors de la suppression')
    }
  }
}
</script>
