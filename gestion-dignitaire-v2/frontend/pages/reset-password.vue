<template>
  <div class="min-h-screen flex">
    <!-- Partie gauche - Formulaire -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
      <div class="w-full max-w-md">
        <div class="flex h-3 mb-8 rounded-lg overflow-hidden shadow-lg">
          <div class="flex-1" style="background-color: #16a34a;"></div>
          <div class="flex-1" style="background-color: #eab308;"></div>
          <div class="flex-1" style="background-color: #2563eb;"></div>
        </div>

        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-4 shadow-xl" style="background: linear-gradient(to bottom right, #16a34a, #2563eb);">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-800 mb-2">Nouveau mot de passe</h1>
          <p class="text-gray-600 text-sm">Choisissez un nouveau mot de passe pour votre compte</p>
        </div>

        <div v-if="!tokenPresent" class="text-center space-y-4">
          <p class="text-red-600">Lien de réinitialisation invalide. Merci de refaire une demande.</p>
          <NuxtLink to="/forgot-password" class="inline-block font-semibold" style="color: #16a34a;">Faire une nouvelle demande</NuxtLink>
        </div>

        <form v-else @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nouveau mot de passe</label>
            <input
              v-model="password"
              type="password"
              required
              minlength="8"
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 transition-all bg-white"
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
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 transition-all bg-white"
              placeholder="Confirmez votre mot de passe"
            >
          </div>

          <button
            type="submit"
            :disabled="loading"
            style="background: linear-gradient(to right, #16a34a, #15803d);"
            class="w-full text-white py-4 rounded-lg font-bold text-lg transition-all duration-300 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Enregistrement...' : 'Réinitialiser le mot de passe' }}
          </button>
        </form>

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
          <h2 class="text-4xl font-bold text-center mb-4">Presque terminé</h2>
          <p class="text-xl text-center opacity-90">Choisissez un mot de passe fort et unique</p>
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
import Swal from 'sweetalert2'

definePageMeta({
  layout: false
})

const config = useRuntimeConfig()
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
    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Les mots de passe ne correspondent pas' })
    return
  }

  loading.value = true
  try {
    await $fetch(`${config.public.apiBase}/reset-password`, {
      method: 'POST',
      body: {
        email: email.value,
        token: token.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value
      }
    })

    await Swal.fire({
      icon: 'success',
      title: 'Mot de passe réinitialisé',
      text: 'Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.',
      confirmButtonColor: '#16a34a'
    })
    router.push('/login')
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Ce lien de réinitialisation est invalide ou a expiré.',
      confirmButtonColor: '#16a34a'
    })
  } finally {
    loading.value = false
  }
}
</script>
