<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gabon-green-50 flex items-center justify-center px-4 py-12">
    <!-- Background decoratif -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-20 right-0 w-96 h-96 bg-gabon-green-500/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 left-0 w-96 h-96 bg-gabon-blue-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
      <!-- Logo et titre -->
      <div class="text-center mb-8">
        <NuxtLink to="/accueil" class="inline-flex items-center justify-center gap-3 mb-6 hover:opacity-80 transition-opacity">
          <div class="bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-xl p-3 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
        </NuxtLink>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Connexion Candidat</h1>
        <p class="text-gray-600">Accédez à votre espace personnel</p>
      </div>

      <!-- Card de connexion -->
      <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl border border-gray-200/50 overflow-hidden">
        <!-- Header coloré -->
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-8 py-6">
          <h2 class="text-xl font-bold text-white">Connectez-vous</h2>
          <p class="text-gabon-green-100 text-sm mt-1">Suivez l'état de votre candidature</p>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="login" class="p-8 space-y-6">
          <!-- Email -->
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
              <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Adresse email
              </span>
            </label>
            <input 
              v-model="form.email" 
              type="email" 
              required 
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" 
              placeholder="votre.email@example.com"
            >
          </div>

          <!-- Mot de passe -->
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
              <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Mot de passe
              </span>
            </label>
            <input 
              v-model="form.password" 
              type="password" 
              required 
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" 
              placeholder="Votre mot de passe"
            >
          </div>

          <!-- Se souvenir -->
          <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" class="w-4 h-4 border-2 border-gray-300 rounded text-gabon-green-600 focus:ring-2 focus:ring-gabon-green-500">
              <span class="text-gray-700">Se souvenir de moi</span>
            </label>
            <NuxtLink to="/candidature/forgot-password" class="text-gabon-green-700 font-semibold hover:underline">
              Mot de passe oublié ?
            </NuxtLink>
          </div>

          <!-- Bouton de connexion -->
          <button 
            type="submit" 
            :disabled="loading"
            :class="{ 'opacity-50 cursor-not-allowed': loading }"
            class="w-full px-6 py-4 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-bold rounded-xl shadow-lg shadow-gabon-green-600/30 hover:shadow-xl hover:shadow-gabon-green-600/40 transition-all duration-300 flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-else>Se connecter</span>
          </button>

          <!-- Divider -->
          <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-4 bg-white text-gray-500">ou</span>
            </div>
          </div>

          <!-- Lien inscription -->
          <div class="text-center">
            <p class="text-gray-600 mb-3">Vous n'avez pas encore de compte ?</p>
            <NuxtLink to="/candidature" class="inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:border-gabon-green-600 transition-all duration-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
              </svg>
              Créer un compte
            </NuxtLink>
          </div>
        </form>
      </div>

      <!-- Retour accueil -->
      <div class="text-center mt-6">
        <NuxtLink to="/accueil" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Retour à l'accueil
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const { $api, $swal } = useNuxtApp()

const loading = ref(false)
const form = ref({
  email: '',
  password: ''
})

const login = async () => {
  loading.value = true

  try {
    const response = await $api.post('/candidats/login', form.value)

    if (response.otp_required) {
      router.push(`/candidature/verify-otp?email=${encodeURIComponent(response.email)}&purpose=connexion`)
    } else if (response.success) {
      // Stocker le token
      localStorage.setItem('candidat_token', response.token)

      // Message de bienvenue
      $swal.fire({
        icon: 'success',
        title: 'Connexion réussie !',
        text: `Bienvenue ${response.candidat.nom_complet}`,
        timer: 2000,
        showConfirmButton: false
      })

      // Redirection vers le dashboard
      setTimeout(() => {
        router.push('/candidat/dashboard')
      }, 2000)
    }
  } catch (error) {
    console.error('Erreur de connexion:', error)
    
    let errorMessage = 'Identifiants incorrects'
    
    if (error.response?.status === 403) {
      errorMessage = error.response.data.message || 'Votre candidature a été refusée'
    } else if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }

    $swal.fire({
      icon: 'error',
      title: 'Erreur de connexion',
      text: errorMessage
    })
  } finally {
    loading.value = false
  }
}

// SEO
useHead({
  title: 'Connexion - Gestion Dignitaires',
  meta: [
    { name: 'description', content: 'Connectez-vous à votre espace candidat pour suivre votre candidature' }
  ]
})
</script>
