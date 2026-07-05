<template>
  <DashboardLayout>
    <div style="zoom: 0.8;" class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <!-- Bouton retour élégant -->
      <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 mb-6">
        <NuxtLink to="/dignitaires" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-all hover:translate-x-[-4px]">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Retour à la liste
        </NuxtLink>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="max-w-7xl mx-auto px-6">
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-green-600 border-t-transparent mx-auto"></div>
          <p class="text-gray-500 mt-6 text-lg">Chargement des informations...</p>
        </div>
      </div>

      <!-- Contenu principal -->
      <div v-else-if="dignitaire" class="max-w-7xl mx-auto px-6 pb-12">
        <!-- Header avec photo et infos principales -->
        <div class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 rounded-2xl shadow-2xl p-8 mb-8 text-white relative">
          <div class="absolute top-6 right-6 flex gap-3">
            <NuxtLink
              :to="`/dignitaires/${route.params.id}/documents`"
              class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2 transition"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Documents
            </NuxtLink>
            <button
              @click="exporterFiche"
              class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2 transition"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Exporter la fiche (PDF)
            </button>
          </div>
          <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <!-- Photo -->
            <div class="relative group">
              <div class="absolute inset-0 bg-white rounded-full blur-xl opacity-30 group-hover:opacity-50 transition"></div>
              <img
                :src="dignitaire.photo ? `/uploads/photos/${dignitaire.photo}` : '/default-avatar.svg'"
                :alt="`Photo de ${dignitaire.prenom}`"
                class="relative w-40 h-40 rounded-full object-cover border-4 border-white shadow-2xl"
                @error="(e) => (e.target as HTMLImageElement).src = '/default-avatar.svg'"
              >
            </div>
            
            <!-- Informations principales -->
            <div class="flex-1 text-center md:text-left">
              <h1 class="text-4xl font-bold mb-3 drop-shadow-lg">
                {{ dignitaire.prenom }} {{ dignitaire.nom }}
              </h1>
              <div class="flex flex-wrap gap-4 justify-center md:justify-start mb-4">
                <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg">
                  <span class="text-white/80 text-sm">NIP:</span>
                  <span class="ml-2 font-semibold">{{ dignitaire.nip || 'N/A' }}</span>
                </div>
                <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg">
                  <span class="text-white/80 text-sm">Matricule:</span>
                  <span class="ml-2 font-semibold">{{ dignitaire.matricule }}</span>
                </div>
                <span class="px-4 py-2 rounded-lg text-sm font-semibold" :class="statutBadgeClass(dignitaire.statut)">
                  {{ statutLabel(dignitaire.statut) }}
                </span>
              </div>
              <p class="text-white/90 text-lg">
                {{ dignitaire.genre }} • {{ dignitaire.etat_civil || 'N/A' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Grille de sections -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Colonne gauche (2/3) -->
          <div class="lg:col-span-2 space-y-6">
            
            <!-- Informations personnelles -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Informations personnelles
                </h2>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="group">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Date de naissance</label>
                    <p class="text-base font-medium text-gray-800 mt-1">{{ formatDate(dignitaire.date_naissance) }}</p>
                  </div>
                  <div class="group">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Lieu de naissance</label>
                    <p class="text-base font-medium text-gray-800 mt-1">
                      {{ dignitaire.lieu_naissance ? dignitaire.lieu_naissance.nom : 'N/A' }}
                    </p>
                  </div>
                  <div class="group">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pays de naissance</label>
                    <p class="text-base font-medium text-gray-800 mt-1">
                      {{ dignitaire.lieu_naissance?.pays?.nom || 'N/A' }}
                    </p>
                  </div>
                  <div class="group">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Région</label>
                    <p class="text-base font-medium text-gray-800 mt-1">
                      {{ dignitaire.lieu_naissance?.region?.nom || 'N/A' }}
                    </p>
                  </div>
                  <div class="group">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nationalité</label>
                    <p class="text-base font-medium text-gray-800 mt-1">{{ dignitaire.nationalite || 'N/A' }}</p>
                  </div>
                  <div class="group col-span-2">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Adresse</label>
                    <p class="text-base font-medium text-gray-800 mt-1">{{ dignitaire.adresse || 'N/A' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Postes occupés -->
            <div v-if="dignitaire.postes && dignitaire.postes.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gabon-yellow-500 to-gabon-yellow-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  Postes occupés
                </h2>
              </div>
              <div class="p-6 space-y-4">
                <div v-for="poste in dignitaire.postes" :key="poste.id" class="border-l-4 border-gabon-yellow-500 bg-yellow-50 p-4 rounded-r-lg hover:shadow-md transition">
                  <h3 class="font-bold text-lg text-gray-800 mb-2">{{ poste.intitule }}</h3>
                  <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                      <span class="text-gray-600">Entité:</span>
                      <span class="ml-1 font-medium text-gray-800">{{ poste.entite?.nom || 'N/A' }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Ville:</span>
                      <span class="ml-1 font-medium text-gray-800">{{ poste.ville?.nom || 'N/A' }}</span>
                    </div>
                    <div class="col-span-2">
                      <span class="text-gray-600">Période:</span>
                      <span class="ml-1 font-medium text-gray-800">
                        {{ formatDate(poste.date_debut) }} -
                        {{ poste.statut === 'terminee' ? formatDate(poste.date_fin) : 'En cours' }}
                      </span>
                      <span v-if="poste.statut === 'terminee' && poste.motif_fin" class="ml-2 text-xs text-gray-500">({{ motifFinLabel(poste.motif_fin) }})</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Nominations -->
            <div v-if="dignitaire.nominations && dignitaire.nominations.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Nominations
                </h2>
              </div>
              <div class="p-6 space-y-4">
                <div v-for="nomination in dignitaire.nominations" :key="nomination.id" class="border-l-4 border-gabon-blue-600 bg-blue-50 p-4 rounded-r-lg hover:shadow-md transition">
                  <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-lg text-gray-800">{{ nomination.fonction || nomination.poste?.intitule || 'N/A' }}</h3>
                    <span v-if="nomination.statut === 'terminee'" class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Terminée</span>
                    <span v-else class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">En cours</span>
                  </div>
                  <div class="space-y-1 text-sm">
                    <p><span class="text-gray-600">Date de début:</span> <span class="font-medium text-gray-800">{{ formatDate(nomination.date_debut) }}</span></p>
                    <p v-if="nomination.statut === 'terminee'"><span class="text-gray-600">Date de fin:</span> <span class="font-medium text-gray-800">{{ formatDate(nomination.date_fin) }}</span> <span class="text-gray-500">({{ motifFinLabel(nomination.motif_fin) }})</span></p>
                    <p v-if="nomination.numero_decret"><span class="text-gray-600">Numéro de décret:</span> <span class="font-medium text-gray-800">{{ nomination.numero_decret }}</span></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Langues parlées -->
            <div v-if="dignitaire.langues_parlees && dignitaire.langues_parlees.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                  </svg>
                  Langues parlées
                </h2>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div v-for="langue in dignitaire.langues_parlees" :key="langue.id" class="bg-green-50 p-3 rounded-lg hover:shadow-md transition">
                    <p class="font-bold text-gray-800">{{ langue.langue?.nom || 'N/A' }}</p>
                    <p class="text-sm text-gray-600">
                      <span class="font-medium">Niveau:</span> {{ langue.niveau || 'N/A' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Diplômes -->
            <div v-if="dignitaire.diplomes && dignitaire.diplomes.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gabon-yellow-500 to-gabon-yellow-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                  </svg>
                  Diplômes
                </h2>
              </div>
              <div class="p-6 space-y-4">
                <div v-for="diplome in dignitaire.diplomes" :key="diplome.id" class="bg-yellow-50 p-4 rounded-lg hover:shadow-md transition">
                  <h3 class="font-bold text-gray-800 mb-2">{{ diplome.intitule }}</h3>
                  <div class="space-y-1 text-sm">
                    <p><span class="text-gray-600">Établissement:</span> <span class="font-medium text-gray-800">{{ diplome.etablissement?.nom || 'N/A' }}</span></p>
                    <p><span class="text-gray-600">Domaine:</span> <span class="font-medium text-gray-800">{{ diplome.domaine?.nom || 'N/A' }}</span></p>
                    <p><span class="text-gray-600">Année:</span> <span class="font-medium text-gray-800">{{ diplome.annee_obtention || 'N/A' }}</span></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Décorations -->
            <div v-if="dignitaire.decorations && dignitaire.decorations.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                  </svg>
                  Décorations
                </h2>
              </div>
              <div class="p-6 space-y-4">
                <div v-for="decoration in dignitaire.decorations" :key="decoration.id" class="border-l-4 border-gabon-blue-600 bg-blue-50 p-4 rounded-r-lg hover:shadow-md transition">
                  <h3 class="font-bold text-lg text-gray-800 mb-2">{{ decoration.nom }}</h3>
                  <div class="space-y-1 text-sm">
                    <p v-if="decoration.type"><span class="text-gray-600">Type:</span> <span class="font-medium text-gray-800">{{ decoration.type }}</span></p>
                    <p v-if="decoration.niveau"><span class="text-gray-600">Niveau:</span> <span class="font-medium text-gray-800">{{ decoration.niveau }}</span></p>
                    <p v-if="decoration.grade"><span class="text-gray-600">Grade:</span> <span class="font-medium text-gray-800">{{ decoration.grade }}</span></p>
                    <p><span class="text-gray-600">Date d'attribution:</span> <span class="font-medium text-gray-800">{{ decoration.pivot?.date_attribution ? formatDate(decoration.pivot.date_attribution) : 'N/A' }}</span></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Colonne droite (1/3) -->
          <div class="space-y-6">
            
            <!-- Téléphones -->
            <div v-if="dignitaire.telephones && dignitaire.telephones.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                  Téléphones
                </h2>
              </div>
              <div class="p-6 space-y-3">
                <div v-for="tel in dignitaire.telephones" :key="tel.id" class="bg-green-50 p-4 rounded-lg hover:shadow-md transition">
                  <div class="flex items-center justify-between mb-1">
                    <p class="font-bold text-gray-800">{{ tel.numero }}</p>
                    <span v-if="tel.principal" class="bg-gabon-green-600 text-white text-xs px-2 py-1 rounded-full">Principal</span>
                  </div>
                  <p class="text-sm text-gray-600 capitalize">{{ tel.type }}</p>
                </div>
              </div>
            </div>

            <!-- Emails -->
            <div v-if="dignitaire.emails && dignitaire.emails.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gabon-yellow-500 to-gabon-yellow-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  Emails
                </h2>
              </div>
              <div class="p-6 space-y-3">
                <div v-for="email in dignitaire.emails" :key="email.id" class="bg-yellow-50 p-4 rounded-lg hover:shadow-md transition">
                  <div class="flex items-center justify-between mb-1">
                    <p class="font-medium text-gray-800 break-all">{{ email.email }}</p>
                    <span v-if="email.principal" class="bg-gabon-yellow-600 text-white text-xs px-2 py-1 rounded-full ml-2">Principal</span>
                  </div>
                  <p class="text-sm text-gray-600 capitalize">{{ email.type }}</p>
                </div>
              </div>
            </div>

            <!-- Enfants -->
            <div v-if="dignitaire.enfants && dignitaire.enfants.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  Enfants
                </h2>
              </div>
              <div class="p-6 space-y-3">
                <div v-for="enfant in dignitaire.enfants" :key="enfant.id" class="bg-blue-50 p-4 rounded-lg hover:shadow-md transition">
                  <h3 class="font-bold text-gray-800 mb-2">{{ enfant.prenom }} {{ enfant.nom }}</h3>
                  <div class="space-y-1 text-sm">
                    <p><span class="text-gray-600">Né(e) le:</span> <span class="font-medium text-gray-800">{{ formatDate(enfant.date_naissance) }}</span></p>
                    <p><span class="text-gray-600">À:</span> <span class="font-medium text-gray-800">{{ enfant.lieu_naissance?.nom || 'N/A' }}</span></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Conjoint(s) -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Conjoint(s)
                </h2>
                <button
                  v-if="permissions.peutEcrire('Dignitaire')"
                  @click="openConjointModal()"
                  class="text-white/90 hover:text-white text-sm font-semibold bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition"
                >
                  + Ajouter
                </button>
              </div>
              <div class="p-6 space-y-3">
                <div v-if="conjoints.length === 0" class="text-sm text-gray-500 italic">Aucun conjoint enregistré</div>
                <div v-for="conjoint in conjoints" :key="conjoint.id" class="bg-green-50 p-4 rounded-lg hover:shadow-md transition">
                  <div class="flex items-center justify-between mb-1">
                    <h3 class="font-bold text-gray-800">{{ conjoint.nom_complet }}</h3>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="conjointStatutClass(conjoint.status_badge?.color)">
                      {{ conjoint.status_badge?.text || conjoint.statut }}
                    </span>
                  </div>
                  <div class="space-y-1 text-sm mb-3">
                    <p v-if="conjoint.profession"><span class="text-gray-600">Profession:</span> <span class="font-medium text-gray-800">{{ conjoint.profession }}</span></p>
                    <p v-if="conjoint.duree_mariage"><span class="text-gray-600">Mariage:</span> <span class="font-medium text-gray-800">{{ conjoint.duree_mariage }}</span></p>
                    <p v-if="conjoint.est_militaire"><span class="inline-block bg-gabon-blue-100 text-gabon-blue-700 text-xs px-2 py-0.5 rounded-full">Militaire{{ conjoint.grade_militaire ? ` (${conjoint.grade_militaire})` : '' }}</span></p>
                    <p v-if="conjoint.est_dignitaire"><span class="inline-block bg-gabon-yellow-100 text-gabon-yellow-700 text-xs px-2 py-0.5 rounded-full ml-1">Dignitaire{{ conjoint.fonction_dignitaire ? ` (${conjoint.fonction_dignitaire})` : '' }}</span></p>
                  </div>
                  <div class="flex gap-2 flex-wrap">
                    <button
                      v-if="permissions.peutEcrire('Dignitaire')"
                      @click="openConjointModal(conjoint)"
                      class="text-xs font-semibold text-gabon-blue-700 bg-gabon-blue-50 hover:bg-gabon-blue-100 px-3 py-1.5 rounded-lg transition"
                    >
                      Modifier
                    </button>
                    <button
                      v-if="conjoint.statut === 'actif' && permissions.peutEcrire('Dignitaire')"
                      @click="openTerminerUnionModal(conjoint)"
                      class="text-xs font-semibold text-orange-700 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg transition"
                    >
                      Terminer l'union
                    </button>
                    <button
                      v-if="permissions.peutSupprimer()"
                      @click="deleteConjoint(conjoint)"
                      class="text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition"
                    >
                      Supprimer
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Historique -->
            <div v-if="historique.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Historique
                </h2>
              </div>
              <div class="p-6">
                <div class="space-y-3">
                  <div v-for="log in historique" :key="log.id" class="flex items-start gap-3 border-l-4 border-gray-300 bg-gray-50 p-3 rounded-r-lg">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold flex-shrink-0" :class="{
                      'bg-green-100 text-green-700': log.action === 'created',
                      'bg-blue-100 text-blue-700': log.action === 'updated',
                      'bg-red-100 text-red-700': log.action === 'deleted'
                    }">
                      {{ log.action === 'created' ? 'Créé' : log.action === 'updated' ? 'Modifié' : 'Supprimé' }}
                    </span>
                    <div class="flex-1 text-sm">
                      <span class="font-medium text-gray-800">{{ log.causer_label || 'Système' }}</span>
                      <span class="text-gray-500"> — {{ formatDate(log.created_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Erreur -->
      <div v-else class="max-w-7xl mx-auto px-6">
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
          <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <p class="text-red-500 text-lg font-medium">Erreur lors du chargement du dignitaire</p>
        </div>
      </div>
    </div>

    <!-- Modal Ajouter/Modifier un conjoint -->
    <div
      v-if="showConjointModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeConjointModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-scale-in">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
          <h4 class="text-xl font-bold text-white">
            {{ selectedConjoint ? 'Modifier' : 'Ajouter' }} un conjoint
          </h4>
          <button @click="closeConjointModal" class="text-white hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveConjoint" class="p-6">
          <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom <span class="text-red-500">*</span></label>
                <input v-model="conjointForm.nom" type="text" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom <span class="text-red-500">*</span></label>
                <input v-model="conjointForm.prenom" type="text" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Genre <span class="text-red-500">*</span></label>
                <select v-model="conjointForm.genre" required class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                  <option value="">-- Sélectionner --</option>
                  <option value="M">Masculin</option>
                  <option value="F">Féminin</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de naissance</label>
                <input v-model="conjointForm.date_naissance" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lieu de naissance</label>
                <select v-model="conjointForm.lieu_naissance_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                  <option value="">-- Sélectionner une ville --</option>
                  <option v-for="ville in villes" :key="ville.id" :value="ville.id">{{ ville.nom }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nationalité</label>
                <select v-model="conjointForm.nationalite_id" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
                  <option value="">-- Sélectionner un pays --</option>
                  <option v-for="pays in paysList" :key="pays.id" :value="pays.id">{{ pays.nom }}</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Profession</label>
                <input v-model="conjointForm.profession" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Employeur</label>
                <input v-model="conjointForm.employeur" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de mariage</label>
                <input v-model="conjointForm.date_mariage" type="date" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lieu de mariage</label>
                <input v-model="conjointForm.lieu_mariage" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                <input v-model="conjointForm.telephone" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input v-model="conjointForm.email" type="email" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Adresse</label>
              <input v-model="conjointForm.adresse" type="text" class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-center gap-3">
                <input v-model="conjointForm.est_militaire" type="checkbox" id="est_militaire" class="w-5 h-5 rounded text-gabon-green-600 focus:ring-gabon-green-500">
                <label for="est_militaire" class="text-sm font-semibold text-gray-700">Est militaire</label>
              </div>
              <input
                v-if="conjointForm.est_militaire"
                v-model="conjointForm.grade_militaire"
                type="text"
                placeholder="Grade militaire"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-center gap-3">
                <input v-model="conjointForm.est_dignitaire" type="checkbox" id="est_dignitaire" class="w-5 h-5 rounded text-gabon-green-600 focus:ring-gabon-green-500">
                <label for="est_dignitaire" class="text-sm font-semibold text-gray-700">Est également dignitaire</label>
              </div>
              <input
                v-if="conjointForm.est_dignitaire"
                v-model="conjointForm.fonction_dignitaire"
                type="text"
                placeholder="Fonction"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition"
              >
            </div>
          </div>

          <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeConjointModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">Annuler</button>
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
  </DashboardLayout>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const route = useRoute()
const config = useRuntimeConfig()
const authStore = useAuthStore()
const fileDownload = useFileDownload()
const permissions = usePermissions()
const api = useApi()
const referentiels = useReferentiels()

const dignitaire = ref(null)
const historique = ref([])
const loading = ref(true)

// Conjoints
const conjoints = ref<any[]>([])
const villes = ref<any[]>([])
const paysList = ref<any[]>([])
const showConjointModal = ref(false)
const selectedConjoint = ref<any>(null)
const showTerminerUnionModal = ref(false)
const conjointATerminer = ref<any>(null)

const conjointForm = reactive({
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

function conjointStatutClass(color?: string) {
  const classes: Record<string, string> = {
    green: 'bg-green-100 text-green-700',
    orange: 'bg-orange-100 text-orange-700',
    gray: 'bg-gray-200 text-gray-700',
    yellow: 'bg-yellow-100 text-yellow-700'
  }
  return classes[color || ''] || 'bg-gray-100 text-gray-700'
}

async function loadConjoints() {
  try {
    const response: any = await api.getConjoints(Number(route.params.id))
    conjoints.value = response.conjoints || []
  } catch (error) {
    console.error('Erreur chargement conjoints:', error)
  }
}

function openConjointModal(conjoint: any = null) {
  selectedConjoint.value = conjoint
  if (conjoint) {
    conjointForm.nom = conjoint.nom
    conjointForm.prenom = conjoint.prenom
    conjointForm.genre = conjoint.genre
    conjointForm.date_naissance = conjoint.date_naissance || ''
    conjointForm.lieu_naissance_id = conjoint.lieu_naissance_id || ''
    conjointForm.nationalite_id = conjoint.nationalite_id || ''
    conjointForm.profession = conjoint.profession || ''
    conjointForm.employeur = conjoint.employeur || ''
    conjointForm.date_mariage = conjoint.date_mariage || ''
    conjointForm.lieu_mariage = conjoint.lieu_mariage || ''
    conjointForm.telephone = conjoint.telephone || ''
    conjointForm.email = conjoint.email || ''
    conjointForm.adresse = conjoint.adresse || ''
    conjointForm.est_militaire = !!conjoint.est_militaire
    conjointForm.grade_militaire = conjoint.grade_militaire || ''
    conjointForm.est_dignitaire = !!conjoint.est_dignitaire
    conjointForm.fonction_dignitaire = conjoint.fonction_dignitaire || ''
  } else {
    conjointForm.nom = ''
    conjointForm.prenom = ''
    conjointForm.genre = ''
    conjointForm.date_naissance = ''
    conjointForm.lieu_naissance_id = ''
    conjointForm.nationalite_id = ''
    conjointForm.profession = ''
    conjointForm.employeur = ''
    conjointForm.date_mariage = ''
    conjointForm.lieu_mariage = ''
    conjointForm.telephone = ''
    conjointForm.email = ''
    conjointForm.adresse = ''
    conjointForm.est_militaire = false
    conjointForm.grade_militaire = ''
    conjointForm.est_dignitaire = false
    conjointForm.fonction_dignitaire = ''
  }
  showConjointModal.value = true
}

function closeConjointModal() {
  showConjointModal.value = false
  selectedConjoint.value = null
}

async function saveConjoint() {
  const { $swal } = useNuxtApp()
  try {
    if (selectedConjoint.value) {
      await api.updateConjoint(selectedConjoint.value.id, conjointForm)
    } else {
      await api.createConjoint(Number(route.params.id), conjointForm)
    }

    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: selectedConjoint.value ? 'Conjoint modifié avec succès' : 'Conjoint ajouté avec succès',
      timer: 2000,
      showConfirmButton: false
    })

    closeConjointModal()
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

async function deleteConjoint(conjoint: any) {
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
      await api.deleteConjoint(conjoint.id)
      $swal.fire({
        icon: 'success',
        title: 'Supprimé',
        text: 'Le conjoint a été supprimé avec succès',
        timer: 2000,
        showConfirmButton: false
      })
      loadConjoints()
    } catch (error) {
      console.error('Erreur suppression conjoint:', error)
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Erreur lors de la suppression'
      })
    }
  }
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

async function exporterFiche() {
  try {
    await fileDownload.download(
      `/dignitaires/${route.params.id}/export-pdf`,
      {},
      `fiche-${dignitaire.value?.matricule || route.params.id}.pdf`
    )
  } catch (error) {
    console.error('Erreur export fiche dignitaire:', error)
  }
}

onMounted(async () => {
  try {
    const response = await $fetch(`${config.public.apiBase}/dignitaires/${route.params.id}`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    dignitaire.value = response
  } catch (error) {
    console.error('Erreur chargement dignitaire:', error)
  } finally {
    loading.value = false
  }

  try {
    const historiqueResponse: any = await $fetch(`${config.public.apiBase}/admin/audit-logs`, {
      params: { auditable_type: 'Dignitaire', auditable_id: route.params.id, per_page: 50 },
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })
    historique.value = historiqueResponse.logs.data
  } catch (error) {
    console.error('Erreur chargement historique:', error)
  }

  loadConjoints()
  villes.value = await referentiels.getVilles()
  paysList.value = await referentiels.getPays()
})

function statutLabel(statut: string | null) {
  return { actif: 'Actif', retraite: 'Retraité', non_localise: 'Non localisé' }[statut || ''] || 'Actif'
}

function statutBadgeClass(statut: string | null) {
  const classes: Record<string, string> = {
    actif: 'bg-white/20 backdrop-blur-sm text-white',
    retraite: 'bg-gray-800/60 backdrop-blur-sm text-white',
    non_localise: 'bg-orange-500/80 backdrop-blur-sm text-white'
  }
  return classes[statut || ''] || classes.actif
}

function motifFinLabel(motif: string | null) {
  if (motif === 'mise_a_disposition') return 'Mise à disposition'
  if (motif === 'fin_fonction') return 'Fin de fonction'
  return ''
}

function formatDate(date: string | null) {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<style scoped>
/* Animations personnalisées */
.group:hover {
  transform: translateY(-2px);
  transition: all 0.3s ease;
}

/* Effet de survol sur les cartes */
.hover\:shadow-md:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
