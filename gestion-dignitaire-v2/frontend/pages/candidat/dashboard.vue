<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gabon-green-50">
    <!-- Navbar candidat -->
    <nav class="fixed top-4 left-4 right-4 z-50">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-gray-200/50 px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-xl p-2.5 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
              <div>
                <h1 class="text-lg font-bold text-gray-900 leading-none">Mon Espace</h1>
                <p class="text-xs text-gray-600">Candidat</p>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <span class="hidden sm:block text-sm text-gray-700 font-medium">{{ candidat?.nom_complet }}</span>
              <button @click="logout" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="hidden sm:inline">Déconnexion</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <div class="pt-32 pb-20 px-4">
      <div class="max-w-7xl mx-auto">
        <!-- Loader -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <div class="relative">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-green-600 border-t-transparent absolute top-0 left-0"></div>
          </div>
        </div>

        <!-- Contenu chargé -->
        <div v-else>
          <!-- Header avec statut -->
          <div class="mb-8">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-200 p-8">
              <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-6">
                  <!-- Avatar -->
                  <div class="relative">
                    <div class="w-24 h-24 bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-2xl flex items-center justify-center shadow-xl">
                      <span class="text-3xl font-bold text-white">{{ getInitials(candidat?.nom_complet) }}</span>
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg">
                      <svg v-if="candidat?.statut === 'valide'" class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <svg v-else-if="candidat?.statut === 'refuse'" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <svg v-else class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </div>
                  </div>

                  <!-- Infos -->
                  <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ candidat?.nom_complet }}</h2>
                    <p class="text-gray-600 flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                      {{ candidat?.email }}
                    </p>
                    <p v-if="candidat?.telephone" class="text-gray-600 flex items-center gap-2 mt-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                      </svg>
                      {{ candidat?.telephone }}
                    </p>
                  </div>
                </div>

                <!-- Badge statut -->
                <div class="flex items-center gap-3">
                  <div :class="{
                    'bg-yellow-100 border-yellow-500 text-yellow-700': candidat?.statut === 'en_attente',
                    'bg-green-100 border-green-500 text-green-700': candidat?.statut === 'valide',
                    'bg-red-100 border-red-500 text-red-700': candidat?.statut === 'refuse'
                  }" class="px-6 py-3 rounded-2xl border-2 font-bold text-center">
                    <div class="text-2xl mb-1">
                      {{ candidat?.statut === 'en_attente' ? '⏳' : candidat?.statut === 'valide' ? '✅' : '❌' }}
                    </div>
                    <div class="text-sm">
                      {{ candidat?.statut === 'en_attente' ? 'En attente' : candidat?.statut === 'valide' ? 'Validé' : 'Refusé' }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Progression du dossier -->
          <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
              <h3 class="text-lg font-bold text-gray-900 mb-4">Progression de mon dossier</h3>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div v-for="item in progressItems" :key="item.label" class="flex items-center gap-3 p-3 rounded-xl" :class="item.done ? 'bg-green-50' : 'bg-gray-50'">
                  <span class="w-8 h-8 rounded-full flex items-center justify-center text-white flex-shrink-0" :class="item.done ? 'bg-green-500' : 'bg-gray-300'">
                    <svg v-if="item.done" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </span>
                  <span class="text-sm font-semibold text-gray-800">{{ item.label }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Message selon le statut -->
          <div v-if="candidat?.statut === 'en_attente'" class="mb-8">
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-500 rounded-xl p-6">
              <div class="flex items-start gap-4">
                <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                  <h3 class="font-bold text-yellow-900 mb-2">Candidature en cours d'examen</h3>
                  <p class="text-yellow-800">Votre dossier est actuellement examiné par nos administrateurs. Vous recevrez un email dès qu'une décision sera prise.</p>
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="candidat?.statut === 'valide'" class="mb-8">
            <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-xl p-6">
              <div class="flex items-start gap-4">
                <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                  <h3 class="font-bold text-green-900 mb-2">🎉 Félicitations ! Votre candidature a été acceptée</h3>
                  <p class="text-green-800">Vous êtes maintenant officiellement enregistré en tant que dignitaire. Un email de confirmation a été envoyé à votre adresse.</p>
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="candidat?.statut === 'refuse'" class="mb-8">
            <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-xl p-6">
              <div class="flex items-start gap-4">
                <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                  <h3 class="font-bold text-red-900 mb-2">Candidature refusée</h3>
                  <p class="text-red-800 mb-3">{{ candidat?.motif_refus || 'Votre candidature n\'a pas été retenue.' }}</p>
                  <p class="text-red-700 text-sm">Vous pouvez soumettre une nouvelle candidature après avoir complété votre dossier.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Grid principal -->
          <div class="grid lg:grid-cols-3 gap-8">
            <!-- Informations personnelles -->
            <div class="lg:col-span-2 space-y-8">
              <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                  <svg class="w-6 h-6 text-gabon-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Informations Personnelles
                </h3>

                <div class="grid md:grid-cols-2 gap-6">
                  <div>
                    <label class="text-sm font-semibold text-gray-600 block mb-2">Nom complet</label>
                    <p class="text-gray-900 font-medium">{{ candidat?.nom_complet }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-semibold text-gray-600 block mb-2">Date de naissance</label>
                    <p class="text-gray-900 font-medium">{{ formatDate(candidat?.date_naissance) }} ({{ candidat?.age }} ans)</p>
                  </div>
                  <div>
                    <label class="text-sm font-semibold text-gray-600 block mb-2">Genre</label>
                    <p class="text-gray-900 font-medium">{{ candidat?.genre === 'M' ? 'Masculin' : 'Féminin' }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-semibold text-gray-600 block mb-2">État civil</label>
                    <p class="text-gray-900 font-medium">{{ candidat?.etat_civil || 'Non renseigné' }}</p>
                  </div>
                  <div class="md:col-span-2">
                    <label class="text-sm font-semibold text-gray-600 block mb-2">Adresse</label>
                    <p class="text-gray-900 font-medium">{{ candidat?.adresse || 'Non renseignée' }}</p>
                  </div>
                </div>
              </div>

              <!-- Documents -->
              <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
                <div class="flex items-center justify-between mb-6">
                  <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-gabon-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Mes Documents
                  </h3>
                  <span class="px-3 py-1 bg-gabon-blue-100 text-gabon-blue-700 rounded-full text-sm font-bold">
                    {{ documents?.length || 0 }}
                  </span>
                </div>

                <div v-if="documents && documents.length > 0" class="space-y-3">
                  <div v-for="doc in documents" :key="doc.id" class="flex items-center justify-between bg-gray-50 rounded-xl p-4 border border-gray-200 hover:border-gabon-blue-500 transition-colors group">
                    <div class="flex items-center gap-3 flex-1">
                      <div class="w-10 h-10 bg-gabon-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-xl">{{ getDocumentIcon(doc.type_document) }}</span>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ doc.nom_fichier }}</p>
                        <p class="text-sm text-gray-500">{{ doc.taille_lisible }} • {{ getDocumentTypeLabel(doc.type_document) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-else class="text-center py-12">
                  <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                  </svg>
                  <p class="text-gray-500">Aucun document téléchargé</p>
                </div>
              </div>

              <!-- Langues -->
              <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Mes langues</h3>

                <div v-if="langues.length > 0" class="space-y-2 mb-6">
                  <div v-for="l in langues" :key="l.id" class="flex items-center justify-between bg-gray-50 rounded-xl p-3 border border-gray-200">
                    <span class="font-medium text-gray-900">{{ l.langue?.nom }} <span class="text-gray-500 text-sm">({{ l.niveau || 'niveau non précisé' }})</span></span>
                    <button v-if="estModifiable" @click="deleteLangue(l.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>
                <p v-else class="text-gray-500 mb-6">Aucune langue ajoutée</p>

                <form v-if="estModifiable" @submit.prevent="addLangue" class="flex flex-col sm:flex-row gap-3">
                  <select v-model="newLangue.langue_id" required class="flex-1 px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    <option value="">Sélectionnez une langue...</option>
                    <option v-for="l in referenceLangues" :key="l.id" :value="l.id">{{ l.nom }}</option>
                  </select>
                  <select v-model="newLangue.niveau" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    <option value="">Niveau...</option>
                    <option value="Débutant">Débutant</option>
                    <option value="Intermédiaire">Intermédiaire</option>
                    <option value="Courant">Courant</option>
                    <option value="Natif">Natif</option>
                  </select>
                  <button type="submit" class="px-4 py-2 bg-gabon-green-600 hover:bg-gabon-green-700 text-white font-semibold rounded-lg transition-colors">Ajouter</button>
                </form>
              </div>

              <!-- Diplômes -->
              <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Mes diplômes</h3>

                <div v-if="diplomes.length > 0" class="space-y-2 mb-6">
                  <div v-for="d in diplomes" :key="d.id" class="flex items-center justify-between bg-gray-50 rounded-xl p-3 border border-gray-200">
                    <div>
                      <p class="font-medium text-gray-900">{{ d.intitule }}</p>
                      <p class="text-sm text-gray-500">{{ d.etablissement?.nom || 'Établissement non précisé' }} {{ d.annee ? `• ${d.annee}` : '' }}</p>
                    </div>
                    <button v-if="estModifiable" @click="deleteDiplome(d.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>
                <p v-else class="text-gray-500 mb-6">Aucun diplôme ajouté</p>

                <form v-if="estModifiable" @submit.prevent="addDiplome" class="space-y-3">
                  <div class="grid md:grid-cols-2 gap-3">
                    <input v-model="newDiplome.intitule" type="text" required placeholder="Intitulé du diplôme" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    <input v-model="newDiplome.annee" type="text" placeholder="Année" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    <select v-model="newDiplome.etablissement_id" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                      <option value="">Établissement...</option>
                      <option v-for="e in referenceEtablissements" :key="e.id" :value="e.id">{{ e.nom }}</option>
                    </select>
                    <select v-model="newDiplome.domaine_id" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                      <option value="">Domaine...</option>
                      <option v-for="dm in referenceDomaines" :key="dm.id" :value="dm.id">{{ dm.nom }}</option>
                    </select>
                    <div class="md:col-span-2">
                      <label class="text-xs font-semibold text-gray-600 block mb-1">Justificatif (facultatif)</label>
                      <input type="file" accept=".pdf,.jpg,.jpeg,.png" @change="e => newDiplome.justificatif = e.target.files[0]" class="text-sm">
                    </div>
                  </div>
                  <button type="submit" class="px-4 py-2 bg-gabon-green-600 hover:bg-gabon-green-700 text-white font-semibold rounded-lg transition-colors">Ajouter le diplôme</button>
                </form>
              </div>

              <!-- Expériences professionnelles -->
              <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Mes expériences professionnelles</h3>

                <div v-if="experiences.length > 0" class="space-y-2 mb-6">
                  <div v-for="ex in experiences" :key="ex.id" class="flex items-center justify-between bg-gray-50 rounded-xl p-3 border border-gray-200">
                    <div>
                      <p class="font-medium text-gray-900">{{ ex.intitule }}</p>
                      <p class="text-sm text-gray-500">{{ ex.structure?.nom || 'Structure non précisée' }} • {{ formatDate(ex.date_debut) }} — {{ ex.date_fin ? formatDate(ex.date_fin) : 'en cours' }}</p>
                    </div>
                    <button v-if="estModifiable" @click="deleteExperience(ex.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>
                <p v-else class="text-gray-500 mb-6">Aucune expérience ajoutée</p>

                <form v-if="estModifiable" @submit.prevent="addExperience" class="space-y-3">
                  <div class="grid md:grid-cols-2 gap-3">
                    <input v-model="newExperience.intitule" type="text" required placeholder="Intitulé du poste" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm md:col-span-2">
                    <select v-model="newExperience.structure_id" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm md:col-span-2">
                      <option value="">Structure / institution...</option>
                      <option v-for="s in referenceStructures" :key="s.id" :value="s.id">{{ s.nom }}</option>
                    </select>
                    <div>
                      <label class="text-xs font-semibold text-gray-600 block mb-1">Date de début</label>
                      <input v-model="newExperience.date_debut" type="date" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                      <label class="text-xs font-semibold text-gray-600 block mb-1">Date de fin</label>
                      <input v-model="newExperience.date_fin" type="date" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    </div>
                    <div class="md:col-span-2">
                      <label class="text-xs font-semibold text-gray-600 block mb-1">Justificatif (facultatif)</label>
                      <input type="file" accept=".pdf,.jpg,.jpeg,.png" @change="e => newExperience.justificatif = e.target.files[0]" class="text-sm">
                    </div>
                  </div>
                  <button type="submit" class="px-4 py-2 bg-gabon-green-600 hover:bg-gabon-green-700 text-white font-semibold rounded-lg transition-colors">Ajouter l'expérience</button>
                </form>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
              <!-- Carte timeline -->
              <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Chronologie</h3>
                <div class="space-y-4">
                  <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                      <div class="w-8 h-8 bg-gabon-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                      </div>
                      <div class="w-0.5 h-12 bg-gray-300"></div>
                    </div>
                    <div class="flex-1 pb-4">
                      <p class="font-semibold text-gray-900">Candidature soumise</p>
                      <p class="text-sm text-gray-500">{{ formatDate(candidat?.date_candidature) }}</p>
                    </div>
                  </div>

                  <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                      <div :class="candidat?.statut !== 'en_attente' ? 'bg-gabon-green-500' : 'bg-gray-300'" class="w-8 h-8 rounded-full flex items-center justify-center">
                        <svg v-if="candidat?.statut !== 'en_attente'" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span v-else class="text-white text-xs">2</span>
                      </div>
                      <div v-if="candidat?.statut !== 'en_attente'" class="w-0.5 h-12 bg-gray-300"></div>
                    </div>
                    <div class="flex-1 pb-4">
                      <p class="font-semibold text-gray-900">Examen du dossier</p>
                      <p v-if="candidat?.date_validation" class="text-sm text-gray-500">{{ formatDate(candidat?.date_validation) }}</p>
                      <p v-else class="text-sm text-gray-500">En cours...</p>
                    </div>
                  </div>

                  <div v-if="candidat?.statut === 'valide'" class="flex gap-3">
                    <div class="w-8 h-8 bg-gabon-green-500 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <p class="font-semibold text-gray-900">Acceptation</p>
                      <p class="text-sm text-gray-500">Dignitaire confirmé</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Actions rapides -->
              <div class="bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-2xl shadow-xl p-6 text-white">
                <h3 class="text-lg font-bold mb-4">Besoin d'aide ?</h3>
                <p class="text-sm text-gabon-green-50 mb-4">Contactez notre support si vous avez des questions</p>
                <a href="mailto:contact@dignitaires.ga" class="block px-4 py-3 bg-white/20 hover:bg-white/30 rounded-xl font-semibold text-center transition-colors backdrop-blur-sm">
                  Contacter le support
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const { $api, $swal } = useNuxtApp()

const loading = ref(true)
const candidat = ref(null)
const documents = ref([])
const langues = ref([])
const diplomes = ref([])
const experiences = ref([])

const referenceLangues = ref([])
const referenceEtablissements = ref([])
const referenceDomaines = ref([])
const referenceStructures = ref([])

const newLangue = ref({ langue_id: '', niveau: '' })
const newDiplome = ref({ intitule: '', etablissement_id: '', domaine_id: '', annee: '', justificatif: null })
const newExperience = ref({ intitule: '', structure_id: '', date_debut: '', date_fin: '', justificatif: null })

const estModifiable = computed(() => candidat.value?.statut === 'en_attente')

const progressItems = computed(() => [
  { label: 'Documents', done: documents.value.length > 0 },
  { label: 'Langues', done: langues.value.length > 0 },
  { label: 'Diplômes', done: diplomes.value.length > 0 },
  { label: 'Expériences', done: experiences.value.length > 0 },
])

// Charger les données du candidat
const loadCandidatData = async () => {
  try {
    const token = localStorage.getItem('candidat_token')
    
    if (!token) {
      router.push('/candidature/login')
      return
    }

    const response = await $api.get('/candidats/me', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })

    if (response.success) {
      candidat.value = response.candidat
      documents.value = response.candidat.documents || []
    }

    await Promise.all([loadLangues(), loadDiplomes(), loadExperiences(), loadReferenceLists()])
  } catch (error) {
    console.error('Erreur de chargement:', error)
    
    if (error.response?.status === 401) {
      $swal.fire({
        icon: 'error',
        title: 'Session expirée',
        text: 'Veuillez vous reconnecter',
        confirmButtonColor: '#16a34a'
      }).then(() => {
        localStorage.removeItem('candidat_token')
        router.push('/candidature/login')
      })
    }
  } finally {
    loading.value = false
  }
}

const authHeaders = () => ({ Authorization: `Bearer ${localStorage.getItem('candidat_token')}` })

// Langues, diplômes, expériences du candidat
const loadLangues = async () => {
  const response = await $api.get('/candidats/me/langues', { headers: authHeaders() })
  langues.value = response.langues || []
}

const loadDiplomes = async () => {
  const response = await $api.get('/candidats/me/diplomes', { headers: authHeaders() })
  diplomes.value = response.diplomes || []
}

const loadExperiences = async () => {
  const response = await $api.get('/candidats/me/experiences', { headers: authHeaders() })
  experiences.value = response.experiences || []
}

// Référentiels pour les select (langue, établissement, domaine, structure)
const loadReferenceLists = async () => {
  const [l, e, d, s] = await Promise.all([
    $api.get('/langues', { headers: authHeaders() }),
    $api.get('/etablissements', { headers: authHeaders() }),
    $api.get('/domaines', { headers: authHeaders() }),
    $api.get('/structures', { headers: authHeaders() }),
  ])
  referenceLangues.value = Array.isArray(l) ? l : (l?.data || [])
  referenceEtablissements.value = Array.isArray(e) ? e : (e?.data || [])
  referenceDomaines.value = Array.isArray(d) ? d : (d?.data || [])
  referenceStructures.value = Array.isArray(s) ? s : (s?.data || [])
}

const addLangue = async () => {
  try {
    await $api.post('/candidats/me/langues', newLangue.value, { headers: authHeaders() })
    newLangue.value = { langue_id: '', niveau: '' }
    await loadLangues()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Impossible d\'ajouter cette langue' })
  }
}

const deleteLangue = async (id) => {
  try {
    await $api.delete(`/candidats/me/langues/${id}`, { headers: authHeaders() })
    await loadLangues()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Suppression impossible' })
  }
}

const addDiplome = async () => {
  try {
    const formData = new FormData()
    formData.append('intitule', newDiplome.value.intitule)
    if (newDiplome.value.etablissement_id) formData.append('etablissement_id', newDiplome.value.etablissement_id)
    if (newDiplome.value.domaine_id) formData.append('domaine_id', newDiplome.value.domaine_id)
    if (newDiplome.value.annee) formData.append('annee', newDiplome.value.annee)
    if (newDiplome.value.justificatif) formData.append('justificatif', newDiplome.value.justificatif)

    await $api.post('/candidats/me/diplomes', formData, {
      headers: { ...authHeaders(), 'Content-Type': 'multipart/form-data' }
    })
    newDiplome.value = { intitule: '', etablissement_id: '', domaine_id: '', annee: '', justificatif: null }
    await loadDiplomes()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Impossible d\'ajouter ce diplôme' })
  }
}

