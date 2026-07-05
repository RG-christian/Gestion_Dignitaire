<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gabon-green-50">
    <!-- Navbar simple -->
    <nav class="fixed top-4 left-4 right-4 z-50">
      <div class="max-w-5xl mx-auto">
        <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-gray-200/50 px-6 py-4 flex items-center justify-between">
          <NuxtLink to="/accueil" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
            <div class="bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-xl p-2">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <span class="font-bold text-gray-900 hidden sm:block">Gestion Dignitaires</span>
          </NuxtLink>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 hidden sm:block">Déjà inscrit ?</span>
            <NuxtLink to="/candidature/login" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg border-2 border-gray-200 hover:border-gabon-green-600 transition-all duration-300">
              Se connecter
            </NuxtLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Formulaire principal -->
    <div class="pt-32 pb-20 px-4">
      <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-10">
          <div class="inline-flex items-center gap-2 bg-gabon-green-100 px-4 py-2 rounded-full mb-6">
            <svg class="w-5 h-5 text-gabon-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-semibold text-gabon-green-700">Étape {{ currentStep }} sur 3</span>
          </div>
          <h1 class="text-4xl font-bold text-gray-900 mb-4">Formulaire de Candidature Dignitaire</h1>
          <p class="text-lg text-gray-600">Complétez toutes les informations requises pour votre dossier de candidature</p>
        </div>

        <!-- Progress bar -->
        <div class="mb-10">
          <div class="flex items-center justify-between mb-3 text-xs md:text-sm">
            <span v-for="step in 3" :key="step" class="font-medium text-center" :class="step <= currentStep ? 'text-gabon-green-600' : 'text-gray-400'">
              {{ getStepLabel(step) }}
            </span>
          </div>
          <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 transition-all duration-500 rounded-full" :style="{ width: (currentStep / 3 * 100) + '%' }"></div>
          </div>
        </div>

        <!-- Card principale -->
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden">
          <!-- Step 1: Informations personnelles -->
          <form v-if="currentStep === 1" @submit.prevent="nextStep" class="p-8 space-y-6">
            <!-- Photo -->
            <div class="flex justify-center mb-2">
              <label class="relative cursor-pointer group">
                <div class="w-28 h-28 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden group-hover:border-gabon-green-500 transition-colors">
                  <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                  <svg v-else class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                </div>
                <input type="file" accept="image/*" @change="handlePhotoChange" class="hidden">
              </label>
            </div>
            <p class="text-center text-xs text-gray-500 -mt-4">Photo d'identité (facultative)</p>

            <div class="grid md:grid-cols-2 gap-6">
              <!-- NIP -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">NIP</label>
                <input v-model="form.nip" type="text" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" placeholder="Numéro d'identification personnel">
              </div>

              <!-- Matricule -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Matricule</label>
                <input v-model="form.matricule" type="text" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" placeholder="Matricule (si applicable)">
              </div>

              <!-- Nom -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Nom <span class="text-red-500">*</span>
                </label>
                <input v-model="form.nom" type="text" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" placeholder="Votre nom">
              </div>

              <!-- Prénom -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Prénom <span class="text-red-500">*</span>
                </label>
                <input v-model="form.prenom" type="text" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" placeholder="Votre prénom">
              </div>

              <!-- Date de naissance -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Date de naissance <span class="text-red-500">*</span>
                </label>
                <input v-model="form.date_naissance" type="date" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all">
              </div>

              <!-- Genre -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Genre <span class="text-red-500">*</span>
                </label>
                <select v-model="form.genre" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all">
                  <option value="">Sélectionnez...</option>
                  <option value="M">Masculin</option>
                  <option value="F">Féminin</option>
                </select>
              </div>

              <!-- Pays de naissance -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Pays de naissance <span class="text-red-500">*</span>
                </label>
                <select v-model="form.pays_naissance_id" @change="onPaysNaissanceChange" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all">
                  <option value="">Sélectionnez un pays...</option>
                  <option v-for="pays in paysListe" :key="pays.id" :value="pays.id">{{ pays.nom }}</option>
                </select>
              </div>

              <!-- Lieu de naissance (ville) -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Ville de naissance <span class="text-red-500">*</span>
                </label>
                <select v-if="!showCustomVilleNaissance" v-model="form.lieu_naissance_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all">
                  <option value="">{{ villesNaissanceFiltered.length > 0 ? 'Sélectionnez une ville...' : 'Sélectionnez d\'abord un pays' }}</option>
                  <option v-for="ville in villesNaissanceFiltered" :key="ville.id" :value="ville.id">{{ ville.nom }}</option>
                </select>
                <input v-else v-model="form.ville_naissance_custom" type="text" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" placeholder="Nom de la ville">
                <button type="button" @click="showCustomVilleNaissance = !showCustomVilleNaissance" class="mt-2 text-sm text-gabon-green-600 hover:text-gabon-green-700 font-semibold">
                  {{ showCustomVilleNaissance ? '← Retour à la liste' : 'Ma ville n\'est pas dans la liste' }}
                </button>
              </div>

              <!-- Email -->
              <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Email <span class="text-red-500">*</span>
                </label>
                <input v-model="form.email" type="email" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" placeholder="votre.email@example.com">
              </div>

              <!-- Téléphone -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Téléphone
                </label>
                <input v-model="form.telephone" type="tel" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" placeholder="+241 01 23 45 67">
              </div>

              <!-- État civil -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  État civil
                </label>
                <select v-model="form.etat_civil" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all">
                  <option value="">Sélectionnez...</option>
                  <option value="Célibataire">Célibataire</option>
                  <option value="Marié(e)">Marié(e)</option>
                  <option value="Divorcé(e)">Divorcé(e)</option>
                  <option value="Veuf(ve)">Veuf(ve)</option>
                </select>
              </div>

              <!-- Adresse -->
              <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Adresse
                </label>
                <textarea v-model="form.adresse" rows="3" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" placeholder="Votre adresse complète"></textarea>
              </div>

              <!-- Mot de passe -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Mot de passe <span class="text-red-500">*</span>
                </label>
                <input v-model="form.password" type="password" required minlength="8" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" placeholder="Minimum 8 caractères">
              </div>

              <!-- Confirmation mot de passe -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Confirmer mot de passe <span class="text-red-500">*</span>
                </label>
                <input v-model="form.password_confirmation" type="password" required minlength="8" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" placeholder="Confirmez le mot de passe">
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
              <NuxtLink to="/accueil" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-xl font-semibold transition-colors">
                Annuler
              </NuxtLink>
              <button type="submit" class="px-8 py-3 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-bold rounded-xl shadow-lg shadow-gabon-green-600/30 hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                Continuer
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
              </button>
            </div>
          </form>

          <!-- Step 2: Documents -->
          <form v-if="currentStep === 2" @submit.prevent="nextStep" class="p-8 space-y-6">
            <div class="text-center mb-8">
              <div class="w-20 h-20 bg-gabon-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gabon-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
              </div>
              <h3 class="text-2xl font-bold text-gray-900 mb-2">Upload de documents</h3>
              <p class="text-gray-600">Ajoutez vos documents (CV, diplômes, attestations...)</p>
            </div>

            <!-- Zone de drop -->
            <div @drop.prevent="handleFileDrop" @dragover.prevent="isDragging = true" @dragleave="isDragging = false" :class="{ 'border-gabon-green-500 bg-gabon-green-50': isDragging }" class="border-4 border-dashed border-gray-300 rounded-2xl p-12 text-center transition-all duration-300 cursor-pointer hover:border-gabon-green-500 hover:bg-gabon-green-50" @click="$refs.fileInput.click()">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
              </svg>
              <p class="text-lg font-semibold text-gray-700 mb-2">Glissez-déposez vos fichiers ici</p>
              <p class="text-sm text-gray-500 mb-4">ou cliquez pour parcourir</p>
              <p class="text-xs text-gray-400">PDF, DOC, DOCX, JPG, PNG (Max 10 Mo)</p>
              <input ref="fileInput" type="file" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" @change="handleFileSelect" class="hidden">
            </div>

            <!-- Liste des fichiers -->
            <div v-if="uploadedFiles.length > 0" class="space-y-3">
              <h4 class="font-bold text-gray-900 mb-3">Fichiers ajoutés ({{ uploadedFiles.length }})</h4>
              <div v-for="(file, index) in uploadedFiles" :key="index" class="flex items-center justify-between bg-gray-50 rounded-xl p-4 border border-gray-200 hover:border-gabon-green-500 transition-colors group">
                <div class="flex items-center gap-3 flex-1">
                  <div class="w-10 h-10 bg-gabon-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gabon-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ file.name }}</p>
                    <p class="text-sm text-gray-500">{{ formatFileSize(file.size) }}</p>
                  </div>
                  <select v-model="file.type" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all">
                    <option value="">Type...</option>
                    <option value="cv">CV</option>
                    <option value="diplome">Diplôme</option>
                    <option value="attestation">Attestation</option>
                    <option value="lettre">Lettre</option>
                    <option value="casier">Casier judiciaire</option>
                    <option value="medical">Certificat médical</option>
                    <option value="autre">Autre</option>
                  </select>
                </div>
                <button type="button" @click="removeFile(index)" class="ml-3 p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>

            <div v-if="uploadedFiles.length === 0" class="text-center py-8">
              <p class="text-gray-500">Aucun fichier ajouté pour le moment</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
              <button type="button" @click="previousStep" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-xl font-semibold transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Retour
              </button>
              <button type="submit" class="px-8 py-3 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-bold rounded-xl shadow-lg shadow-gabon-green-600/30 hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                Continuer
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
              </button>
            </div>
          </form>

          <!-- Step 3: Confirmation -->
          <div v-if="currentStep === 3" class="p-8">
            <div class="text-center mb-8">
              <div class="w-20 h-20 bg-gabon-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gabon-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
              </div>
              <h3 class="text-2xl font-bold text-gray-900 mb-2">Vérification finale</h3>
              <p class="text-gray-600">Vérifiez vos informations avant de soumettre</p>
            </div>

            <!-- Résumé des informations COMPLET -->
            <div class="space-y-4 mb-8">
              <!-- Photo de profil -->
              <div v-if="photoPreview" class="bg-gray-50 rounded-xl p-6 border border-gray-200 text-center">
                <img :src="photoPreview" class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-lg">
                <p class="text-sm text-gray-600 mt-2">Photo d'identité</p>
              </div>

              <!-- Informations personnelles -->
              <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <svg class="w-5 h-5 text-gabon-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Informations personnelles
                </h4>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                  <div v-if="form.nip"><span class="text-gray-600">NIP :</span> <span class="font-semibold text-gray-900">{{ form.nip }}</span></div>
                  <div v-if="form.matricule"><span class="text-gray-600">Matricule :</span> <span class="font-semibold text-gray-900">{{ form.matricule }}</span></div>
                  <div><span class="text-gray-600">Nom :</span> <span class="font-semibold text-gray-900">{{ form.nom }}</span></div>
                  <div><span class="text-gray-600">Prénom :</span> <span class="font-semibold text-gray-900">{{ form.prenom }}</span></div>
                  <div><span class="text-gray-600">Date de naissance :</span> <span class="font-semibold text-gray-900">{{ formatDateFr(form.date_naissance) }}</span></div>
                  <div><span class="text-gray-600">Genre :</span> <span class="font-semibold text-gray-900">{{ form.genre === 'M' ? 'Masculin' : 'Féminin' }}</span></div>
                  <div class="md:col-span-2" v-if="form.lieu_naissance_id || form.ville_naissance_custom">
                    <span class="text-gray-600">Lieu de naissance :</span> 
                    <span class="font-semibold text-gray-900">{{ getLieuNaissanceLabel() }} ({{ getPaysNaissanceLabel() }})</span>
                  </div>
                  <div v-if="form.etat_civil" class="md:col-span-2"><span class="text-gray-600">État civil :</span> <span class="font-semibold text-gray-900">{{ form.etat_civil }}</span></div>
                </div>
              </div>

              <!-- Coordonnées -->
              <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <svg class="w-5 h-5 text-gabon-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  Coordonnées
                </h4>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                  <div class="md:col-span-2"><span class="text-gray-600">Email :</span> <span class="font-semibold text-gray-900">{{ form.email }}</span></div>
                  <div v-if="form.telephone"><span class="text-gray-600">Téléphone :</span> <span class="font-semibold text-gray-900">{{ form.telephone }}</span></div>
                  <div v-if="form.adresse" class="md:col-span-2"><span class="text-gray-600">Adresse :</span> <span class="font-semibold text-gray-900">{{ form.adresse }}</span></div>
                </div>
              </div>

              <!-- Documents -->
              <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <svg class="w-5 h-5 text-gabon-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Documents joints ({{ uploadedFiles.length }})
                </h4>
                <div v-if="uploadedFiles.length > 0" class="space-y-2">
                  <div v-for="(file, index) in uploadedFiles" :key="index" class="flex items-center justify-between bg-white rounded-lg p-3 border border-gray-200">
                    <div class="flex items-center gap-3">
                      <span class="text-xl">{{ getDocumentIcon(file.type) }}</span>
                      <div>
                        <p class="text-sm font-semibold text-gray-900">{{ file.name }}</p>
                        <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
                      </div>
                    </div>
                    <span class="px-3 py-1 bg-gabon-blue-100 text-gabon-blue-700 rounded-full text-xs font-semibold">
                      {{ getDocumentTypeLabel(file.type) }}
                    </span>
                  </div>
                </div>
                <p v-else class="text-gray-500 text-sm">Aucun document ajouté</p>
              </div>
            </div>

            <!-- Checkbox acceptation -->
            <div class="mb-8">
              <label class="flex items-start gap-3 cursor-pointer group">
                <input v-model="acceptTerms" type="checkbox" class="mt-1 w-5 h-5 border-2 border-gray-300 rounded text-gabon-green-600 focus:ring-2 focus:ring-gabon-green-500">
                <span class="text-sm text-gray-700 group-hover:text-gray-900">
                  J'atteste que les informations fournies sont exactes et j'accepte les 
                  <a href="#" class="text-gabon-green-600 hover:underline font-semibold">conditions d'utilisation</a> 
                  de la plateforme.
                </span>
              </label>
            </div>

            <!-- Actions finales -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
              <button type="button" @click="previousStep" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-xl font-semibold transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Retour
              </button>
              <button @click="submitForm" :disabled="!acceptTerms || loading" :class="{ 'opacity-50 cursor-not-allowed': !acceptTerms || loading }" class="px-8 py-3 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-bold rounded-xl shadow-lg shadow-gabon-green-600/30 hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                <svg v-if="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ loading ? 'Envoi en cours...' : 'Soumettre ma candidature' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const { $api, $swal } = useNuxtApp()

