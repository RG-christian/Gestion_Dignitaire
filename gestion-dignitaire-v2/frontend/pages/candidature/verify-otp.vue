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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </NuxtLink>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          {{ purpose === 'inscription' ? 'Vérifiez votre email' : 'Vérification en deux étapes' }}
        </h1>
        <p class="text-gray-600">
          Un code à 6 chiffres a été envoyé à<br><strong>{{ email }}</strong>
        </p>
      </div>

      <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl border border-gray-200/50 overflow-hidden">
        <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 px-8 py-6">
          <h2 class="text-xl font-bold text-white">Code de vérification</h2>
        </div>

        <div class="p-8">
          <form @submit.prevent="handleVerify" class="space-y-6">
            <div>
              <input
                v-model="code"
                type="text"
                inputmode="numeric"
                maxlength="6"
                required
                autofocus
                :disabled="loading || uploading"
                class="w-full text-center tracking-[0.5em] text-2xl font-bold px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all disabled:opacity-60"
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
              :disabled="loading || uploading || code.length !== 6"
              :class="{ 'opacity-50 cursor-not-allowed': loading || uploading }"
              class="w-full px-6 py-4 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-bold rounded-xl shadow-lg transition-all duration-300 flex items-center justify-center gap-2"
            >
              <svg v-if="loading || uploading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ uploading ? 'Envoi des documents...' : loading ? 'Vérification...' : 'Valider le code' }}</span>
            </button>
          </form>

          <div class="mt-6 text-center">
            <button @click="handleResend" :disabled="resending" class="text-sm font-semibold text-gabon-green-700 hover:underline disabled:opacity-50">
              {{ resending ? 'Envoi en cours...' : 'Renvoyer le code' }}
            </button>
          </div>
        </div>
      </div>

      <div class="text-center mt-6">
        <NuxtLink :to="purpose === 'inscription' ? '/candidature' : '/candidature/login'" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Retour
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
const { $api, $swal } = useNuxtApp()
const route = useRoute()
const router = useRouter()
const draftStore = useCandidatureDraftStore()

const email = ref(route.query.email || '')
const purpose = ref(route.query.purpose === 'connexion' ? 'connexion' : 'inscription')
const code = ref('')
const loading = ref(false)
const uploading = ref(false)
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
    const response = await $api.post('/candidats/verify-otp', {
      email: email.value,
      code: code.value,
      purpose: purpose.value
    })

    localStorage.setItem('candidat_token', response.token)

    // Après une inscription, uploader les documents mis en attente avant
    // la vérification (cf. candidature/index.vue)
    if (purpose.value === 'inscription' && draftStore.pendingFiles.length > 0) {
      uploading.value = true
      for (const fileData of draftStore.pendingFiles) {
        const formData = new FormData()
        formData.append('fichier', fileData.file)
        formData.append('type_document', fileData.type || 'autre')

        await $api.post('/candidats/me/documents', formData, {
          headers: {
            Authorization: `Bearer ${response.token}`,
            'Content-Type': 'multipart/form-data'
          }
        })
      }
      draftStore.clear()
      uploading.value = false
    }

    await $swal.fire({
      icon: 'success',
      title: purpose.value === 'inscription' ? 'Email vérifié !' : 'Connexion réussie !',
      text: purpose.value === 'inscription'
        ? 'Votre candidature a été enregistrée avec succès. Vous recevrez un email dès qu\'un administrateur aura validé votre dossier.'
        : `Bienvenue ${response.candidat.nom_complet}`,
      confirmButtonColor: '#16a34a',
      confirmButtonText: 'Accéder à mon espace'
    })

    clearInterval(countdownInterval)
    router.push('/candidat/dashboard')
  } catch (error) {
    console.error('Erreur vérification OTP:', error)
    code.value = ''
    $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || error.response?.data?.message || 'Code invalide ou expiré.'
    })
  } finally {
    loading.value = false
    uploading.value = false
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
    await $api.post('/candidats/resend-otp', { email: email.value, purpose: purpose.value })
    code.value = ''
    startCountdown()
    $swal.fire({ icon: 'success', title: 'Code renvoyé', timer: 1500, showConfirmButton: false })
  } catch (error) {
    console.error('Erreur renvoi code:', error)
  } finally {
    resending.value = false
  }
}

onMounted(() => {
  if (!email.value) {
    router.push('/candidature/login')
    return
  }
  startCountdown()
})

onUnmounted(() => {
  clearInterval(countdownInterval)
})

useHead({
  title: 'Vérification - Gestion Dignitaires'
})
</script>
