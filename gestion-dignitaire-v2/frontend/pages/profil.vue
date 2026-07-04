<template>
  <DashboardLayout>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">

      <!-- Container principal -->
      <div class="max-w-6xl mx-auto">
        <!-- En-tête avec breadcrumb -->
        <div class="mb-8">
          <nav class="flex items-center text-sm text-gray-600 mb-4">
            <NuxtLink to="/dashboard" class="hover:text-gabon-green-600 transition">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
              Accueil
            </NuxtLink>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 font-medium">Mon Profil</span>
          </nav>
          <h1 class="text-4xl font-bold text-gray-900">Paramètres du compte</h1>
          <p class="text-gray-600 mt-2">Gérez vos informations personnelles et préférences de sécurité</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Sidebar gauche - Carte profil -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-4">
              <!-- Header gradient -->
              <div class="h-32 bg-gradient-to-br from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 relative">
                <div class="absolute inset-0 bg-black/10"></div>
              </div>
              
              <!-- Photo de profil centrée -->
              <div class="relative px-6 pb-6">
                <div class="flex flex-col items-center -mt-20">
                  <div class="relative group">
                    <!-- Photo ou initiale -->
                    <div v-if="photoPreview || form.photo" class="w-40 h-40 rounded-full border-4 border-white shadow-2xl overflow-hidden bg-white">
                      <img 
                        :src="photoPreview || (form.photo ? `http://127.0.0.1:8000/storage/photos/${form.photo}` : '')" 
                        alt="Photo de profil"
                        class="w-full h-full object-cover"
                        style="object-fit: cover;"
                      />
                    </div>
                    <div v-else class="w-40 h-40 rounded-full border-4 border-white shadow-2xl bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 flex items-center justify-center">
                      <span class="text-6xl font-bold text-white">
                        {{ (authStore.user?.nom_complet || 'A').charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    
                    <!-- Bouton upload photo -->
                    <label 
                      class="absolute bottom-2 right-2 bg-white hover:bg-gray-50 border-2 border-gabon-green-600 text-gabon-green-600 w-12 h-12 rounded-full shadow-lg cursor-pointer flex items-center justify-center transition-all group-hover:scale-110"
                      title="Changer la photo"
                    >
                      <input 
                        type="file" 
                        accept="image/*" 
                        class="hidden" 
                        @change="handlePhotoUpload"
                      />
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                      </svg>
                    </label>
                  </div>
                  
                  <!-- Informations utilisateur -->
                  <div class="text-center mt-4 w-full">
                    <h2 class="text-2xl font-bold text-gray-900">
                      {{ authStore.user?.nom_complet || 'Utilisateur' }}
                    </h2>
                    <p class="text-gray-600 mt-1 text-sm">
                      {{ authStore.user?.email || '' }}
                    </p>
                    <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-gabon-green-50 to-gabon-green-100 border border-gabon-green-200 rounded-full">
                      <div class="w-2 h-2 bg-gabon-green-600 rounded-full animate-pulse"></div>
                      <span class="text-sm font-semibold text-gabon-green-800">
                        {{ authStore.user?.role_name || 'Administrateur' }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Stats compactes -->
                <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 flex items-center gap-2">
                      <svg class="w-4 h-4 text-gabon-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Permissions
                    </span>
                    <span class="font-bold text-gray-900">{{ stats.permissions }}</span>
                  </div>
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 flex items-center gap-2">
                      <svg class="w-4 h-4 text-gabon-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      Membre depuis
                    </span>
                    <span class="font-bold text-gray-900">{{ formatDateShort(authStore.user?.created_at) }}</span>
                  </div>
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 flex items-center gap-2">
                      <svg class="w-4 h-4 text-gabon-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                      </svg>
                      Statut
                    </span>
                    <span class="font-bold text-gabon-green-600">Actif</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Contenu principal -->
          <div class="lg:col-span-2 space-y-6">

            <!-- Carte Informations personnelles -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gabon-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-gabon-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                  </div>
                  <h3 class="text-xl font-bold text-gray-900">Informations personnelles</h3>
                </div>
                <button
                  v-if="!editMode"
                  @click="editMode = true"
                  class="px-4 py-2 bg-gabon-green-50 hover:bg-gabon-green-100 text-gabon-green-700 rounded-lg transition flex items-center gap-2 font-medium text-sm"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                  </svg>
                  Modifier
                </button>
              </div>

              <form @submit.prevent="saveProfile">
                <div class="space-y-5">
                  <!-- Nom complet -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Nom complet <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                      </div>
                      <input
                        v-model="form.nom_complet"
                        :disabled="!editMode"
                        type="text"
                        required
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition disabled:bg-gray-50 disabled:text-gray-600 disabled:cursor-not-allowed"
                        placeholder="Votre nom complet"
                      >
                    </div>
                  </div>

                  <!-- Email -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Adresse email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                      </div>
                      <input
                        v-model="form.email"
                        :disabled="!editMode"
                        type="email"
                        required
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition disabled:bg-gray-50 disabled:text-gray-600 disabled:cursor-not-allowed"
                        placeholder="votre.email@exemple.com"
                      >
                    </div>
                  </div>

                  <!-- Téléphone -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Téléphone
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                      </div>
                      <input
                        v-model="form.telephone"
                        :disabled="!editMode"
                        type="tel"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition disabled:bg-gray-50 disabled:text-gray-600 disabled:cursor-not-allowed"
                        placeholder="+241 XX XX XX XX"
                      >
                    </div>
                  </div>

                  <!-- Rôle (lecture seule) -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Rôle
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                      </div>
                      <input
                        :value="authStore.user?.role_name || 'Administrateur'"
                        disabled
                        type="text"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                      >
                    </div>
                  </div>
                </div>

                <!-- Boutons d'action -->
                <div v-if="editMode" class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
                  <button
                    type="button"
                    @click="cancelEdit"
                    class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition"
                  >
                    Annuler
                  </button>
                  <button
                    type="submit"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
                  >
                    Enregistrer
                  </button>
                </div>
              </form>
            </div>

            <!-- Carte Sécurité -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gabon-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-gabon-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Sécurité du compte</h3>
                  <p class="text-sm text-gray-600">Modifier votre mot de passe</p>
                </div>
              </div>

              <form @submit.prevent="changePassword">
                <div class="space-y-5">
                  <!-- Mot de passe actuel -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Mot de passe actuel <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                      </div>
                      <input
                        v-model="passwordForm.current_password"
                        type="password"
                        required
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
                        placeholder="••••••••"
                      >
                    </div>
                  </div>

                  <!-- Nouveau mot de passe -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Nouveau mot de passe <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                      </div>
                      <input
                        v-model="passwordForm.new_password"
                        type="password"
                        required
                        minlength="8"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
                        placeholder="••••••••"
                      >
                    </div>
                    <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Minimum 8 caractères
                    </p>
                  </div>

                  <!-- Confirmation -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Confirmer le nouveau mot de passe <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <input
                        v-model="passwordForm.new_password_confirmation"
                        type="password"
                        required
                        minlength="8"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition"
                        placeholder="••••••••"
                      >
                    </div>
                  </div>
                </div>

                <!-- Bouton -->
                <div class="mt-6">
                  <button
                    type="submit"
                    class="w-full px-6 py-3 bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 hover:from-gabon-blue-700 hover:to-gabon-blue-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
                  >
                    Changer le mot de passe
                  </button>
                </div>
              </form>
            </div>
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
const api = useApi()
const editMode = ref(false)
const photoPreview = ref<string | null>(null)
const photoFile = ref<File | null>(null)

const form = reactive({
  nom_complet: authStore.user?.nom_complet || '',
  email: authStore.user?.email || '',
  telephone: authStore.user?.telephone || '',
  photo: authStore.user?.photo || ''
})

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const stats = reactive({
  permissions: authStore.user?.sousfonctions?.length || 0
})

function formatDateShort(dateString: string | null) {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('fr-FR', { 
    year: 'numeric', 
    month: 'short'
  })
}

function handlePhotoUpload(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (file) {
    // Vérifier le type de fichier
    if (!file.type.startsWith('image/')) {
      const { $swal } = useNuxtApp()
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Veuillez sélectionner une image valide'
      })
      return
    }
    
    // Vérifier la taille (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
      const { $swal } = useNuxtApp()
      $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'L\'image ne doit pas dépasser 2 MB'
      })
      return
    }
    
    photoFile.value = file
    
    // Prévisualisation
    const reader = new FileReader()
    reader.onload = (e) => {
      photoPreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
    
    // Upload automatique
    uploadPhoto()
  }
}

