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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-800 mb-2">Mot de passe oublié</h1>
          <p class="text-gray-600 text-sm">Entrez votre email pour recevoir un lien de réinitialisation</p>
        </div>

        <div v-if="!sent" class="space-y-6">
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Adresse email</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                  <svg class="h-5 w-5" style="color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <input
                  v-model="email"
                  type="email"
                  required
                  class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 transition-all bg-white"
                  placeholder="votre.email@example.com"
                >
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              style="background: linear-gradient(to right, #16a34a, #15803d);"
              class="w-full text-white py-4 rounded-lg font-bold text-lg transition-all duration-300 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ loading ? 'Envoi en cours...' : 'Envoyer le lien de réinitialisation' }}
            </button>
          </form>
        </div>

        <div v-else class="text-center space-y-4">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <p class="text-gray-700">Si cet email existe, un lien de réinitialisation vient de vous être envoyé. Vérifiez votre boîte de réception.</p>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 text-center">
          <NuxtLink to="/login" class="text-sm font-semibold" style="color: #16a34a;">← Retour à la connexion</NuxtLink>
        </div>
      </div>
    </div>

    <!-- Partie droite -->
    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden">
      <div class="absolute inset-0" style="background: linear-gradient(to bottom right, #16a34a, #eab308, #2563eb);">
        <div class="absolute inset-0 opacity-20">
          <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
          <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="relative h-full flex flex-col items-center justify-center p-12 text-white">
          <h2 class="text-4xl font-bold text-center mb-4">Sécurité du compte</h2>
          <p class="text-xl text-center opacity-90">Réinitialisez votre mot de passe en toute sécurité</p>
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

<script setup>
definePageMeta({
  layout: false
})

const config = useRuntimeConfig()
const email = ref('')
const loading = ref(false)
const sent = ref(false)

async function handleSubmit() {
  loading.value = true
  try {
    await $fetch(`${config.public.apiBase}/forgot-password`, {
      method: 'POST',
      body: { email: email.value }
    })
    sent.value = true
  } catch (error) {
    console.error('Erreur:', error)
    sent.value = true // Message générique quoi qu'il arrive (ne pas révéler si l'email existe)
  } finally {
    loading.value = false
  }
}
</script>
