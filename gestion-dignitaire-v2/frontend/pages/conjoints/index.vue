<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <!-- Header moderne avec gradient gabonais -->
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Gestion des Conjoints</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Gérer les conjoints des dignitaires</p>
      </div>
    </header>

    <main class="max-w-full mx-auto px-2 pb-8">
      <!-- Barre de recherche et filtres -->
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <div class="flex-1 w-full">
            <SearchInput
              v-model="filters.search"
              placeholder="Rechercher un conjoint..."
              @update:modelValue="debouncedLoadConjoints"
            />
          </div>

          <div class="w-full md:w-64">
            <select
              v-model="filters.dignitaire_id"
              @change="loadConjoints"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
            >
              <option value="">Tous les dignitaires</option>
              <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                {{ dig.prenom }} {{ dig.nom }}
              </option>
            </select>
          </div>

          <div class="w-full md:w-56">
            <select
              v-model="filters.statut"
              @change="loadConjoints"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
            >
              <option value="">Tous les statuts</option>
              <option value="actif">Actif</option>
              <option value="divorce">Divorcé</option>
              <option value="veuf">Veuf/Veuve</option>
              <option value="separe">Séparé</option>
            </select>
          </div>

          <button
            v-if="permissions.peutEcrire('Dignitaire')"
            @click="openModal()"
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

      <!-- Erreur -->
      <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg shadow mb-4">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <span class="font-semibold">{{ error }}</span>
        </div>
      </div>

      <!-- Table -->
      <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div v-if="paginatedConjoints.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nom complet</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Genre</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Statut</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Profession</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dignitaire</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="conjoint in paginatedConjoints" :key="conjoint.id" class="hover:bg-gabon-green-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ conjoint.prenom }} {{ conjoint.nom }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="conjoint.genre === 'M'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gabon-blue-100 text-gabon-blue-800">
                    Masculin
                  </span>
                  <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                    Féminin
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" :class="statutClass(conjoint.statut)">
                    {{ statutLabel(conjoint.statut) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  {{ conjoint.profession || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  {{ conjoint.dignitaire_nom_complet }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2 flex-wrap">
                    <button
                      @click="openDetailModal(conjoint)"
                      class="inline-flex items-center gap-1 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold px-3 py-2 rounded-lg transition-colors"
                      title="Détails"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      Détails
                    </button>
                    <button
                      v-if="permissions.peutEcrire('Dignitaire')"
                      @click="openModal(conjoint)"
                      class="inline-flex items-center gap-1 bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700 font-semibold px-3 py-2 rounded-lg transition-colors"
                      title="Modifier"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Modifier
                    </button>
                    <button
                      v-if="conjoint.statut === 'actif' && permissions.peutEcrire('Dignitaire')"
                      @click="openTerminerUnionModal(conjoint)"
                      class="inline-flex items-center gap-1 bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold px-3 py-2 rounded-lg transition-colors"
                      title="Terminer l'union"
                    >
                      Terminer l'union
                    </button>
                    <button
                      v-if="permissions.peutSupprimer()"
                      @click="deleteConjoint(conjoint.id)"
                      class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 font-semibold px-3 py-2 rounded-lg transition-colors"
                      title="Supprimer"
                    >
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

        <!-- Message vide -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <p class="mt-4 text-gray-500 text-lg">Aucun conjoint enregistré</p>
        </div>

        <!-- Pagination -->
        <Pagination
          v-if="conjoints.length > 0"
          :current-page="currentPage"
          :total-pages="totalPages"
          :start-index="startIndex"
          :end-index="endIndex"
          :total="conjoints.length"
          @update:current-page="currentPage = $event"
        />
      </div>
    </main>
    </div>

    <!-- Modal Ajout/Modification -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-scale-in">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white">
            {{ selectedConjoint ? 'Modifier' : 'Ajouter' }} un conjoint
          </h4>
          <button @click="closeModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveConjoint" class="p-6">
          <div class="space-y-4">
            <!-- Dignitaire -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Dignitaire <span class="text-red-500">*</span>
              </label>
              <select v-model="form.dignitaire_id" required :disabled="!!selectedConjoint" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition disabled:bg-gray-100">
                <option value="">-- Choisir un dignitaire --</option>
                <option v-for="dig in dignitaires" :key="dig.id" :value="dig.id">
                  {{ dig.prenom }} {{ dig.nom }}
                </option>
              </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom <span class="text-red-500">*</span></label>
                <input v-model="form.nom" type="text" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom <span class="text-red-500">*</span></label>
                <input v-model="form.prenom" type="text" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Genre <span class="text-red-500">*</span></label>
                <select v-model="form.genre" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                  <option value="">-- Sélectionner --</option>
                  <option value="M">Masculin</option>
                  <option value="F">Féminin</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de naissance</label>
                <input v-model="form.date_naissance" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lieu de naissance</label>
                <select v-model="form.lieu_naissance_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                  <option value="">-- Sélectionner une ville --</option>
                  <option v-for="ville in villes" :key="ville.id" :value="ville.id">{{ ville.nom }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nationalité</label>
                <select v-model="form.nationalite_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                  <option value="">-- Sélectionner un pays --</option>
                  <option v-for="pays in paysList" :key="pays.id" :value="pays.id">{{ pays.nom }}</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Profession</label>
                <input v-model="form.profession" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Employeur</label>
                <input v-model="form.employeur" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de mariage</label>
                <input v-model="form.date_mariage" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lieu de mariage</label>
                <input v-model="form.lieu_mariage" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                <input v-model="form.telephone" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input v-model="form.email" type="email" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Adresse</label>
              <input v-model="form.adresse" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-center gap-3">
                <input v-model="form.est_militaire" type="checkbox" id="est_militaire" class="w-5 h-5 rounded text-gabon-green-600 focus:ring-gabon-green-500">
                <label for="est_militaire" class="text-sm font-semibold text-gray-700">Est militaire</label>
              </div>
              <input
                v-if="form.est_militaire"
                v-model="form.grade_militaire"
                type="text"
                placeholder="Grade militaire"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-center gap-3">
                <input v-model="form.est_dignitaire" type="checkbox" id="est_dignitaire" class="w-5 h-5 rounded text-gabon-green-600 focus:ring-gabon-green-500">
                <label for="est_dignitaire" class="text-sm font-semibold text-gray-700">Est également dignitaire</label>
              </div>
              <input
                v-if="form.est_dignitaire"
                v-model="form.fonction_dignitaire"
                type="text"
                placeholder="Fonction"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
            </div>
          </div>

          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
              {{ selectedConjoint ? 'Modifier' : 'Ajouter' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Terminer l'union -->
    <div
      v-if="showTerminerUnionModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeTerminerUnionModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-scale-in">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white">Terminer l'union</h4>
          <button @click="closeTerminerUnionModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="confirmTerminerUnion" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau statut <span class="text-red-500">*</span></label>
              <select v-model="terminerUnionForm.nouveau_statut" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                <option value="">-- Sélectionner --</option>
                <option value="divorce">Divorce</option>
                <option value="veuf">Veuf/Veuve</option>
                <option value="separe">Séparé(e)</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
              <input v-model="terminerUnionForm.date_fin" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
            </div>
          </div>
          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeTerminerUnionModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
            <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition">Confirmer</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Détail -->
    <div
      v-if="showDetailModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeDetailModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in">
        <div class="bg-gradient-to-r from-gabon-blue-600 to-sky-600 px-6 py-4 flex items-center justify-between">
          <h4 class="text-xl font-bold text-white">Détails du conjoint</h4>
          <button @click="closeDetailModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div v-if="selectedDetail" class="p-6">
          <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
              <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Nom complet</p>
                <p class="text-lg font-bold text-gray-900">{{ selectedDetail.prenom }} {{ selectedDetail.nom }}</p>
              </div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="statutClass(selectedDetail.statut)">
                {{ statutLabel(selectedDetail.statut) }}
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Dignitaire</p>
                <p class="text-gray-900">{{ selectedDetail.dignitaire_nom_complet }}</p>
              </div>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Profession</p>
                <p class="text-gray-900">{{ selectedDetail.profession || 'N/A' }}</p>
              </div>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Date de mariage</p>
                <p class="text-gray-900">{{ formatDate(selectedDetail.date_mariage) }}</p>
              </div>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-500 mb-1">Téléphone / Email</p>
                <p class="text-gray-900">{{ selectedDetail.telephone || selectedDetail.email || 'N/A' }}</p>
              </div>
            </div>

            <div v-if="selectedDetail.est_militaire || selectedDetail.est_dignitaire" class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm font-semibold text-gray-500 mb-2">Statut particulier</p>
              <span v-if="selectedDetail.est_militaire" class="inline-block bg-gabon-blue-100 text-gabon-blue-700 text-xs px-2 py-1 rounded-full mr-2">
                Militaire{{ selectedDetail.grade_militaire ? ` (${selectedDetail.grade_militaire})` : '' }}
              </span>
              <span v-if="selectedDetail.est_dignitaire" class="inline-block bg-gabon-yellow-100 text-gabon-yellow-700 text-xs px-2 py-1 rounded-full">
                Dignitaire{{ selectedDetail.fonction_dignitaire ? ` (${selectedDetail.fonction_dignitaire})` : '' }}
              </span>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t">
            <button
              @click="closeDetailModal"
              class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition"
            >
              Fermer
            </button>
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

const config = useRuntimeConfig()
const authStore = useAuthStore()
const referentiels = useReferentiels()
const api = useApi()
const permissions = usePermissions()
const { debounce } = useDebounce()

const conjoints = ref<any[]>([])
const dignitaires = ref<any[]>([])
const villes = ref<any[]>([])
const paysList = ref<any[]>([])
const loading = ref(true)
const error = ref('')
const showModal = ref(false)
const showDetailModal = ref(false)
const showTerminerUnionModal = ref(false)
const selectedConjoint = ref<any>(null)
const selectedDetail = ref<any>(null)
const conjointATerminer = ref<any>(null)
const currentPage = ref(1)
const itemsPerPage = 10

const filters = reactive({
  search: '',
  dignitaire_id: '',
  statut: ''
})

const form = reactive({
  dignitaire_id: '',
  nom: '',
  prenom: '',
  genre: '',
  date_naissance: '',
  lieu_naissance_id: '',
  nationalite_id: '',
  profession: '',
  employeur: '',
  date_mariage: '',
  lieu_mariage: '',
  telephone: '',
  email: '',
  adresse: '',
  est_militaire: false,
  grade_militaire: '',
  est_dignitaire: false,
  fonction_dignitaire: ''
})

const terminerUnionForm = reactive({
  nouveau_statut: '',
  date_fin: new Date().toISOString().slice(0, 10)
})

// Pagination
const totalPages = computed(() => Math.ceil(conjoints.value.length / itemsPerPage))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, conjoints.value.length))
const paginatedConjoints = computed(() => conjoints.value.slice(startIndex.value, endIndex.value))

function statutLabel(statut: string) {
  return { actif: 'Actif', divorce: 'Divorcé', veuf: 'Veuf/Veuve', separe: 'Séparé' }[statut] || statut
}

function statutClass(statut: string) {
  const classes: Record<string, string> = {
    actif: 'bg-green-100 text-green-800',
    divorce: 'bg-orange-100 text-orange-800',
    veuf: 'bg-gray-200 text-gray-800',
    separe: 'bg-yellow-100 text-yellow-800'
  }
  return classes[statut] || 'bg-gray-100 text-gray-800'
}

async function loadConjoints() {
  loading.value = true
  error.value = ''
  try {
    const response: any = await api.getAllConjoints({
      search: filters.search || undefined,
      dignitaire_id: filters.dignitaire_id || undefined,
      statut: filters.statut || undefined
    })
    conjoints.value = response.conjoints || []
    currentPage.value = 1
  } catch (err: any) {
    console.error('Erreur chargement conjoints:', err)
    error.value = 'Erreur lors du chargement des conjoints'
    conjoints.value = []
  } finally {
    loading.value = false
  }
}

async function loadDignitaires() {
  try {
    const response = await $fetch(`${config.public.apiBase}/dignitaires?per_page=1000`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    dignitaires.value = (response as any).data || []
  } catch (error) {
    console.error('Erreur chargement dignitaires:', error)
  }
}

const debouncedLoadConjoints = debounce(loadConjoints, 500)

function resetForm() {
  form.dignitaire_id = ''
  form.nom = ''
  form.prenom = ''
  form.genre = ''
  form.date_naissance = ''
  form.lieu_naissance_id = ''
  form.nationalite_id = ''
  form.profession = ''
  form.employeur = ''
  form.date_mariage = ''
  form.lieu_mariage = ''
  form.telephone = ''
  form.email = ''
  form.adresse = ''
  form.est_militaire = false
  form.grade_militaire = ''
  form.est_dignitaire = false
  form.fonction_dignitaire = ''
}

function openModal(conjoint: any = null) {
  selectedConjoint.value = conjoint
  if (conjoint) {
    form.dignitaire_id = conjoint.dignitaire_id
    form.nom = conjoint.nom
    form.prenom = conjoint.prenom
    form.genre = conjoint.genre
    form.date_naissance = conjoint.date_naissance || ''
    form.lieu_naissance_id = conjoint.lieu_naissance_id || ''
    form.nationalite_id = conjoint.nationalite_id || ''
    form.profession = conjoint.profession || ''
    form.employeur = conjoint.employeur || ''
    form.date_mariage = conjoint.date_mariage || ''
    form.lieu_mariage = conjoint.lieu_mariage || ''
    form.telephone = conjoint.telephone || ''
    form.email = conjoint.email || ''
    form.adresse = conjoint.adresse || ''
    form.est_militaire = !!conjoint.est_militaire
    form.grade_militaire = conjoint.grade_militaire || ''
    form.est_dignitaire = !!conjoint.est_dignitaire
    form.fonction_dignitaire = conjoint.fonction_dignitaire || ''
  } else {
    resetForm()
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedConjoint.value = null
}

function openDetailModal(conjoint: any) {
  selectedDetail.value = conjoint
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedDetail.value = null
}

function openTerminerUnionModal(conjoint: any) {
  conjointATerminer.value = conjoint
  terminerUnionForm.nouveau_statut = ''
  terminerUnionForm.date_fin = new Date().toISOString().slice(0, 10)
  showTerminerUnionModal.value = true
}

function closeTerminerUnionModal() {
  showTerminerUnionModal.value = false
  conjointATerminer.value = null
}

async function saveConjoint() {
  const { $swal } = useNuxtApp()
  try {
    if (selectedConjoint.value) {
      await api.updateConjoint(selectedConjoint.value.id, form)
    } else {
      await api.createConjoint(Number(form.dignitaire_id), form)
    }

    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedConjoint.value ? 'Conjoint modifié avec succès' : 'Conjoint ajouté avec succès',
      timer: 2000,
      showConfirmButton: false
    })

    closeModal()
    loadConjoints()
  } catch (error: any) {
    console.error('Erreur sauvegarde conjoint:', error)
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de la sauvegarde'
    })
  }
}

async function confirmTerminerUnion() {
  const { $swal } = useNuxtApp()
  try {
    await api.terminerUnionConjoint(conjointATerminer.value.id, terminerUnionForm)
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: 'Union terminée avec succès',
      timer: 2000,
      showConfirmButton: false
    })
    closeTerminerUnionModal()
    loadConjoints()
  } catch (error: any) {
    console.error('Erreur terminer union:', error)
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de l\'opération'
    })
  }
}

async function deleteConjoint(id: number) {
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
      await api.deleteConjoint(id)
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'Le conjoint a été supprimé avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      loadConjoints()
    } catch (error) {
      console.error('Erreur suppression:', error)
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Erreur lors de la suppression'
      })
    }
  }
}

function formatDate(date: string | null) {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR')
}

onMounted(async () => {
  villes.value = await referentiels.getVilles()
  paysList.value = await referentiels.getPays()
  await loadDignitaires()
  await loadConjoints()
})
</script>

<style scoped>
.animate-scale-in {
  animation: scaleIn 0.3s ease forwards;
}
.animate-fade-in {
  animation: fadeIn 0.3s ease forwards;
}
@keyframes scaleIn {
  from { transform: scale(0.8); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>
