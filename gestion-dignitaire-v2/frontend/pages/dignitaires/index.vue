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
        <p class="text-white text-sm opacity-95 drop-shadow">{{ dignitaires.length }} dignitaire(s) trouvé(s)</p>
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

      <!-- Barre de recherche et filtres -->
      <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <!-- Recherche principale -->
        <div class="flex flex-col md:flex-row gap-4 mb-4">
          <div class="flex-1">
            <input
              v-model="filters.search"
              @input="loadDignitaires"
              type="text"
              placeholder="Rechercher par nom, prénom, matricule..."
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
            >
          </div>
          <button
            v-if="permissions.peutEcrire('Dignitaire')"
            @click="openModal()"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg whitespace-nowrap"
          >
            + Ajouter un dignitaire
          </button>
        </div>

        <!-- Recherche alphabétique améliorée -->
        <div class="mb-4">
          <div class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
            </svg>
            Recherche par lettre :
          </div>
          <div class="flex flex-wrap gap-2">
            <button
              @click="filters.letter = ''; loadDignitaires()"
              :class="filters.letter === '' ? 'bg-gradient-to-br from-green-600 to-green-700 text-white shadow-lg scale-105' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'"
              class="px-4 py-2 rounded-lg font-bold text-sm transition-all duration-200 transform hover:scale-105"
            >
              Tous
            </button>
            <button
              v-for="letter in alphabet"
              :key="letter"
              @click="filters.letter = letter; loadDignitaires()"
              :class="filters.letter === letter ? 'bg-gradient-to-br from-green-600 to-green-700 text-white shadow-lg scale-105' : 'bg-white text-gray-700 hover:bg-green-50 border border-gray-200'"
              class="w-10 h-10 rounded-lg font-bold text-base transition-all duration-200 transform hover:scale-110 hover:shadow-md flex items-center justify-center"
            >
              {{ letter }}
            </button>
          </div>
        </div>

        <!-- Filtres avancés -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <select v-model="filters.genre" @change="loadDignitaires" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">Tous les genres</option>
            <option value="Homme">Homme</option>
            <option value="Femme">Femme</option>
          </select>
          
          <select v-model="filters.ville_id" @change="loadDignitaires" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">Toutes les villes</option>
            <option v-for="ville in villes" :key="ville.id" :value="ville.id">
              {{ ville.nom }}
            </option>
          </select>
          
          <select v-model="filters.entite_id" @change="loadDignitaires" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">Toutes les entités</option>
            <option v-for="entite in entites" :key="entite.id" :value="entite.id">
              {{ entite.nom }}
            </option>
          </select>

          <select v-model="filters.statut" @change="loadDignitaires" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">Tous les statuts</option>
            <option value="actif">Actif</option>
            <option value="retraite">Retraité</option>
            <option value="non_localise">Non localisé</option>
          </select>

          <div class="flex gap-2">
            <button
              @click="viewMode = 'grille'"
              :class="viewMode === 'grille' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
              class="flex-1 px-3 py-2 rounded-lg flex items-center justify-center gap-1 text-sm"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM13 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2z"/>
              </svg>
              Grille
            </button>
            <button
              @click="viewMode = 'liste'"
              :class="viewMode === 'liste' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
              class="flex-1 px-3 py-2 rounded-lg flex items-center justify-center gap-1 text-sm"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
              </svg>
              Liste
            </button>
          </div>
        </div>
      </div>

      <!-- MODE GRILLE -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>
      <div v-else-if="viewMode === 'grille'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 w-full">
        <div v-if="dignitaires.length === 0" class="col-span-full text-center py-12 text-gray-500">
          <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-1m-2.5-4a4 4 0 11-8 0 4 4 0 018 0zm-7.5 8h-5v-2a3 3 0 013-3h1"/>
          </svg>
          <p class="text-lg font-medium">Aucun dignitaire trouvé</p>
        </div>
        <div v-for="d in dignitaires" :key="d.id" class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all p-5 flex flex-col items-center group">
          <div class="relative w-full flex flex-col items-center">
            <!-- Photo -->
            <img
              :src="d.photo ? `/uploads/photos/${d.photo}` : '/default-avatar.svg'"
              :alt="`Photo de ${d.prenom} ${d.nom}`"
              class="w-28 h-28 rounded-full object-cover border-4 border-green-100 shadow-md mb-3"
              @error="(e) => (e.target as HTMLImageElement).src = '/default-avatar.svg'"
            >
            
            <!-- Nom complet -->
            <h4 class="text-lg font-bold text-gray-800 text-center mb-1">
              {{ d.prenom }} {{ d.nom }}
            </h4>

            <span class="px-2 py-0.5 rounded-full text-xs font-semibold mb-2" :class="statutBadgeClass(d.statut)">
              {{ statutLabel(d.statut) }}
            </span>

            <!-- Poste actuel avec année -->
            <div v-if="d.poste_actuel" class="text-center mb-2 w-full px-2">
              <div class="text-sm font-medium text-green-700 flex items-center justify-center gap-1">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ d.poste_actuel }}
              </div>
              <div v-if="d.poste_annee" class="text-xs text-gray-500 mt-0.5">
                {{ d.poste_annee }}
              </div>
            </div>
            <div v-else class="text-center mb-2">
              <div class="text-sm text-gray-400 italic">Aucun poste actuel</div>
            </div>

            <!-- Ville d'affectation -->
            <div v-if="d.ville_poste" class="text-xs text-gray-600 flex items-center gap-1 mb-2">
              <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              {{ d.ville_poste }}
            </div>

            <!-- Actions flottantes au hover -->
            <div class="absolute top-0 right-0 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <NuxtLink
                :to="`/dignitaires/${d.id}`"
                class="bg-sky-500 hover:bg-sky-600 text-white p-2 rounded-full shadow-lg"
                title="Voir les détails"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </NuxtLink>
              <button
                v-if="permissions.peutEcrire('Dignitaire')"
                @click="openModal(d)"
                class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-full shadow-lg"
                title="Modifier"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
              <button
                v-if="permissions.peutSupprimer()"
                @click="deleteDignitaire(d.id)"
                class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full shadow-lg"
                title="Supprimer"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
        <div v-if="dignitaires.length === 0" class="text-center py-12 text-gray-500 bg-white rounded-lg">
          <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-1m-2.5-4a4 4 0 11-8 0 4 4 0 018 0zm-7.5 8h-5v-2a3 3 0 013-3h1"/>
          </svg>
          <p class="text-lg font-medium">Aucun dignitaire trouvé</p>
        </div>

        <!-- Table liste améliorée -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-green-600 to-green-700 text-white">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Photo</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Identité</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Poste actuel</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Période</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Ville</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Entité</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Genre</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Statut</th>
                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="d in dignitaires" :key="d.id" class="hover:bg-green-50 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                  <img
                    :src="d.photo ? `/uploads/photos/${d.photo}` : '/default-avatar.svg'"
                    :alt="`Photo de ${d.prenom} ${d.nom}`"
                    class="w-14 h-14 rounded-full object-cover border-2 border-green-200 shadow-sm"
                    @error="(e) => (e.target as HTMLImageElement).src = '/default-avatar.svg'"
                  >
                </td>
                <td class="px-4 py-3">
                  <div class="font-bold text-gray-900">{{ d.prenom }} {{ d.nom }}</div>
                  <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                    {{ d.matricule }}
                  </div>
                  <div v-if="d.lieu_naissance_nom" class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Né(e) à {{ d.lieu_naissance_nom }}
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div v-if="d.poste_actuel" class="text-sm">
                    <div class="font-semibold text-green-700 flex items-center gap-1">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                      {{ d.poste_actuel }}
                    </div>
                  </div>
                  <div v-else class="text-sm text-gray-400 italic flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    Aucun poste
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div v-if="d.poste_annee" class="text-sm text-gray-700 flex items-center gap-1">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ d.poste_annee }}
                  </div>
                  <div v-else class="text-sm text-gray-400">—</div>
                </td>
                <td class="px-4 py-3">
                  <div v-if="d.ville_poste" class="text-sm text-gray-700 flex items-center gap-1">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ d.ville_poste }}
                  </div>
                  <div v-else class="text-sm text-gray-400">—</div>
                </td>
                <td class="px-4 py-3">
                  <div v-if="d.nom_entite" class="text-sm text-gray-700">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                      {{ d.nom_entite }}
                    </span>
                  </div>
                  <div v-else class="text-sm text-gray-400">—</div>
                </td>
                <td class="px-4 py-3">
                  <span v-if="d.genre === 'Homme'" class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                    Homme
                  </span>
                  <span v-else-if="d.genre === 'Femme'" class="bg-pink-100 text-pink-800 px-2 py-1 rounded-full text-xs font-medium">
                    Femme
                  </span>
                  <span v-else class="text-sm text-gray-400">—</span>
                </td>
                <td class="px-4 py-3">
                  <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="statutBadgeClass(d.statut)">
                    {{ statutLabel(d.statut) }}
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <NuxtLink
                      :to="`/dignitaires/${d.id}`"
                      class="bg-sky-500 hover:bg-sky-600 text-white p-2 rounded-lg shadow transition-all"
                      title="Voir les détails"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </NuxtLink>
                    <button
                      v-if="permissions.peutEcrire('Dignitaire')"
                      @click="openModal(d)"
                      class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg shadow transition-all"
                      title="Modifier"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button
                      v-if="permissions.peutSupprimer()"
                      @click="deleteDignitaire(d.id)"
                      class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow transition-all"
                      title="Supprimer"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
    </div>

    <!-- Modal Ajout/Modification améliorée -->
    <div v-if="showModal" class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" @click.self="closeModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative">
        <!-- Header de la modal -->
        <div class="sticky top-0 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between">
          <div class="flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h3 class="text-xl font-bold">{{ selectedDignitaire ? 'Modifier' : 'Ajouter' }} un dignitaire</h3>
          </div>
          <button 
            @click="closeModal" 
            class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-all"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Corps de la modal -->
        <form @submit.prevent="saveDignitaire" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- NIP -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                  NIP
                </span>
              </label>
              <input 
                v-model="form.nip" 
                type="text"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                placeholder="Numéro d'identification"
              >
            </div>

            <!-- Matricule -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                  </svg>
                  Matricule <span class="text-red-500">*</span>
                </span>
              </label>
              <input 
                v-model="form.matricule" 
                type="text"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                placeholder="Matricule"
                required
              >
            </div>

            <!-- Nom -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Nom <span class="text-red-500">*</span>
                </span>
              </label>
              <input 
                v-model="form.nom" 
                type="text"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                placeholder="Nom de famille"
                required
              >
            </div>

            <!-- Prénom -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Prénom <span class="text-red-500">*</span>
                </span>
              </label>
              <input 
                v-model="form.prenom" 
                type="text"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                placeholder="Prénom"
                required
              >
            </div>

            <!-- Date de naissance -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  Date de naissance <span class="text-red-500">*</span>
                </span>
              </label>
              <input 
                v-model="form.date_naissance" 
                type="date"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                required
              >
            </div>

            <!-- Lieu de naissance -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  Lieu de naissance <span class="text-red-500">*</span>
                </span>
              </label>
              <select 
                v-model="form.lieu_naissance" 
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                required
              >
                <option value="">Sélectionner une ville</option>
                <option v-for="ville in villes" :key="ville.id" :value="ville.id">
                  {{ ville.nom }}
                </option>
              </select>
            </div>

            <!-- Genre -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                  Genre <span class="text-red-500">*</span>
                </span>
              </label>
              <select 
                v-model="form.genre" 
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                required
              >
                <option value="">Sélectionner le genre</option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
              </select>
            </div>

            <!-- État civil -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                  </svg>
                  État civil <span class="text-red-500">*</span>
                </span>
              </label>
              <select 
                v-model="form.etat_civil" 
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                required
              >
                <option value="">Sélectionner l'état civil</option>
                <option value="Célibataire">Célibataire</option>
                <option value="Marié(e)">Marié(e)</option>
                <option value="Divorcé(e)">Divorcé(e)</option>
                <option value="Veuf(ve)">Veuf(ve)</option>
              </select>
            </div>

            <!-- Statut -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Statut</label>
              <select
                v-model="form.statut"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
              >
                <option value="actif">Actif</option>
                <option value="retraite">Retraité</option>
                <option value="non_localise">Non localisé</option>
              </select>
            </div>

            <!-- Photo -->
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  Nom du fichier photo
                </span>
              </label>
              <input 
                v-model="form.photo" 
                type="text"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                placeholder="exemple: photo.jpg"
              >
              <p class="text-xs text-gray-500 mt-1">Le fichier doit être uploadé dans /uploads/photos/</p>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200">
            <button 
              type="button"
              @click="closeModal"
              class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition-all"
            >
              Annuler
            </button>
            <button 
              type="submit"
              class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-lg transition-all transform hover:scale-105"
            >
              {{ selectedDignitaire ? '✓ Modifier' : '+ Enregistrer' }}
            </button>
          </div>
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
const permissions = usePermissions()
const showModal = ref(false)
const selectedDignitaire = ref(null)
const viewMode = ref('grille')