// État du formulaire
const currentStep = ref(1)
const loading = ref(false)
const acceptTerms = ref(false)
const isDragging = ref(false)

// Données du formulaire
const form = ref({
  nip: '',
  matricule: '',
  photo: '',
  pays_naissance_id: '',
  lieu_naissance_id: '',
  ville_naissance_custom: '',
  nom: '',
  prenom: '',
  date_naissance: '',
  genre: '',
  email: '',
  telephone: '',
  etat_civil: '',
  adresse: '',
  password: '',
  password_confirmation: ''
})

const getStepLabel = (step) => {
  return { 1: 'Identité', 2: 'Documents', 3: 'Confirmation' }[step] || ''
}

// Photo (encodée en base64, comme attendu par l'API)
const photoPreview = ref(null)
const handlePhotoChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = () => {
    form.value.photo = reader.result
    photoPreview.value = reader.result
  }
  reader.readAsDataURL(file)
}

// Liste des pays
const paysListe = ref([])
const { data: paysData } = await useAsyncData('candidature-pays', async () => {
  try {
    return await $api.get('/public/pays')
  } catch (error) {
    console.error('Erreur chargement pays:', error)
    return []
  }
})
paysListe.value = Array.isArray(paysData.value) ? paysData.value : (paysData.value?.data || [])

