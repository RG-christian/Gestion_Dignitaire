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
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Nouveau mot de passe</h1>
        <p class="text-gray-600">Choisissez un nouveau mot de passe pour votre compte</p>
      </div>

      <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl border border-gray-200/50 overflow-hidden">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-8 py-6">
          <h2 class="text-xl font-bold text-white">Réinitialisation</h2>
        </div>

        <div class="p-8">
          <div v-if="!tokenPresent" class="text-center space-y-4">
            <p class="text-red-600">Lien de réinitialisation invalide. Merci de refaire une demande.</p>
            <NuxtLink to="/candidature/forgot-password" class="inline-block font-semibold text-gabon-green-700 hover:underline">Faire une nouvelle demande</NuxtLink>
          </div>

          <form v-else @submit.prevent="handleSubmit" class="space-y-6">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Nouveau mot de passe</label>
              <input
                v-model="password"
                type="password"
                required
                minlength="8"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all"
                placeholder="8 caractères minimum"
              >
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Confirmer le mot de passe</label>
              <input
                v-model="passwordConfirmation"
                type="password"
                required
                minlength="8"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all"
                placeholder="Confirmez votre mot de passe"
              >
            </div>

            <button
              type="submit"
              :disabled="loading"
              :class="{ 'opacity-50 cursor-not-allowed': loading }"
              class="w-full px-6 py-4 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-bold rounded-xl shadow-lg transition-all duration-300"
            >
              {{ loading ? 'Enregistrement...' : 'Réinitialiser le mot de passe' }}
            </button>
          </form>
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
const { $api, $swal } = useNuxtApp()
const route = useRoute()
const router = useRouter()

const token = ref(route.query.token || '')
const email = ref(route.query.email || '')
const tokenPresent = computed(() => !!token.value && !!email.value)

const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)

async function handleSubmit() {
  if (password.value !== passwordConfirmation.value) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: 'Les mots de passe ne correspondent pas' })
    return
  }

  loading.value = true
  try {
    await $api.post('/candidats/reset-password', {
      email: email.value,
      token: token.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })

    await $swal.fire({
      icon: 'success',
      title: 'Mot de passe réinitialisé',
      text: 'Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.',
      confirmButtonColor: '#16a34a'
    })
    router.push('/candidature/login')
  } catch (error) {
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || error.response?.data?.message || 'Ce lien de réinitialisation est invalide ou a expiré.',
      confirmButtonColor: '#16a34a'
    })
  } finally {
    loading.value = false
  }
}

useHead({
  title: 'Réinitialiser mon mot de passe - Gestion Dignitaires'
})
</script>
