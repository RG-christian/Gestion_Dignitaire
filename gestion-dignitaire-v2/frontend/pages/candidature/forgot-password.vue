<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gabon-green-50 flex items-center justify-center px-4 py-12">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-20 right-0 w-96 h-96 bg-gabon-green-500/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 left-0 w-96 h-96 bg-gabon-blue-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
      <div class="text-center mb-8">
        <NuxtLink to="/accueil" class="inline-flex items-center justify-center gap-3 mb-6 hover:opacity-80 transition-opacity">
          <div class="bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-xl p-3 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
          </div>
        </NuxtLink>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mot de passe oublié</h1>
        <p class="text-gray-600">Recevez un lien pour réinitialiser votre mot de passe</p>
      </div>

      <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl border border-gray-200/50 overflow-hidden">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-8 py-6">
          <h2 class="text-xl font-bold text-white">Réinitialisation</h2>
          <p class="text-gabon-green-100 text-sm mt-1">Entrez l'email de votre candidature</p>
        </div>

        <div v-if="!sent" class="p-8">
          <form @submit.prevent="handleSubmit" class="space-y-6">
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
                v-model="email"
                type="email"
                required
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all"
                placeholder="votre.email@example.com"
              >
            </div>

            <button
              type="submit"
              :disabled="loading"
              :class="{ 'opacity-50 cursor-not-allowed': loading }"
              class="w-full px-6 py-4 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-bold rounded-xl shadow-lg transition-all duration-300"
            >
              {{ loading ? 'Envoi en cours...' : 'Envoyer le lien' }}
            </button>
          </form>
        </div>

        <div v-else class="p-8 text-center space-y-4">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <p class="text-gray-700">Si cet email est associé à une candidature, un lien de réinitialisation vient de vous être envoyé.</p>
        </div>
      </div>

      <div class="text-center mt-6">
        <NuxtLink to="/candidature/login" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Retour à la connexion
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
const { $api } = useNuxtApp()

const email = ref('')
const loading = ref(false)
const sent = ref(false)

async function handleSubmit() {
  loading.value = true
  try {
    await $api.post('/candidats/forgot-password', { email: email.value })
    sent.value = true
  } catch (error) {
    console.error('Erreur:', error)
    sent.value = true
  } finally {
    loading.value = false
  }
}

useHead({
  title: 'Mot de passe oublié - Gestion Dignitaires'
})
</script>
