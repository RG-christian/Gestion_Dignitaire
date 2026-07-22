<template>
  <div class="min-h-screen flex">
    <!-- Partie gauche - Formulaire -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
      <div class="w-full max-w-md">
        <!-- Drapeau gabonais en haut -->
        <div class="flex h-3 mb-8 rounded-lg overflow-hidden shadow-lg">
          <div class="flex-1" style="background-color: #16a34a;"></div>
          <div class="flex-1" style="background-color: #eab308;"></div>
          <div class="flex-1" style="background-color: #2563eb;"></div>
        </div>

        <!-- Logo et titre -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-4 shadow-xl" style="background: linear-gradient(to bottom right, #16a34a, #2563eb);">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-gray-800 mb-2">République Gabonaise</h1>
          <h2 class="text-xl font-semibold mb-1" style="color: #15803d;">Gestion des Dignitaires</h2>
          <p class="text-gray-600 text-sm">Espace d'administration sécurisé</p>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Nom d'utilisateur -->
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
              Nom d'utilisateur
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                <svg class="h-5 w-5" style="color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <input
                v-model="credentials.username"
                type="text"
                required
                class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 transition-all bg-white"
                style="focus:ring-color: #16a34a; focus:border-color: transparent;"
                placeholder="Entrez votre nom d'utilisateur"
              />
            </div>
          </div>

          <!-- Mot de passe -->
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
              Mot de passe
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                <svg class="h-5 w-5" style="color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </div>
              <input
                v-model="credentials.password"
                :type="showPassword ? 'text' : 'password'"
                required
                class="w-full pl-12 pr-12 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 transition-all bg-white"
                style="focus:ring-color: #16a34a; focus:border-color: transparent;"
                placeholder="Entrez votre mot de passe"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-4 flex items-center transition-colors z-10"
                style="color: #9ca3af;"
              >
                <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
            <div class="text-right mt-2">
              <NuxtLink to="/forgot-password" class="text-sm font-semibold hover:underline" style="color: #16a34a;">
                Mot de passe oublié ?
              </NuxtLink>
            </div>
          </div>

          <!-- Bouton de connexion -->
          <button
            type="submit"
            :disabled="loading"
            style="background: linear-gradient(to right, #16a34a, #15803d);"
            class="w-full text-white py-4 rounded-lg font-bold text-lg transition-all duration-300 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center group relative overflow-hidden"
          >
            <!-- Effet hover animé -->
            <span class="absolute inset-0 bg-gradient-to-r from-green-700 to-green-800 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            
            <!-- Contenu du bouton -->
            <span class="relative z-10 flex items-center gap-2">
              <svg v-if="loading" class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <template v-else>
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span>Se connecter</span>
              </template>
              <span v-if="loading">Connexion en cours...</span>
            </span>
          </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200">
          <div class="flex items-center justify-center gap-2 text-sm text-gray-600">
            <svg class="w-4 h-4" style="color: #16a34a;" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
            <p class="font-medium">Connexion sécurisée SSL</p>
          </div>
          <p class="text-center text-xs text-gray-500 mt-3">© 2025 République Gabonaise - Tous droits réservés</p>
        </div>
      </div>
    </div>

    <!-- Partie droite - Image cover -->
    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden">
      <!-- Image de fond -->
      <div class="absolute inset-0" style="background: linear-gradient(to bottom right, #16a34a, #eab308, #2563eb);">
        <!-- Overlay avec motif -->
        <div class="absolute inset-0 opacity-20">
          <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
          <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        
        <!-- Contenu -->
        <div class="relative h-full flex flex-col items-center justify-center p-12 text-white">
          <!-- Armoiries ou logo du Gabon -->
          <div class="mb-8">
            <svg class="w-32 h-32 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
            </svg>
          </div>

          <!-- Texte -->
          <h2 class="text-4xl font-bold text-center mb-4">Bienvenue</h2>
          <p class="text-xl text-center mb-8 opacity-90">Système de Gestion des Dignitaires</p>
          
          <!-- Citations ou valeurs -->
          <div class="max-w-md text-center space-y-4">
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-6 border border-white border-opacity-20">
              <p class="text-lg italic">"Union - Travail - Justice"</p>
              <p class="text-sm mt-2 opacity-75">Devise de la République Gabonaise</p>
            </div>
          </div>

          <!-- Drapeau stylisé en bas -->
          <div class="absolute bottom-0 left-0 right-0 h-4 flex">
            <div class="flex-1" style="background-color: #16a34a;"></div>
            <div class="flex-1" style="background-color: #eab308;"></div>
            <div class="flex-1" style="background-color: #2563eb;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Swal from 'sweetalert2'

definePageMeta({
  layout: false
})

const authStore = useAuthStore()
const router = useRouter()

const credentials = reactive({
  username: '',
  password: ''
})

const loading = ref(false)
const showPassword = ref(false)

async function handleLogin() {
  loading.value = true

  try {
    const result = await authStore.login(credentials)

    if (result.otpRequired) {
      const route = useRoute()
      const redirectTo = (route.query.redirect as string) || '/dashboard'
      router.push(`/verify-otp?email=${encodeURIComponent(result.email)}&redirect=${encodeURIComponent(redirectTo)}`)
    } else if (result.success) {
      // Récupérer l'URL de redirection depuis les query params
      const route = useRoute()
      const redirectTo = (route.query.redirect as string) || '/dashboard'

      // SweetAlert succès avec redirection immédiate
      Swal.fire({
        icon: 'success',
        title: 'Connexion réussie !',
        text: 'Redirection en cours...',
        confirmButtonColor: '#16a34a',
        timer: 1000,
        timerProgressBar: true,
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
          // Redirection vers la page d'origine ou le dashboard
          setTimeout(() => {
            router.push(redirectTo)
          }, 500)
        }
      })
    } else {
      // SweetAlert erreur
      await Swal.fire({
        icon: 'error',
        title: 'Échec de connexion',
        text: 'Identifiants invalides. Veuillez réessayer.',
        confirmButtonColor: '#16a34a',
        confirmButtonText: 'Réessayer'
      })
    }
  } catch (e: any) {
    // SweetAlert erreur serveur
    await Swal.fire({
      icon: 'error',
      title: 'Erreur de connexion',
      text: e.message || 'Une erreur est survenue. Veuillez réessayer.',
      confirmButtonColor: '#16a34a',
      confirmButtonText: 'OK'
    })
  } finally {
    loading.value = false
  }
}

// Rediriger si déjà connecté
onMounted(async () => {
  if (!authStore.token && process.client) {
    const token = localStorage.getItem('auth_token')
    if (token) {
      await authStore.loadFromStorage()
    }
  }
  
  if (authStore.isAuthenticated) {
    router.push('/dashboard')
  }
})
</script>
