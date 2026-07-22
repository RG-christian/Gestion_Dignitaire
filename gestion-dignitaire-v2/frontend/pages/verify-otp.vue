<template>
  <div class="min-h-screen flex">
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-800 mb-2">Vérification en deux étapes</h1>
          <p class="text-gray-600 text-sm">Un code à 6 chiffres a été envoyé à<br><strong>{{ email }}</strong></p>
        </div>

        <form @submit.prevent="handleVerify" class="space-y-6">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 text-center">Code de vérification</label>
            <input
              v-model="code"
              type="text"
              inputmode="numeric"
              maxlength="6"
              required
              autofocus
              :disabled="loading"
              class="w-full text-center tracking-[0.5em] text-2xl font-bold px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 transition-all bg-white disabled:opacity-60"
              placeholder="------"
            >
            <p v-if="secondsLeft > 0" class="text-center text-sm mt-2" :class="secondsLeft <= 60 ? 'text-red-600 font-semibold' : 'text-gray-500'">
              Code valable encore {{ formattedTimer }}
            </p>
            <p v-else class="text-center text-sm mt-2 text-red-600 font-semibold">
              Le code a expiré, cliquez sur « Renvoyer le code »
            </p>
          </div>

          <button
            type="submit"
            :disabled="loading || code.length !== 6"
            style="background: linear-gradient(to right, #16a34a, #15803d);"
            class="w-full text-white py-4 rounded-lg font-bold text-lg transition-all duration-300 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? 'Vérification...' : 'Valider le code' }}</span>
          </button>
        </form>

        <div class="mt-6 text-center">
          <button @click="handleResend" :disabled="resending" class="text-sm font-semibold hover:underline disabled:opacity-50" style="color: #16a34a;">
            {{ resending ? 'Envoi en cours...' : 'Renvoyer le code' }}
          </button>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 text-center">
          <NuxtLink to="/login" class="text-sm font-semibold text-gray-500 hover:text-gray-700">← Retour à la connexion</NuxtLink>
        </div>
      </div>
    </div>

    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden">
      <div class="absolute inset-0" style="background: linear-gradient(to bottom right, #16a34a, #eab308, #2563eb);">
        <div class="absolute inset-0 opacity-20">
          <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
          <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="relative h-full flex flex-col items-center justify-center p-12 text-white">
          <h2 class="text-4xl font-bold text-center mb-4">Compte sécurisé</h2>
          <p class="text-xl text-center opacity-90">La double authentification protège votre accès administrateur</p>
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
const authStore = useAuthStore()

const email = ref(route.query.email || '')
const code = ref('')
const loading = ref(false)
const resending = ref(false)

// Compte à rebours aligné sur l'expiration du code (5 min, cf. OtpService)
const EXPIRATION_SECONDS = 5 * 60
const secondsLeft = ref(EXPIRATION_SECONDS)
let countdownInterval = null

const formattedTimer = computed(() => {
  const m = Math.floor(secondsLeft.value / 60)
  const s = secondsLeft.value % 60
  return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
})

function startCountdown() {
  clearInterval(countdownInterval)
  secondsLeft.value = EXPIRATION_SECONDS
  countdownInterval = setInterval(() => {
    if (secondsLeft.value > 0) {
      secondsLeft.value--
    } else {
      clearInterval(countdownInterval)
    }
  }, 1000)
}

async function handleVerify() {
  loading.value = true
  try {
    const response = await $fetch(`${config.public.apiBase}/verify-otp`, {
      method: 'POST',
      body: { email: email.value, code: code.value }
    })

    clearInterval(countdownInterval)
    authStore.setSession(response.token, response.user, response.expires_at)

    const redirectTo = route.query.redirect || '/dashboard'
    Swal.fire({
      icon: 'success',
      title: 'Connexion réussie !',
      timer: 1000,
      showConfirmButton: false
    })
    setTimeout(() => router.push(redirectTo), 500)
  } catch (error) {
    code.value = ''
    Swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Code invalide ou expiré.',
      confirmButtonColor: '#16a34a'
    })
  } finally {
    loading.value = false
  }
}

// Validation automatique dès que les 6 chiffres sont saisis
watch(code, (value) => {
  if (value.length === 6 && !loading.value) {
    handleVerify()
  }
})

async function handleResend() {
  resending.value = true
  try {
    await $fetch(`${config.public.apiBase}/resend-otp`, {
      method: 'POST',
      body: { email: email.value }
    })
    code.value = ''
    startCountdown()
    Swal.fire({ icon: 'success', title: 'Code renvoyé', timer: 1500, showConfirmButton: false })
  } catch (error) {
    console.error('Erreur renvoi code:', error)
  } finally {
    resending.value = false
  }
}

onMounted(() => {
  if (!email.value) {
    router.push('/login')
    return
  }
  startCountdown()
})

onUnmounted(() => {
  clearInterval(countdownInterval)
})
</script>