async function uploadPhoto() {
  if (!photoFile.value) return
  
  try {
    const formData = new FormData()
    formData.append('photo', photoFile.value)
    
    const response: any = await api.uploadPhoto(formData)
    
    // Mettre à jour le store ET localStorage
    if (authStore.user && response.photo) {
      authStore.user.photo = response.photo
      form.photo = response.photo
      
      // IMPORTANT : Sauvegarder dans localStorage
      if (process.client) {
        localStorage.setItem('user', JSON.stringify(authStore.user))
      }
    }
    
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: 'Photo de profil mise à jour avec succès',
      timer: 2000,
      showConfirmButton: false
    })
  } catch (error: any) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de l\'upload de la photo'
    })
    // Réinitialiser la prévisualisation
    photoPreview.value = null
    photoFile.value = null
  }
}

function cancelEdit() {
  editMode.value = false
  form.nom_complet = authStore.user?.nom_complet || ''
  form.email = authStore.user?.email || ''
  form.telephone = authStore.user?.telephone || ''
}

async function saveProfile() {
  try {
    const response: any = await api.updateProfile(form)

    // Mettre à jour le store
    if (authStore.user) {
      authStore.user.nom_complet = form.nom_complet
      authStore.user.email = form.email
      authStore.user.telephone = form.telephone
    }

    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: 'Profil mis à jour avec succès',
      timer: 2000,
      showConfirmButton: false
    })

    editMode.value = false
  } catch (error: any) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de la mise à jour du profil'
    })
  }
}

async function changePassword() {
  // Vérification des mots de passe
  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: 'Les mots de passe ne correspondent pas'
    })
    return
  }

  try {
    await api.updatePassword(passwordForm)

    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'success',
      title: 'Succès',
      text: 'Mot de passe changé avec succès',
      timer: 2000,
      showConfirmButton: false
    })

    // Réinitialiser le formulaire
    passwordForm.current_password = ''
    passwordForm.new_password = ''
    passwordForm.new_password_confirmation = ''
  } catch (error: any) {
    console.error('Erreur:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors du changement de mot de passe'
    })
  }
}
</script>
