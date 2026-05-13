<template>
  <DashboardLayout>
    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Bouton retour -->
      <NuxtLink to="/dignitaires" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour à la liste
      </NuxtLink>

      <div v-if="dignitaire" class="bg-white rounded-lg shadow-lg p-8">
        <!-- En-tête avec photo -->
        <div class="flex items-start gap-6 mb-8 pb-6 border-b">
          <img
            :src="dignitaire.photo ? `/uploads/photos/${dignitaire.photo}` : '/default-avatar.svg'"
            :alt="`Photo de ${dignitaire.prenom}`"
            class="w-32 h-32 rounded-full object-cover border-4 border-green-200 shadow-lg"
            @error="(e) => (e.target as HTMLImageElement).src = '/default-avatar.svg'"
          >
          <div class="flex-1">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
              {{ dignitaire.prenom }} {{ dignitaire.nom }}
            </h1>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-600">NIP:</span>
                <span class="ml-2 font-medium">{{ dignitaire.nip || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-600">Matricule:</span>
                <span class="ml-2 font-medium">{{ dignitaire.matricule }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Informations personnelles -->
        <section class="mb-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Informations personnelles
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded">
              <span class="text-gray-600 text-sm">Date de naissance</span>
              <p class="font-medium">{{ formatDate(dignitaire.date_naissance) }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
              <span class="text-gray-600 text-sm">Lieu de naissance</span>
              <p class="font-medium">
                {{ dignitaire.lieu_naissance ? dignitaire.lieu_naissance.nom : 'N/A' }}
                <span v-if="dignitaire.lieu_naissance?.pays" class="text-gray-500 text-sm">
                  ({{ dignitaire.lieu_naissance.pays.nom }})
                </span>
              </p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
              <span class="text-gray-600 text-sm">Genre</span>
              <p class="font-medium">{{ dignitaire.genre || 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
              <span class="text-gray-600 text-sm">État civil</span>
              <p class="font-medium">{{ dignitaire.etat_civil || 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
              <span class="text-gray-600 text-sm">Nationalité</span>
              <p class="font-medium">{{ dignitaire.nationalite || 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
              <span class="text-gray-600 text-sm">Téléphone</span>
              <p class="font-medium">{{ dignitaire.telephone || 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded col-span-2">
              <span class="text-gray-600 text-sm">Adresse</span>
              <p class="font-medium">{{ dignitaire.adresse || 'N/A' }}</p>
            </div>
          </div>
        </section>

        <!-- Postes -->
        <section v-if="dignitaire.postes && dignitaire.postes.length > 0" class="mb-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Postes occupés
          </h2>
          <div class="space-y-3">
            <div v-for="poste in dignitaire.postes" :key="poste.id" class="bg-gray-50 p-4 rounded border-l-4 border-green-500">
              <h3 class="font-bold text-lg text-gray-800">{{ poste.intitule }}</h3>
              <p class="text-sm text-gray-600">
                <span class="font-medium">Entité:</span> {{ poste.entite?.nom || 'N/A' }}
              </p>
              <p class="text-sm text-gray-600">
                <span class="font-medium">Ville:</span> {{ poste.ville?.nom || 'N/A' }}
              </p>
              <p class="text-sm text-gray-600">
                <span class="font-medium">Période:</span> 
                {{ formatDate(poste.date_debut) }} - {{ poste.date_fin ? formatDate(poste.date_fin) : 'En cours' }}
              </p>
            </div>
          </div>
        </section>

        <!-- Diplômes -->
        <section v-if="dignitaire.diplomes && dignitaire.diplomes.length > 0" class="mb-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
            Diplômes
          </h2>
          <div class="space-y-3">
            <div v-for="diplome in dignitaire.diplomes" :key="diplome.id" class="bg-gray-50 p-4 rounded">
              <h3 class="font-bold text-gray-800">{{ diplome.intitule }}</h3>
              <p class="text-sm text-gray-600">
                <span class="font-medium">Établissement:</span> {{ diplome.etablissement?.nom || 'N/A' }}
              </p>
              <p class="text-sm text-gray-600">
                <span class="font-medium">Domaine:</span> {{ diplome.domaine?.nom || 'N/A' }}
              </p>
              <p class="text-sm text-gray-600">
                <span class="font-medium">Année:</span> {{ diplome.annee_obtention || 'N/A' }}
              </p>
            </div>
          </div>
        </section>

        <!-- Enfants -->
        <section v-if="dignitaire.enfants && dignitaire.enfants.length > 0" class="mb-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Enfants
          </h2>
          <div class="space-y-3">
            <div v-for="enfant in dignitaire.enfants" :key="enfant.id" class="bg-gray-50 p-4 rounded">
              <h3 class="font-bold text-gray-800">{{ enfant.prenom }} {{ enfant.nom }}</h3>
              <p class="text-sm text-gray-600">
                <span class="font-medium">Date de naissance:</span> {{ formatDate(enfant.date_naissance) }}
              </p>
              <p class="text-sm text-gray-600">
                <span class="font-medium">Lieu de naissance:</span> {{ enfant.lieu_naissance?.nom || 'N/A' }}
              </p>
            </div>
          </div>
        </section>

        <!-- Décorations -->
        <section v-if="dignitaire.decorations && dignitaire.decorations.length > 0" class="mb-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
            Décorations
          </h2>
          <div class="space-y-3">
            <div v-for="decoration in dignitaire.decorations" :key="decoration.id" class="bg-gray-50 p-4 rounded">
              <h3 class="font-bold text-gray-800">{{ decoration.nom }}</h3>
              <p class="text-sm text-gray-600">
                <span class="font-medium">Date d'attribution:</span> 
                {{ decoration.pivot?.date_attribution ? formatDate(decoration.pivot.date_attribution) : 'N/A' }}
              </p>
            </div>
          </div>
        </section>
      </div>

      <div v-else class="bg-white rounded-lg shadow-lg p-8 text-center">
        <p class="text-gray-500">Chargement...</p>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const route = useRoute()
const config = useRuntimeConfig()
const authStore = useAuthStore()

const { data: dignitaire } = await useAsyncData(`dignitaire-${route.params.id}`, async () => {
  try {
    const response = await $fetch(`${config.public.apiBase}/dignitaires/${route.params.id}`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    return response
  } catch (error) {
    console.error('Erreur chargement dignitaire:', error)
    return null
  }
})

function formatDate(date: string | null) {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>