// Alphabet pour la recherche par lettre
const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('')

const filters = reactive({
  search: '',
  letter: '',
  genre: '',
  ville_id: '',
  entite_id: '',
  statut: ''
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
  photo: '',
  statut: 'actif'
})

function statutLabel(statut: string) {
  return { actif: 'Actif', retraite: 'Retraité', non_localise: 'Non localisé' }[statut] || 'Actif'
}

function statutBadgeClass(statut: string) {
  const classes: Record<string, string> = {
    actif: 'bg-green-100 text-green-700',
    retraite: 'bg-gray-200 text-gray-700',
    non_localise: 'bg-orange-100 text-orange-700'
  }
  return classes[statut] || classes.actif
}

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
    
    // Recherche par texte
    if (filters.search) params.append('search', filters.search)
    
    // Recherche par lettre (filtre côté client pour l'instant)
    if (filters.genre) params.append('genre', filters.genre)
    if (filters.ville_id) params.append('ville_id', filters.ville_id)
    if (filters.entite_id) params.append('entite_id', filters.entite_id)
    if (filters.statut) params.append('statut', filters.statut)
    params.append('per_page', '100')
    
    const response = await $fetch(`${config.public.apiBase}/dignitaires?${params.toString()}`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    
    let data = response.data || []
    
    // Filtre par lettre côté client
    if (filters.letter) {
      data = data.filter(d => d.nom.toUpperCase().startsWith(filters.letter))
    }
    
    // Tri alphabétique par nom
    data.sort((a, b) => {
      const nomA = a.nom.toUpperCase()
      const nomB = b.nom.toUpperCase()
      return nomA.localeCompare(nomB)
    })
    
    // Enrichir avec l'année du poste
    dignitaires.value = data.map(d => {
      if (d.poste_actuel) {
        // Récupérer l'année depuis les postes si disponible
        d.poste_annee = 'En cours'
      }
      return d
    })
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
    form.statut = dignitaire.statut || 'actif'
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
    form.statut = 'actif'
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
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedDignitaire.value ? 'Dignitaire modifié avec succès' : 'Dignitaire ajouté avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeModal()
    loadDignitaires()
  } catch (error) {
    console.error('Erreur sauvegarde:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: 'Une erreur est survenue lors de la sauvegarde'
    })
  }
}

async function deleteDignitaire(id: number) {
  const { $swal } = useNuxtApp()
  const result = await $swal.fire({
    title: 'Êtes-vous sûr ?',
    text: 'Cette action supprimera définitivement ce dignitaire',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#dc2626',
    confirmButtonText: 'Oui, supprimer',
    cancelButtonText: 'Annuler'
  })
  
  if (result.isConfirmed) {
    try {
      await $fetch(`${config.public.apiBase}/dignitaires/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'Le dignitaire a été supprimé avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      loadDignitaires()
    } catch (error) {
      console.error('Erreur suppression:', error)
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Une erreur est survenue lors de la suppression'
      })
    }
  }
}
</script>