// Villes (pour le lieu de naissance) - endpoint public, avant authentification du candidat
const villes = ref([])
const { data: villesData } = await useAsyncData('candidature-villes', async () => {
  try {
    return await $api.get('/public/villes')
  } catch (error) {
    console.error('Erreur chargement villes:', error)
    return []
  }
})
villes.value = Array.isArray(villesData.value) ? villesData.value : (villesData.value?.data || [])

// Toggle pour afficher le champ custom de ville
const showCustomVilleNaissance = ref(false)

// Computed - Villes filtrées par pays sélectionné
const villesNaissanceFiltered = computed(() => {
  if (!form.value.pays_naissance_id) {
    return []
  }
  return villes.value.filter(ville => ville.pays_id == form.value.pays_naissance_id)
})

// Événement lors du changement de pays
const onPaysNaissanceChange = () => {
  // Réinitialiser la ville sélectionnée et le champ custom
  form.value.lieu_naissance_id = ''
  form.value.ville_naissance_custom = ''
  showCustomVilleNaissance.value = false
}

// Fichiers uploadés
const uploadedFiles = ref([])

// Navigation entre étapes
const nextStep = () => {
  // Validation étape 1
  if (currentStep.value === 1) {
    if (!form.value.nom || !form.value.prenom || !form.value.date_naissance || !form.value.genre || !form.value.email || !form.value.password || !form.value.pays_naissance_id) {
      $swal.fire({
        icon: 'error',
        title: 'Champs manquants',
        text: 'Veuillez remplir tous les champs obligatoires'
      })
      return
    }
    
    // Vérifier qu'une ville est renseignée (soit sélectionnée, soit custom)
    if (!form.value.lieu_naissance_id && !form.value.ville_naissance_custom) {
      $swal.fire({
        icon: 'error',
        title: 'Champs manquants',
        text: 'Veuillez sélectionner ou saisir votre ville de naissance'
      })
      return
    }
    
    if (form.value.password !== form.value.password_confirmation) {
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Les mots de passe ne correspondent pas'
      })
      return
    }
  }
  
  currentStep.value++
}