const deleteDiplome = async (id) => {
  try {
    await $api.delete(`/candidats/me/diplomes/${id}`, { headers: authHeaders() })
    await loadDiplomes()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Suppression impossible' })
  }
}

const addExperience = async () => {
  try {
    const formData = new FormData()
    formData.append('intitule', newExperience.value.intitule)
    if (newExperience.value.structure_id) formData.append('structure_id', newExperience.value.structure_id)
    if (newExperience.value.date_debut) formData.append('date_debut', newExperience.value.date_debut)
    if (newExperience.value.date_fin) formData.append('date_fin', newExperience.value.date_fin)
    if (newExperience.value.justificatif) formData.append('justificatif', newExperience.value.justificatif)

    await $api.post('/candidats/me/experiences', formData, {
      headers: { ...authHeaders(), 'Content-Type': 'multipart/form-data' }
    })
    newExperience.value = { intitule: '', structure_id: '', date_debut: '', date_fin: '', justificatif: null }
    await loadExperiences()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Impossible d\'ajouter cette expérience' })
  }
}

const deleteExperience = async (id) => {
  try {
    await $api.delete(`/candidats/me/experiences/${id}`, { headers: authHeaders() })
    await loadExperiences()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Suppression impossible' })
  }
}

// Déconnexion
const logout = async () => {
  const result = await $swal.fire({
    title: 'Déconnexion',
    text: 'Êtes-vous sûr de vouloir vous déconnecter ?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#dc2626',
    confirmButtonText: 'Oui, me déconnecter',
    cancelButtonText: 'Annuler'
  })

  if (result.isConfirmed) {
    try {
      const token = localStorage.getItem('candidat_token')
      
      await $api.post('/candidats/logout', {}, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
    } catch (error) {
      console.error('Erreur de déconnexion:', error)
    } finally {
      localStorage.removeItem('candidat_token')
      router.push('/accueil')
    }
  }
}

// Utilitaires
const getInitials = (name) => {
  if (!name) return '?'
  const parts = name.split(' ')
  return parts.length >= 2 
    ? `${parts[0][0]}${parts[parts.length - 1][0]}`.toUpperCase()
    : name.substring(0, 2).toUpperCase()
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const getDocumentIcon = (type) => {
  const icons = {
    cv: '📄',
    diplome: '🎓',
    attestation: '📜',
    lettre: '✉️',
    casier: '🔒',
    medical: '⚕️',
    passeport: '🛂',
    autre: '📎'
  }
  return icons[type] || '📎'
}

const getDocumentTypeLabel = (type) => {
  const labels = {
    cv: 'CV',
    diplome: 'Diplôme',
    attestation: 'Attestation',
    lettre: 'Lettre de motivation',
    casier: 'Casier judiciaire',
    medical: 'Certificat médical',
    passeport: 'Passeport',
    autre: 'Autre'
  }
  return labels[type] || 'Document'
}

// Chargement au montage
onMounted(() => {
  loadCandidatData()
})

// SEO
useHead({
  title: 'Mon Dashboard - Gestion Dignitaires',
  meta: [
    { name: 'description', content: 'Dashboard candidat - Suivez l\'état de votre candidature' }
  ]
})
</script>

<style scoped>
/* Animation pour le loader */
@keyframes spin {
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
