<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2">
        <div class="flex items-center gap-3 mb-2">
          <svg class="w-8 h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <h1 class="text-3xl font-bold text-white drop-shadow-lg">Paramètres</h1>
        </div>
        <p class="text-white text-sm opacity-95 drop-shadow">Réglages de sécurité de la plateforme</p>
      </div>
    </header>

    <section class="max-w-3xl mx-auto px-2 pb-8">
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200 border-t-gabon-green-600"></div>
      </div>

      <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
          <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-gabon-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Double authentification (OTP)
          </h2>
          <p class="text-sm text-gray-500 mt-1">
            Désactivée par défaut. Une fois activée, un code de vérification par email est exigé à chaque connexion.
          </p>
        </div>

        <div class="p-6 space-y-6">
          <div class="flex items-center justify-between py-3 border-b border-gray-100">
            <div>
              <p class="font-semibold text-gray-800">OTP à la connexion — comptes administrateurs</p>
              <p class="text-sm text-gray-500">Concerne Assistant, Gestionnaire, Administrateur et Super Administrateur.</p>
            </div>
            <button
              type="button"
              @click="form.otp_login_admin_enabled = !form.otp_login_admin_enabled"
              :class="form.otp_login_admin_enabled ? 'bg-gabon-green-600' : 'bg-gray-300'"
              class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors flex-shrink-0"
            >
              <span :class="form.otp_login_admin_enabled ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform shadow"></span>
            </button>
          </div>

          <div class="flex items-center justify-between py-3">
            <div>
              <p class="font-semibold text-gray-800">OTP à la connexion — comptes candidats</p>
              <p class="text-sm text-gray-500">Concerne l'espace candidat (suivi de candidature).</p>
            </div>
            <button
              type="button"
              @click="form.otp_login_candidat_enabled = !form.otp_login_candidat_enabled"
              :class="form.otp_login_candidat_enabled ? 'bg-gabon-green-600' : 'bg-gray-300'"
              class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors flex-shrink-0"
            >
              <span :class="form.otp_login_candidat_enabled ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform shadow"></span>
            </button>
          </div>

          <p class="text-xs text-gray-400 bg-gray-50 rounded-lg p-3">
            La vérification d'email à l'inscription candidat (code envoyé après soumission du formulaire) reste toujours active, indépendamment de ces réglages.
          </p>

          <button
            @click="save"
            :disabled="saving"
            class="w-full bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 disabled:opacity-50"
          >
            {{ saving ? 'Enregistrement...' : 'Enregistrer les réglages' }}
          </button>
        </div>
      </div>
    </section>
    </div>
  </DashboardLayout>
</template>

<script setup>
definePageMeta({
  middleware: 'auth'
})

const config = useRuntimeConfig()
const authStore = useAuthStore()
const permissions = usePermissions()
const router = useRouter()

const loading = ref(true)
const saving = ref(false)

const form = reactive({
  otp_login_admin_enabled: false,
  otp_login_candidat_enabled: false
})

async function loadParametres() {
  loading.value = true
  try {
    const response = await $fetch(`${config.public.apiBase}/admin/parametres`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    form.otp_login_admin_enabled = response.otp_login_admin_enabled
    form.otp_login_candidat_enabled = response.otp_login_candidat_enabled
  } catch (error) {
    console.error('Erreur chargement paramètres:', error)
  } finally {
    loading.value = false
  }
}

async function save() {
  saving.value = true
  try {
    await $fetch(`${config.public.apiBase}/admin/parametres`, {
      method: 'PUT',
      body: form,
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    const { $swal } = useNuxtApp()
    $swal.fire({ icon: 'success', title: 'Réglages enregistrés', timer: 2000, showConfirmButton: false })
  } catch (error) {
    console.error('Erreur enregistrement paramètres:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || 'Erreur lors de l\'enregistrement' })
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  if (!permissions.estSuperAdmin.value) {
    router.push('/dashboard')
    return
  }
  loadParametres()
})
</script>