const previousStep = () => {
  currentStep.value--
}

// Gestion des fichiers
const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)
  addFiles(files)
}

const handleFileDrop = (event) => {
  isDragging.value = false
  const files = Array.from(event.dataTransfer.files)
  addFiles(files)
}

const addFiles = (files) => {
  files.forEach(file => {
    if (file.size > 10 * 1024 * 1024) {
      $swal.fire({
        icon: 'error',
        title: 'Fichier trop volumineux',
        text: `Le fichier "${file.name}" dépasse 10 Mo`
      })
      return
    }
    uploadedFiles.value.push({
      file: file,
      name: file.name,
      size: file.size,
      type: ''
    })
  })
}

const removeFile = (index) => {
  uploadedFiles.value.splice(index, 1)
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'Ko', 'Mo', 'Go']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

// Méthodes helper pour le récapitulatif
const formatDateFr = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return date.toLocaleDateString('fr-FR', options)
}

const getLieuNaissanceLabel = () => {
  if (form.value.ville_naissance_custom) {
    return form.value.ville_naissance_custom
  }
  if (form.value.lieu_naissance_id) {
    const ville = villes.value.find(v => v.id == form.value.lieu_naissance_id)
    return ville ? ville.nom : 'Non spécifié'
  }
  return 'Non spécifié'
}

