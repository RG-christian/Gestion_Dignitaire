<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <!-- Header moderne avec gradient gabonais -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Postes & Entités</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Gérer les postes, nominations et entités organisationnelles</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <!-- Onglets -->
      <div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="flex border-b">
          <button
            @click="activeTab = 'postes'"
            :class="[
              'flex-1 px-6 py-4 font-semibold transition-all duration-300 flex items-center justify-center gap-2',
              activeTab === 'postes' 
                ? 'bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 text-white border-b-4 border-gabon-green-800' 
                : 'bg-gray-50 text-gray-600 hover:bg-gray-100'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Postes ({{ postes.length }})
          </button>
          <button
            @click="activeTab = 'entites'"
            :class="[
              'flex-1 px-6 py-4 font-semibold transition-all duration-300 flex items-center justify-center gap-2',
              activeTab === 'entites' 
                ? 'bg-gradient-to-r from-gabon-blue-600 to-sky-600 text-white border-b-4 border-gabon-blue-800' 
                : 'bg-gray-50 text-gray-600 hover:bg-gray-100'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Entités ({{ entitesData.length }})
          </button>
        </div>
      </div>

      <!-- Contenu Onglet Postes -->
      <div v-show="activeTab === 'postes'">
        <!-- Barre de recherche et filtres -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
          <div class="flex flex-col md:flex-row gap-4 items-center">
            <div class="flex-1 w-full">
              <SearchInput
                v-model="filters.search"
                placeholder="Rechercher (intitulé, dignitaire, entité, ville)..."
                @update:modelValue="debouncedLoadPostes"
              />
            </div>
            <div class="w-full md:w-64">
              <select
                v-model="filters.dignitaire_id"
                @change="loadPostes"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
              >
                <option value="">Tous les dignitaires</option>
                <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                  {{ dig.prenom }} {{ dig.nom }}
                </option>
              </select>
            </div>
            <button
              v-if="permissions.peutEcrire('Poste')"
              @click="openPosteModal()"
              class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Ajouter
            </button>
          </div>
        </div>

        <!-- Loader -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <div class="relative">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-green-600 border-t-transparent absolute top-0 left-0"></div>
          </div>
        </div>

        <!-- Table Postes -->
        <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div v-if="paginatedPostes.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Intitulé</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dignitaire</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ville</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Entité</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Période</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="poste in paginatedPostes" :key="poste.id" class="hover:bg-gabon-green-50 transition-colors duration-150">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-semibold text-gray-900">{{ poste.intitule }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ poste.dignitaire_nom }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ poste.ville_nom || 'N/A' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ poste.entite_nom || 'N/A' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    {{ formatDate(poste.date_debut) }} -
                    <span v-if="poste.statut === 'terminee'">
                      {{ formatDate(poste.date_fin) }}
                      <span class="block text-xs text-gray-500">{{ motifFinLabel(poste.motif_fin) }}</span>
                    </span>
                    <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">En cours</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="openPosteDetailModal(poste)" class="inline-flex items-center gap-1 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Détail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Détail
                      </button>
                      <button v-if="poste.statut !== 'terminee' && permissions.peutEcrire('Poste')" @click="openClotureModal(poste)" class="inline-flex items-center gap-1 bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Clôturer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Clôturer
                      </button>
                      <button v-if="permissions.peutEcrire('Poste')" @click="openPosteModal(poste)" class="inline-flex items-center gap-1 bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Modifier">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                      </button>
                      <button v-if="permissions.peutSupprimer()" @click="deletePoste(poste.id)" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Supprimer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <p class="mt-4 text-gray-500 text-lg">Aucun poste enregistré</p>
          </div>
          <Pagination
            v-if="postes.length > 0"
            :current-page="currentPagePostes"
            :total-pages="totalPagesPostes"
            :start-index="startIndexPostes"
            :end-index="endIndexPostes"
            :total="postes.length"
            @update:current-page="currentPagePostes = $event"
          />
        </div>
      </div>

      <!-- Contenu Onglet Entités -->
      <div v-show="activeTab === 'entites'">
        <!-- Barre de recherche -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
          <div class="flex flex-col md:flex-row gap-4 items-center">
            <div class="flex-1 w-full">
              <SearchInput
                v-model="filtersEntites.search"
                placeholder="Rechercher une entité..."
                @update:modelValue="debouncedLoadEntites"
              />
            </div>
            <button
              @click="openEntiteModal()"
              class="bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 hover:from-gabon-blue-700 hover:to-gabon-blue-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Ajouter
            </button>
          </div>
        </div>

        <!-- Loader -->
        <div v-if="loadingEntites" class="flex justify-center items-center py-20">
          <div class="relative">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-blue-600 border-t-transparent absolute top-0 left-0"></div>
          </div>
        </div>

        <!-- Table Entités -->
        <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div v-if="paginatedEntites.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nom</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Entité parente</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="entite in paginatedEntites" :key="entite.id" class="hover:bg-gabon-blue-50 transition-colors duration-150">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-semibold text-gray-900">{{ entite.nom }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="entite.type" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gabon-blue-100 text-gabon-blue-800">
                      {{ entite.type }}
                    </span>
                    <span v-else class="text-sm text-gray-400">N/A</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ entite.parent?.nom || 'N/A' }}</td>
                  <td class="px-6 py-4 text-sm text-gray-700">
                    <span class="line-clamp-2">{{ entite.description || 'N/A' }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="openEntiteDetailModal(entite)" class="inline-flex items-center gap-1 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Détail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Détail
                      </button>
                      <button @click="openEntiteModal(entite)" class="inline-flex items-center gap-1 bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Modifier">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                      </button>
                      <button @click="deleteEntite(entite.id)" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 font-semibold px-3 py-2 rounded-lg transition-colors" title="Supprimer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
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
            v-if="entitesData.length > 0"
            :current-page="currentPageEntites"
            :total-pages="totalPagesEntites"
            :start-index="startIndexEntites"
            :end-index="endIndexEntites"
            :total="entitesData.length"
            @update:current-page="currentPageEntites = $event"
          />
        </div>
      </div>
    </section>
    </div>

    <!-- MODALS POSTES -->
    <!-- Modal Ajout/Modification Poste -->
    <div v-if="showPosteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closePosteModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            {{ selectedPoste ? 'Modifier' : 'Ajouter' }} un poste
          </h4>
          <button @click="closePosteModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="savePoste" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Dignitaire <span class="text-red-500">*</span></label>
              <select v-model="formPoste.dignitaire_id" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Choisir un dignitaire --</option>
                <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">{{ dig.prenom }} {{ dig.nom }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Intitulé du poste <span class="text-red-500">*</span></label>
              <input v-model="formPoste.intitule" type="text" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Ex: Ministre de l'Économie">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Entité</label>
              <select v-model="formPoste.entite_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Sélectionner une entité --</option>
                <option v-for="entite in entites" :key="entite.id" :value="entite.id">{{ entite.nom }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Ville</label>
              <select v-model="formPoste.ville_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Sélectionner une ville --</option>
                <option v-for="ville in villes" :key="ville.id" :value="ville.id">{{ ville.nom }}</option>
              </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de début</label>
                <input v-model="formPoste.date_debut" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
                <input v-model="formPoste.date_fin" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>
          </div>
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closePosteModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
              {{ selectedPoste ? 'Modifier' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Détail Poste -->
    <div v-if="showPosteDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closePosteDetailModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-gabon-blue-600 to-sky-600 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Détail du Poste
          </h4>
          <button @click="closePosteDetailModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div v-if="selectedPosteDetail" class="p-6">
          <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Intitulé</p>
              <p class="text-lg font-bold text-gray-900">{{ selectedPosteDetail.intitule }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Dignitaire</p>
              <p class="text-lg text-gray-900">{{ selectedPosteDetail.dignitaire_nom }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Entité</p>
                <p class="text-gray-900">{{ selectedPosteDetail.entite_nom || 'N/A' }}</p>
              </div>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Ville</p>
                <p class="text-gray-900">{{ selectedPosteDetail.ville_nom || 'N/A' }}</p>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Date de début</p>
                <p class="text-gray-900">{{ formatDate(selectedPosteDetail.date_debut) }}</p>
              </div>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Statut</p>
                <p class="text-gray-900">
                  {{ selectedPosteDetail.statut === 'terminee' ? `Terminé (${formatDate(selectedPosteDetail.date_fin)})` : 'En cours' }}
                  <span v-if="selectedPosteDetail.statut === 'terminee'" class="block text-sm text-gray-500">{{ motifFinLabel(selectedPosteDetail.motif_fin) }}</span>
                </p>
              </div>
            </div>
          </div>
          <div class="mt-6 pt-4 border-t">
            <button @click="closePosteDetailModal" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Fermer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Clôture Poste -->
    <div v-if="showClotureModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeClotureModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
          <h4 class="text-xl font-bold text-white">Clôturer le poste</h4>
        </div>
        <form @submit.prevent="confirmCloture" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Motif <span class="text-red-500">*</span></label>
            <div class="space-y-2">
              <label class="flex items-center gap-2 border rounded-lg px-4 py-3 cursor-pointer hover:bg-gray-50">
                <input type="radio" v-model="clotureForm.motif_fin" value="fin_fonction" required>
                <span>Fin de fonction formelle</span>
              </label>
              <label class="flex items-center gap-2 border rounded-lg px-4 py-3 cursor-pointer hover:bg-gray-50">
                <input type="radio" v-model="clotureForm.motif_fin" value="mise_a_disposition" required>
                <span>Mise à disposition de l'administration d'origine</span>
              </label>
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
            <input v-model="clotureForm.date_fin" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
          </div>
          <div class="flex gap-3 pt-2">
            <button type="button" @click="closeClotureModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition">Confirmer</button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODALS ENTITÉS -->
    <!-- Modal Ajout/Modification Entité -->
    <div v-if="showEntiteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeEntiteModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            {{ selectedEntite ? 'Modifier' : 'Ajouter' }} une entité
          </h4>
          <button @click="closeEntiteModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveEntite" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nom <span class="text-red-500">*</span></label>
              <input v-model="formEntite.nom" type="text" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition" placeholder="Ex: Ministère de l'Économie">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
              <input v-model="formEntite.type" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition" placeholder="Ex: Ministère, Direction, Service...">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Entité parente</label>
              <select v-model="formEntite.id_sup" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition">
                <option :value="null">-- Aucune (entité racine) --</option>
                <option v-for="e in entitesData.filter(ent => ent.id !== selectedEntite?.id)" :key="e.id" :value="e.id">{{ e.nom }}</option>
              </select>
              <p class="text-xs text-gray-500 mt-1">Sélectionnez une entité parente si cette entité dépend d'une autre</p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
              <textarea v-model="formEntite.description" rows="3" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition" placeholder="Description de l'entité..."></textarea>
            </div>
          </div>
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeEntiteModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 hover:from-gabon-blue-700 hover:to-gabon-blue-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
              {{ selectedEntite ? 'Modifier' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Détail Entité -->
    <div v-if="showEntiteDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeEntiteDetailModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-gabon-blue-600 to-sky-600 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Détail de l'entité
          </h4>
          <button @click="closeEntiteDetailModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div v-if="selectedEntiteDetail" class="p-6">
          <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Nom</p>
              <p class="text-lg font-bold text-gray-900">{{ selectedEntiteDetail.nom }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Type</p>
                <p class="text-gray-900">{{ selectedEntiteDetail.type || 'N/A' }}</p>
              </div>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Entité parente</p>
                <p class="text-gray-900">{{ selectedEntiteDetail.parent?.nom || 'Aucune' }}</p>
              </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Description</p>
              <p class="text-gray-900">{{ selectedEntiteDetail.description || 'N/A' }}</p>
            </div>
            <div v-if="selectedEntiteDetail.enfants && selectedEntiteDetail.enfants.length > 0" class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-2">Entités dépendantes ({{ selectedEntiteDetail.enfants.length }})</p>
              <div class="flex flex-wrap gap-2">
                <span v-for="enfant in selectedEntiteDetail.enfants" :key="enfant.id" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gabon-blue-100 text-gabon-blue-800">
                  {{ enfant.nom }}
                </span>
              </div>
            </div>
          </div>
          <div class="mt-6 pt-4 border-t">
            <button @click="closeEntiteDetailModal" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Fermer</button>
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
const permissions = usePermissions()
const referentiels = useReferentiels()
const { debounce } = useDebounce()

// État général
const activeTab = ref('postes')

// POSTES
const postes = ref([])
const dignitaires = ref([])
const entites = ref([])
const villes = ref([])
const loading = ref(true)
const showPosteModal = ref(false)
const showPosteDetailModal = ref(false)
const showClotureModal = ref(false)
const selectedPoste = ref(null)
const selectedPosteDetail = ref(null)
const posteToClose = ref(null)
const currentPagePostes = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: '',
  dignitaire_id: ''
})

const formPoste = reactive({
  dignitaire_id: '',
  intitule: '',
  entite_id: '',
  ville_id: '',
  date_debut: '',
  date_fin: ''
})

const clotureForm = reactive({
  motif_fin: '',
  date_fin: new Date().toISOString().slice(0, 10)
})

function motifFinLabel(motif) {
  if (motif === 'mise_a_disposition') return 'Mise à disposition'
  if (motif === 'fin_fonction') return 'Fin de fonction'
  return ''
}

// ENTITÉS
const entitesData = ref([])
const loadingEntites = ref(false)
const showEntiteModal = ref(false)
const showEntiteDetailModal = ref(false)
const selectedEntite = ref(null)
const selectedEntiteDetail = ref(null)
const currentPageEntites = ref(1)

const filtersEntites = reactive({
  search: ''
})

const formEntite = reactive({
  nom: '',
  type: '',
  id_sup: null,
  description: ''
})

// Pagination Postes
const totalPagesPostes = computed(() => Math.ceil(postes.value.length / itemsPerPage))
const startIndexPostes = computed(() => (currentPagePostes.value - 1) * itemsPerPage)
const endIndexPostes = computed(() => Math.min(startIndexPostes.value + itemsPerPage, postes.value.length))
const paginatedPostes = computed(() => postes.value.slice(startIndexPostes.value, endIndexPostes.value))

// Pagination Entités
const totalPagesEntites = computed(() => Math.ceil(entitesData.value.length / itemsPerPage))
const startIndexEntites = computed(() => (currentPageEntites.value - 1) * itemsPerPage)
const endIndexEntites = computed(() => Math.min(startIndexEntites.value + itemsPerPage, entitesData.value.length))
const paginatedEntites = computed(() => entitesData.value.slice(startIndexEntites.value, endIndexEntites.value))

function formatDate(dateStr) {
  if (!dateStr) return 'N/A'
  const date = new Date(dateStr)
  return date.toLocaleDateString('fr-FR')
}

// FONCTIONS POSTES
async function loadPostes() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.dignitaire_id) params.append('dignitaire_id', filters.dignitaire_id)
    
    const response = await $fetch(`${config.public.apiBase}/postes?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    postes.value = Array.isArray(response) ? response : (response.data || [])
    currentPagePostes.value = 1
  } catch (error) {
    console.error('Erreur chargement postes:', error)
    postes.value = []
  } finally {
    loading.value = false
  }
}

async function loadDignitaires() {
  try {
    const response = await $fetch(`${config.public.apiBase}/dignitaires?per_page=1000`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    dignitaires.value = response.data || []
  } catch (error) {
    console.error('Erreur:', error)
  }
}

// Versions debouncées pour optimiser les requêtes AJAX
const debouncedLoadPostes = debounce(loadPostes, 500)
const debouncedLoadEntites = debounce(loadEntites, 500)

function openPosteModal(poste = null) {
  selectedPoste.value = poste
  if (poste) {
    formPoste.dignitaire_id = poste.dignitaire_id
    formPoste.intitule = poste.intitule
    formPoste.entite_id = poste.entite_id || ''
    formPoste.ville_id = poste.ville_id || ''
    formPoste.date_debut = poste.date_debut || ''
    formPoste.date_fin = poste.date_fin || ''
  } else {
    formPoste.dignitaire_id = ''
    formPoste.intitule = ''
    formPoste.entite_id = ''
    formPoste.ville_id = ''
    formPoste.date_debut = ''
    formPoste.date_fin = ''
  }
  showPosteModal.value = true
}

function closePosteModal() {
  showPosteModal.value = false
  selectedPoste.value = null
}

function openPosteDetailModal(poste) {
  selectedPosteDetail.value = poste
  showPosteDetailModal.value = true
}

function closePosteDetailModal() {
  showPosteDetailModal.value = false
  selectedPosteDetail.value = null
}

function openClotureModal(poste) {
  posteToClose.value = poste
  clotureForm.motif_fin = ''
  clotureForm.date_fin = new Date().toISOString().slice(0, 10)
  showClotureModal.value = true
}

function closeClotureModal() {
  showClotureModal.value = false
  posteToClose.value = null
}

async function confirmCloture() {
  try {
    await $fetch(`${config.public.apiBase}/postes/${posteToClose.value.id}/cloturer`, {
      method: 'POST',
      body: clotureForm,
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Poste clôturé',
      timer: 2000,
      showConfirmButton: false
    })

    closeClotureModal()
    loadPostes()
  } catch (error) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de la clôture'
    })
  }
}

async function savePoste() {
  try {
    if (selectedPoste.value) {
      await $fetch(`${config.public.apiBase}/postes/${selectedPoste.value.id}`, {
        method: 'PUT',
        body: formPoste,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/postes`, {
        method: 'POST',
        body: formPoste,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedPoste.value ? 'Poste modifié avec succès' : 'Poste ajouté avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closePosteModal()
    await loadPostes()
    await loadEntites() // Recharger les entités aussi
  } catch (error) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de la sauvegarde'
    })
  }
}

async function deletePoste(id) {
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
      await $fetch(`${config.public.apiBase}/postes/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'Le poste a été supprimé avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      loadPostes()
    } catch (error) {
      console.error('Erreur:', error)
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Erreur lors de la suppression'
      })
    }
  }
}

// FONCTIONS ENTITÉS
async function loadEntites() {
  loadingEntites.value = true
  try {
    const params = new URLSearchParams()
    if (filtersEntites.search) params.append('search', filtersEntites.search)

    const response = await $fetch(`${config.public.apiBase}/entites?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    entitesData.value = Array.isArray(response) ? response : (response.data || [])
    entites.value = entitesData.value // Pour le select dans le formulaire poste
    currentPageEntites.value = 1
  } catch (error) {
    console.error('Erreur chargement entités:', error)
    entitesData.value = []
  } finally {
    loadingEntites.value = false
  }
}

function openEntiteModal(entite = null) {
  selectedEntite.value = entite
  if (entite) {
    formEntite.nom = entite.nom
    formEntite.type = entite.type || ''
    formEntite.id_sup = entite.id_sup || null
    formEntite.description = entite.description || ''
  } else {
    formEntite.nom = ''
    formEntite.type = ''
    formEntite.id_sup = null
    formEntite.description = ''
  }
  showEntiteModal.value = true
}

function closeEntiteModal() {
  showEntiteModal.value = false
  selectedEntite.value = null
}

function openEntiteDetailModal(entite) {
  selectedEntiteDetail.value = entite
  showEntiteDetailModal.value = true
}

function closeEntiteDetailModal() {
  showEntiteDetailModal.value = false
  selectedEntiteDetail.value = null
}

async function saveEntite() {
  try {
    const url = selectedEntite.value
      ? `${config.public.apiBase}/entites/${selectedEntite.value.id}`
      : `${config.public.apiBase}/entites`
    
    const method = selectedEntite.value ? 'PUT' : 'POST'
    
    await $fetch(url, {
      method,
      body: formEntite,
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedEntite.value ? 'Entité modifiée avec succès' : 'Entité ajoutée avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeEntiteModal()
    loadEntites()
  } catch (error) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de la sauvegarde'
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

onMounted(async () => {
  villes.value = await referentiels.getVilles()
  await loadEntites()
  await loadDignitaires()
  await loadPostes()
})
</script>
