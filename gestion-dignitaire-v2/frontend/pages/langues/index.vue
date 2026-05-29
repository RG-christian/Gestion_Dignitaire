<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <!-- Header moderne avec gradient gabonais -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Langues</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Gérer les langues et les compétences linguistiques des dignitaires</p>
      </div>
    </header>

    <section class="max-w-full mx-auto px-2 pb-8">
      <!-- Onglets élargis -->
      <div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="flex border-b border-gray-200">
          <button
            @click="activeTab = 'langues'"
            :class="activeTab === 'langues' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="flex-1 px-8 py-4 font-semibold text-base transition-all flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
            </svg>
            Référentiel des Langues
          </button>
          <button
            @click="activeTab = 'langues-parlees'"
            :class="activeTab === 'langues-parlees' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="flex-1 px-8 py-4 font-semibold text-base transition-all flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-1m-2.5-4a4 4 0 11-8 0 4 4 0 018 0zm-7.5 8h-5v-2a3 3 0 013-3h1"/>
            </svg>
            Langues Parlées par les Dignitaires
          </button>
        </div>
      </div>

      <!-- ONGLET 1: RÉFÉRENTIEL DES LANGUES -->
      <div v-if="activeTab === 'langues'">
        <!-- Barre de recherche et filtres -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
          <!-- Recherche principale -->
          <div class="flex flex-col md:flex-row gap-4 mb-4">
            <div class="flex-1">
              <input
                v-model="filters.search"
                @input="debouncedLoadLangues"
                type="text"
                placeholder="Rechercher une langue par nom ou code ISO..."
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
            <button
              @click="openModal()"
              class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Ajouter une langue
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
                @click="filters.letter = ''; loadLangues()"
                :class="filters.letter === '' ? 'bg-gradient-to-br from-green-600 to-green-700 text-white shadow-lg scale-105' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'"
                class="px-4 py-2 rounded-lg font-bold text-sm transition-all duration-200 transform hover:scale-105"
              >
                Tous
              </button>
              <button
                v-for="letter in alphabet"
                :key="letter"
                @click="filters.letter = letter; loadLangues()"
                :class="filters.letter === letter ? 'bg-gradient-to-br from-green-600 to-green-700 text-white shadow-lg scale-105' : 'bg-white text-gray-700 hover:bg-green-50 border border-gray-200'"
                class="w-10 h-10 rounded-lg font-bold text-base transition-all duration-200 transform hover:scale-110 hover:shadow-md flex items-center justify-center"
              >
                {{ letter }}
              </button>
            </div>
          </div>

          <!-- Compteur -->
          <div class="text-sm text-gray-600 flex items-center justify-center bg-gray-50 rounded-lg px-3 py-2">
            {{ filteredLangues.length }} langue(s) trouvée(s)
          </div>
        </div>

        <!-- Loader -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <div class="relative">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-green-600 border-t-transparent absolute top-0 left-0"></div>
          </div>
        </div>

        <!-- Table enrichie -->
        <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div v-if="paginatedLangues.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gradient-to-r from-green-600 to-green-700 text-white">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Langue</th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Code ISO</th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Famille linguistique</th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Nombre de locuteurs</th>
                  <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="langue in paginatedLangues" :key="langue.id" class="hover:bg-green-50 transition-colors duration-150">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                      </svg>
                      <span class="text-sm font-bold text-gray-900">{{ langue.nom }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="langue.code_iso" class="font-mono text-sm font-semibold text-blue-700 bg-blue-50 px-3 py-1 rounded-full">
                      {{ langue.code_iso }}
                    </span>
                    <span v-else class="text-sm text-gray-400">—</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="langue.famille" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                      {{ langue.famille }}
                    </span>
                    <span v-else class="text-sm text-gray-400">—</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="langue.nb_locuteurs" class="flex items-center gap-1 text-sm text-gray-700">
                      <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-1m-2.5-4a4 4 0 11-8 0 4 4 0 018 0zm-7.5 8h-5v-2a3 3 0 013-3h1"/>
                      </svg>
                      {{ langue.nb_locuteurs }}
                    </div>
                    <span v-else class="text-sm text-gray-400">—</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="openDetailModal(langue)" class="bg-sky-500 hover:bg-sky-600 text-white p-2 rounded-lg shadow transition-all" title="Détail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </button>
                      <button @click="openModal(langue)" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg shadow transition-all" title="Modifier">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </button>
                      <button @click="deleteLangue(langue.id)" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow transition-all" title="Supprimer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
            </svg>
            <p class="mt-4 text-gray-500 text-lg">Aucune langue enregistrée</p>
          </div>
          <Pagination
            v-if="filteredLangues.length > 0"
            :current-page="currentPage"
            :total-pages="totalPages"
            :start-index="startIndex"
            :end-index="endIndex"
            :total="filteredLangues.length"
            @update:current-page="currentPage = $event"
          />
        </div>
      </div>

      <!-- ONGLET 2: LANGUES PARLÉES -->
      <div v-if="activeTab === 'langues-parlees'">
        <!-- Barre de recherche et filtres -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
          <div class="flex flex-col md:flex-row gap-4 items-center">
            <div class="flex-1 w-full">
              <input
                v-model="filtersLP.search"
                @input="debouncedLoadLanguesParlees"
                type="text"
                placeholder="Rechercher (langue, dignitaire, niveau)..."
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>
            <div class="w-full md:w-64">
              <select
                v-model="filtersLP.dignitaire_id"
                @change="loadLanguesParlees"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
              >
                <option value="">Tous les dignitaires</option>
                <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                  {{ dig.prenom }} {{ dig.nom }}
                </option>
              </select>
            </div>
            <button
              @click="openModalLP()"
              class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Ajouter
            </button>
          </div>
        </div>

        <!-- Loader -->
        <div v-if="loadingLP" class="flex justify-center items-center py-20">
          <div class="relative">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-green-600 border-t-transparent absolute top-0 left-0"></div>
          </div>
        </div>

        <!-- Table -->
        <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div v-if="paginatedLanguesParlees.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gradient-to-r from-green-600 to-green-700 text-white">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Dignitaire</th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Langue</th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Niveau</th>
                  <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="lp in paginatedLanguesParlees" :key="lp.id" class="hover:bg-green-50 transition-colors duration-150">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                      <span class="text-sm font-bold text-gray-900">{{ lp.dignitaire_nom }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ lp.langue_nom }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="lp.niveau" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      {{ lp.niveau }}
                    </span>
                    <span v-else class="text-sm text-gray-400">—</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="openDetailModalLP(lp)" class="bg-sky-500 hover:bg-sky-600 text-white p-2 rounded-lg shadow transition-all" title="Détail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </button>
                      <button @click="openModalLP(lp)" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg shadow transition-all" title="Modifier">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </button>
                      <button @click="deleteLangueParlee(lp.id)" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow transition-all" title="Supprimer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
            </svg>
            <p class="mt-4 text-gray-500 text-lg">Aucune langue parlée enregistrée</p>
          </div>
          <Pagination
            v-if="languesParlees.length > 0"
            :current-page="currentPageLP"
            :total-pages="totalPagesLP"
            :start-index="startIndexLP"
            :end-index="endIndexLP"
            :total="languesParlees.length"
            @update:current-page="currentPageLP = $event"
          />
        </div>
      </div>
    </section>
    </div>

    <!-- Modal Ajout/Modification LANGUE -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
            </svg>
            {{ selectedLangue ? 'Modifier' : 'Ajouter' }} une langue
          </h4>
          <button @click="closeModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveLangue" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                  </svg>
                  Nom de la langue <span class="text-red-500">*</span>
                </span>
              </label>
              <input v-model="form.nom" type="text" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Ex: Français, Anglais, Fang...">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                  Code ISO
                </span>
              </label>
              <input v-model="form.code_iso" type="text" maxlength="3" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition uppercase" placeholder="Ex: FR, EN, FAN">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  Famille linguistique
                </span>
              </label>
              <input v-model="form.famille" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Ex: Indo-européenne, Bantoue, Niger-Congo...">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-1m-2.5-4a4 4 0 11-8 0 4 4 0 018 0zm-7.5 8h-5v-2a3 3 0 013-3h1"/>
                  </svg>
                  Nombre de locuteurs
                </span>
              </label>
              <input v-model="form.nb_locuteurs" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition" placeholder="Ex: 300 millions, 2 millions...">
            </div>
          </div>
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
              {{ selectedLangue ? '✓ Modifier' : '+ Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Détail LANGUE -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeDetailModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-gabon-blue-600 to-sky-600 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Détail de la Langue
          </h4>
          <button @click="closeDetailModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div v-if="selectedDetail" class="p-6">
          <div class="space-y-4">
            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-4 border-l-4 border-green-600">
              <p class="text-sm font-semibold text-gray-500 mb-1">Nom de la langue</p>
              <p class="text-xl font-bold text-gray-900">{{ selectedDetail.nom }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Code ISO</p>
              <p class="text-lg font-mono font-bold text-blue-700">{{ selectedDetail.code_iso || 'Non renseigné' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Famille linguistique</p>
              <p class="text-gray-900">{{ selectedDetail.famille || 'Non renseignée' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Nombre de locuteurs</p>
              <p class="text-gray-900">{{ selectedDetail.nb_locuteurs || 'Non renseigné' }}</p>
            </div>
          </div>
          <div class="mt-6 pt-4 border-t">
            <button @click="closeDetailModal" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Fermer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ajout/Modification LANGUE PARLÉE -->
    <div v-if="showModalLP" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeModalLP">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
            </svg>
            {{ selectedLangueParlee ? 'Modifier' : 'Ajouter' }} une langue parlée
          </h4>
          <button @click="closeModalLP" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveLangueParlee" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Dignitaire <span class="text-red-500">*</span></label>
              <select v-model="formLP.dignitaire_id" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Choisir un dignitaire --</option>
                <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                  {{ dig.prenom }} {{ dig.nom }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Langue <span class="text-red-500">*</span></label>
              <select v-model="formLP.langue_id" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Choisir une langue --</option>
                <option v-for="langue in langues" :key="langue.id" :value="langue.id">
                  {{ langue.nom }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Niveau</label>
              <select v-model="formLP.niveau" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                <option value="">-- Sélectionner un niveau --</option>
                <option value="Débutant">Débutant</option>
                <option value="Intermédiaire">Intermédiaire</option>
                <option value="Avancé">Avancé</option>
                <option value="Courant">Courant</option>
                <option value="Bilingue">Bilingue</option>
                <option value="Langue maternelle">Langue maternelle</option>
              </select>
            </div>
          </div>
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModalLP" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
              {{ selectedLangueParlee ? '✓ Modifier' : '+ Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Détail LANGUE PARLÉE -->
    <div v-if="showDetailModalLP" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeDetailModalLP">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-gabon-blue-600 to-sky-600 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Détail de la Langue Parlée
          </h4>
          <button @click="closeDetailModalLP" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div v-if="selectedDetailLP" class="p-6">
          <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Dignitaire</p>
              <p class="text-lg font-bold text-gray-900">{{ selectedDetailLP.dignitaire_nom }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Langue</p>
              <p class="text-gray-900">{{ selectedDetailLP.langue_nom }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-1">Niveau</p>
              <p class="text-gray-900">{{ selectedDetailLP.niveau || 'Non renseigné' }}</p>
            </div>
          </div>
          <div class="mt-6 pt-4 border-t">
            <button @click="closeDetailModalLP" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Fermer</button>
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
const referentiels = useReferentiels()
const { debounce } = useDebounce()

// Onglet actif
const activeTab = ref('langues')

// ===== ONGLET 1: LANGUES =====
const langues = ref([])
const loading = ref(true)
const showModal = ref(false)
const showDetailModal = ref(false)
const selectedLangue = ref(null)
const selectedDetail = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

// Alphabet pour la recherche par lettre
const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('')

const filters = reactive({
  search: '',
  letter: ''
})

const form = reactive({
  nom: '',
  code_iso: '',
  famille: '',
  nb_locuteurs: ''
})

// Langues filtrées
const filteredLangues = computed(() => {
  let result = langues.value

  // Filtre par lettre
  if (filters.letter) {
    result = result.filter(l => l.nom.toUpperCase().startsWith(filters.letter))
  }

  // Tri alphabétique
  result.sort((a, b) => a.nom.localeCompare(b.nom))

  return result
})

// Pagination
const totalPages = computed(() => Math.ceil(filteredLangues.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, filteredLangues.value.length))
const paginatedLangues = computed(() => {
  return filteredLangues.value.slice(startIndex.value, endIndex.value)
})

async function loadLangues() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)

    const response = await $fetch(`${config.public.apiBase}/langues?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    langues.value = Array.isArray(response) ? response : (response.data || [])
    currentPage.value = 1
  } catch (error) {
    console.error('Erreur chargement langues:', error)
    langues.value = []
  } finally {
    loading.value = false
  }
}

const debouncedLoadLangues = debounce(loadLangues, 500)

function openModal(langue = null) {
  selectedLangue.value = langue
  if (langue) {
    form.nom = langue.nom
    form.code_iso = langue.code_iso || ''
    form.famille = langue.famille || ''
    form.nb_locuteurs = langue.nb_locuteurs || ''
  } else {
    form.nom = ''
    form.code_iso = ''
    form.famille = ''
    form.nb_locuteurs = ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedLangue.value = null
}

function openDetailModal(langue) {
  selectedDetail.value = langue
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

async function saveLangue() {
  try {
    if (selectedLangue.value) {
      await $fetch(`${config.public.apiBase}/langues/${selectedLangue.value.id}`, {
        method: 'PUT',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/langues`, {
        method: 'POST',
        body: form,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedLangue.value ? 'Langue modifiée avec succès' : 'Langue ajoutée avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeModal()
    loadLangues()
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

async function deleteLangue(id) {
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
      await $fetch(`${config.public.apiBase}/langues/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'La langue a été supprimée avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      loadLangues()
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

// ===== ONGLET 2: LANGUES PARLÉES =====
const languesParlees = ref([])
const dignitaires = ref([])
const loadingLP = ref(true)
const showModalLP = ref(false)
const showDetailModalLP = ref(false)
const selectedLangueParlee = ref(null)
const selectedDetailLP = ref(null)
const currentPageLP = ref(1)

const filtersLP = reactive({
  search: '',
  dignitaire_id: ''
})

const formLP = reactive({
  dignitaire_id: '',
  langue_id: '',
  niveau: ''
})

// Pagination LP
const totalPagesLP = computed(() => Math.ceil(languesParlees.value.length / itemsPerPage))
const startIndexLP = computed(() => (currentPageLP.value - 1) * itemsPerPage)
const endIndexLP = computed(() => Math.min(startIndexLP.value + itemsPerPage, languesParlees.value.length))
const paginatedLanguesParlees = computed(() => {
  return languesParlees.value.slice(startIndexLP.value, endIndexLP.value)
})

async function loadLanguesParlees() {
  loadingLP.value = true
  try {
    const params = new URLSearchParams()
    if (filtersLP.search) params.append('search', filtersLP.search)
    if (filtersLP.dignitaire_id) params.append('dignitaire_id', filtersLP.dignitaire_id)
    
    const response = await $fetch(`${config.public.apiBase}/langues-parlees?${params.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    languesParlees.value = Array.isArray(response) ? response : (response.data || [])
    currentPageLP.value = 1
  } catch (error) {
    console.error('Erreur chargement langues parlées:', error)
    languesParlees.value = []
  } finally {
    loadingLP.value = false
  }
}

const debouncedLoadLanguesParlees = debounce(loadLanguesParlees, 500)

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

function openModalLP(langueParlee = null) {
  selectedLangueParlee.value = langueParlee
  if (langueParlee) {
    formLP.dignitaire_id = langueParlee.dignitaire_id
    formLP.langue_id = langueParlee.langue_id
    formLP.niveau = langueParlee.niveau || ''
  } else {
    formLP.dignitaire_id = ''
    formLP.langue_id = ''
    formLP.niveau = ''
  }
  showModalLP.value = true
}

function closeModalLP() {
  showModalLP.value = false
  selectedLangueParlee.value = null
}

function openDetailModalLP(langueParlee) {
  selectedDetailLP.value = langueParlee
  showDetailModalLP.value = true
}

function closeDetailModalLP() {
  showDetailModalLP.value = false
  selectedDetailLP.value = null
}

async function saveLangueParlee() {
  try {
    if (selectedLangueParlee.value) {
      await $fetch(`${config.public.apiBase}/langues-parlees/${selectedLangueParlee.value.id}`, {
        method: 'PUT',
        body: formLP,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    } else {
      await $fetch(`${config.public.apiBase}/langues-parlees`, {
        method: 'POST',
        body: formLP,
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
    }
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedLangueParlee.value ? 'Langue parlée modifiée avec succès' : 'Langue parlée ajoutée avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    
    closeModalLP()
    loadLanguesParlees()
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

async function deleteLangueParlee(id) {
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
      await $fetch(`${config.public.apiBase}/langues-parlees/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'La langue parlée a été supprimée avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      
      loadLanguesParlees()
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

onMounted(async () => {
  await loadLangues()
  await loadDignitaires()
  await loadLanguesParlees()
})
</script>