const getPaysNaissanceLabel = () => {
  if (!form.value.pays_naissance_id) return 'Non spécifié'
  const pays = paysListe.value.find(p => p.id == form.value.pays_naissance_id)
  return pays ? pays.nom : 'Non spécifié'
}

const getDocumentIcon = (type) => {
  const icons = {
    cv: '📄',
    diplome: '🎓',
    attestation: '📜',
    lettre: '✉️',
    casier: '⚖️',
    medical: '🏥',
    autre: '📎'
  }
  return icons[type] || '📄'
}

const getDocumentTypeLabel = (type) => {
  const labels = {
    cv: 'CV',
    diplome: 'Diplôme',
    attestation: 'Attestation',
    lettre: 'Lettre',
    casier: 'Casier judiciaire',
    medical: 'Certificat médical',
    autre: 'Autre'
  }
  return labels[type] || 'Non classé'
}

// Soumission du formulaire
const submitForm = async () => {
  if (!acceptTerms.value) {
    $swal.fire({
      icon: 'warning',
      title: 'Conditions non acceptées',
      text: 'Veuillez accepter les conditions d\'utilisation'
    })
    return
  }

  loading.value = true

  try {
    // 1. Inscription du candidat
    const response = await $api.post('/candidats/register', form.value)

    if (response.success) {
      const token = response.token
      const candidatId = response.candidat.id

      // Stocker le token
      localStorage.setItem('candidat_token', token)

      // 2. Upload des documents (si présents)
      if (uploadedFiles.value.length > 0) {
        for (const fileData of uploadedFiles.value) {
          const formData = new FormData()
          formData.append('fichier', fileData.file)
          formData.append('type_document', fileData.type || 'autre')
          
          await $api.post('/candidats/me/documents', formData, {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'multipart/form-data'
            }
          })
        }
      }

      // 3. Message de succès
      await $swal.fire({
        icon: 'success',
        title: 'Candidature envoyée !',
        html: `
          <p class="mb-4">Votre candidature a été enregistrée avec succès.</p>
          <p class="text-sm text-gray-600">Vous recevrez un email dès qu'un administrateur aura validé votre dossier.</p>
        `,
        confirmButtonColor: '#16a34a',
        confirmButtonText: 'Accéder à mon espace'
      })

      // 4. Redirection vers le dashboard candidat
      router.push('/candidat/dashboard')
    }
  } catch (error) {
    console.error('Erreur lors de la soumission:', error)
    
    let errorMessage = 'Une erreur est survenue lors de l\'envoi de votre candidature'
    
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      errorMessage = errors.join('<br>')
    } else if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }

    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      html: errorMessage
    })
  } finally {
    loading.value = false
  }
}

// SEO
useHead({
  title: 'Candidature - Gestion Dignitaires',
  meta: [
    { name: 'description', content: 'Formulaire de candidature pour devenir dignitaire de la République Gabonaise' }
  ]
})
</script>

<style scoped>
/* Animation smooth pour les transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}
.fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
