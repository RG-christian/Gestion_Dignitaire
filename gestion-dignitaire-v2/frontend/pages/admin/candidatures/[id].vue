<template>
  <DashboardLayout>
    <div style="zoom: 0.8;">
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 shadow-lg p-6 mb-6">
      <div class="max-w-full mx-auto px-2 flex items-center justify-between">
        <div>
          <NuxtLink to="/admin/candidatures" class="text-white text-sm opacity-90 hover:opacity-100 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour à la liste
          </NuxtLink>
          <h1 v-if="candidat" class="text-3xl font-bold text-white drop-shadow-lg">{{ candidat.prenom }} {{ candidat.nom }}</h1>
        </div>
        <span v-if="candidat" class="px-3 py-1.5 rounded-full text-sm font-semibold" :class="statutBadgeClass(candidat.statut)">
          {{ statutLabel(candidat.statut) }}
        </span>
      </div>
    </header>

    <section v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
    </section>

    <section v-else-if="candidat" class="max-w-full mx-auto px-2 pb-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Colonne principale : profil, documents, diplômes, langues, expériences -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-4">Informations personnelles</h3>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">NIP :</span> {{ candidat.nip || '—' }}</div>
            <div><span class="text-gray-500">Matricule :</span> {{ candidat.matricule || 'Non renseigné' }}</div>
            <div><span class="text-gray-500">Date de naissance :</span> {{ formatDate(candidat.date_naissance) }}</div>
            <div><span class="text-gray-500">Genre :</span> {{ candidat.genre || '—' }}</div>
            <div><span class="text-gray-500">État civil :</span> {{ candidat.etat_civil || '—' }}</div>
            <div><span class="text-gray-500">Ville de résidence :</span> {{ candidat.ville_residence?.nom || '—' }}</div>
            <div><span class="text-gray-500">Email :</span> {{ candidat.email }}</div>
            <div><span class="text-gray-500">Téléphone :</span> {{ candidat.telephone || '—' }}</div>
            <div class="col-span-2"><span class="text-gray-500">Adresse :</span> {{ candidat.adresse || '—' }}</div>
          </div>
          <div v-if="candidat.statut === 'refuse' && candidat.motif_refus" class="mt-4 bg-red-50 border-l-4 border-red-500 rounded p-3 text-sm text-red-800">
            <strong>Motif du refus :</strong> {{ candidat.motif_refus }}
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-4">Documents ({{ candidat.documents?.length || 0 }})</h3>
          <div v-if="candidat.documents?.length" class="space-y-2">
            <a
              v-for="doc in candidat.documents"
              :key="doc.id"
              :href="`${config.public.apiBase}/admin/candidats/${candidat.id}/documents/${doc.id}/download`"
              target="_blank"
              class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 rounded-lg px-4 py-2 text-sm transition-colors"
              @click.prevent="downloadDocument(doc)"
            >
              <span>{{ doc.icone_type?.icon }} {{ doc.nom_fichier }} <span class="text-gray-400">({{ doc.type_document }})</span></span>
              <span class="text-gray-400 text-xs">{{ doc.taille_lisible }}</span>
            </a>
          </div>
          <p v-else class="text-sm text-gray-400">Aucun document joint.</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-4">Diplômes ({{ candidat.diplomes?.length || 0 }})</h3>
          <ul v-if="candidat.diplomes?.length" class="space-y-2">
            <li v-for="d in candidat.diplomes" :key="d.id" class="bg-gray-50 rounded-lg px-4 py-2 text-sm">
              <div class="font-semibold text-gray-800">{{ d.intitule }} <span class="text-gray-400 font-normal">— {{ d.annee }}</span></div>
              <div class="text-gray-500">{{ d.etablissement?.nom }} <span v-if="d.domaine">· {{ d.domaine.nom }}</span></div>
            </li>
          </ul>
          <p v-else class="text-sm text-gray-400">Aucun diplôme déclaré.</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-4">Langues ({{ candidat.langues?.length || 0 }})</h3>
          <div v-if="candidat.langues?.length" class="flex flex-wrap gap-2">
            <span v-for="l in candidat.langues" :key="l.id" class="bg-blue-50 text-blue-800 text-sm px-3 py-1 rounded-full">
              {{ l.langue?.nom }} <span v-if="l.niveau" class="text-blue-500">· {{ l.niveau }}</span>
            </span>
          </div>
          <p v-else class="text-sm text-gray-400">Aucune langue déclarée.</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-4">Expériences professionnelles ({{ candidat.experiences?.length || 0 }})</h3>
          <ul v-if="candidat.experiences?.length" class="space-y-2">
            <li v-for="e in candidat.experiences" :key="e.id" class="bg-gray-50 rounded-lg px-4 py-2 text-sm">
              <div class="font-semibold text-gray-800">{{ e.intitule }}</div>
              <div class="text-gray-500">{{ e.structure?.nom }} · {{ formatDate(e.date_debut) }} — {{ e.date_fin ? formatDate(e.date_fin) : 'en cours' }}</div>
            </li>
          </ul>
          <p v-else class="text-sm text-gray-400">Aucune expérience déclarée.</p>
        </div>
      </div>

      <!-- Colonne latérale : décision + recommandations -->
      <div class="space-y-6">
        <div v-if="candidat.statut === 'en_attente'" class="bg-white rounded-xl shadow-lg p-6 space-y-3">
          <h3 class="text-lg font-bold text-gray-800 mb-2">Décision</h3>
          <button
            @click="valider"
            :disabled="deciding"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-3 rounded-lg transition-colors disabled:opacity-50"
          >
            ✓ Valider la candidature
          </button>
          <div>
            <textarea
              v-model="motifRefus"
              rows="2"
              placeholder="Motif du refus (10 caractères minimum)..."
              class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent"
            ></textarea>
            <button
              @click="refuser"
              :disabled="deciding || motifRefus.trim().length < 10"
              class="w-full mt-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-3 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              ✕ Refuser la candidature
            </button>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-4">Recommandations</h3>
          <p class="text-xs text-gray-500 mb-3">
            Envoyées au candidat par notification et email, visibles sur son tableau de bord.
          </p>

          <div class="space-y-3 mb-4 max-h-96 overflow-y-auto">
            <div v-if="!candidat.messages?.length" class="text-sm text-gray-400">Aucun message pour l'instant.</div>
            <div
              v-for="m in candidat.messages"
              :key="m.id"
              class="rounded-lg p-3 text-sm"
              :class="messageStyle(m.type)"
            >
              <div class="flex items-center justify-between mb-1">
                <span class="font-semibold">{{ messageTypeLabel(m.type) }}</span>
                <span class="text-xs opacity-70">{{ formatDateTime(m.created_at) }}</span>
              </div>
              <p class="whitespace-pre-line">{{ m.contenu }}</p>
              <p v-if="m.user_label" class="text-xs opacity-60 mt-1">— {{ m.user_label }}</p>
            </div>
          </div>

          <textarea
            v-model="nouvelleRecommandation"
            rows="3"
            placeholder="Écrire une recommandation pour ce candidat..."
            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:border-transparent"
          ></textarea>
          <button
            @click="envoyerRecommandation"
            :disabled="sendingMessage || nouvelleRecommandation.trim().length < 3"
            class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2.5 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ sendingMessage ? 'Envoi…' : 'Envoyer la recommandation' }}
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

const route = useRoute()
const config = useRuntimeConfig()
const authStore = useAuthStore()

const candidat = ref(null)
const loading = ref(true)
const deciding = ref(false)
const sendingMessage = ref(false)
const motifRefus = ref('')
const nouvelleRecommandation = ref('')

function statutLabel(statut) {
  return { en_attente: 'En attente', valide: 'Validée', refuse: 'Refusée' }[statut] || statut
}

function statutBadgeClass(statut) {
  const classes = {
    en_attente: 'bg-yellow-100 text-yellow-800',
    valide: 'bg-green-100 text-green-700',
    refuse: 'bg-red-100 text-red-700'
  }
  return classes[statut] || 'bg-gray-100 text-gray-700'
}

function messageTypeLabel(type) {
  return { recommandation: 'Recommandation', validation: 'Validation', refus: 'Refus' }[type] || type
}

function messageStyle(type) {
  const styles = {
    recommandation: 'bg-blue-50 text-blue-900',
    validation: 'bg-green-50 text-green-900',
    refus: 'bg-red-50 text-red-900'
  }
  return styles[type] || 'bg-gray-50 text-gray-900'
}

function formatDate(date) {
  return date ? new Date(date).toLocaleDateString('fr-FR') : '—'
}

function formatDateTime(date) {
  return date ? new Date(date).toLocaleString('fr-FR') : ''
}

async function loadCandidat() {
  loading.value = true
  try {
    const response = await $fetch(`${config.public.apiBase}/admin/candidats/${route.params.id}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    candidat.value = response.candidat
  } catch (error) {
    console.error('Erreur chargement candidature:', error)
  } finally {
    loading.value = false
  }
}

async function valider() {
  const { $swal } = useNuxtApp()
  const result = await $swal.fire({
    title: 'Valider cette candidature ?',
    text: 'Un dignitaire sera créé à partir de ce dossier.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    confirmButtonText: 'Oui, valider',
    cancelButtonText: 'Annuler'
  })
  if (!result.isConfirmed) return

  deciding.value = true
  try {
    await $fetch(`${config.public.apiBase}/admin/candidats/${candidat.value.id}/valider`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    $swal.fire({ icon: 'success', title: 'Candidature validée', timer: 2000, showConfirmButton: false })
    loadCandidat()
  } catch (error) {
    console.error('Erreur validation:', error)
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || 'Erreur lors de la validation' })
  } finally {
    deciding.value = false
  }
}

async function refuser() {
  if (motifRefus.value.trim().length < 10) return
  const { $swal } = useNuxtApp()
  const result = await $swal.fire({
    title: 'Refuser cette candidature ?',
    text: 'Le candidat recevra le motif par email.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    confirmButtonText: 'Oui, refuser',
    cancelButtonText: 'Annuler'
  })
  if (!result.isConfirmed) return

  deciding.value = true
  try {
    await $fetch(`${config.public.apiBase}/admin/candidats/${candidat.value.id}/refuser`, {
      method: 'POST',
      body: { motif: motifRefus.value },
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    $swal.fire({ icon: 'success', title: 'Candidature refusée', timer: 2000, showConfirmButton: false })
    motifRefus.value = ''
    loadCandidat()
  } catch (error) {
    console.error('Erreur refus:', error)
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || 'Erreur lors du refus' })
  } finally {
    deciding.value = false
  }
}

async function envoyerRecommandation() {
  if (nouvelleRecommandation.value.trim().length < 3) return
  sendingMessage.value = true
  try {
    await $fetch(`${config.public.apiBase}/admin/candidats/${candidat.value.id}/messages`, {
      method: 'POST',
      body: { contenu: nouvelleRecommandation.value },
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    nouvelleRecommandation.value = ''
    const { $swal } = useNuxtApp()
    $swal.fire({ icon: 'success', title: 'Recommandation envoyée', timer: 2000, showConfirmButton: false })
    loadCandidat()
  } catch (error) {
    console.error('Erreur envoi recommandation:', error)
    const { $swal } = useNuxtApp()
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || 'Erreur lors de l\'envoi' })
  } finally {
    sendingMessage.value = false
  }
}

async function downloadDocument(doc) {
  try {
    const response = await fetch(`${config.public.apiBase}/admin/candidats/${candidat.value.id}/documents/${doc.id}/download`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    if (!response.ok) throw new Error('Échec du téléchargement')
    const blob = await response.blob()
    const a = document.createElement('a')
    a.href = URL.createObjectURL(blob)
    a.download = doc.nom_fichier
    document.body.appendChild(a)
    a.click()
    a.remove()
    URL.revokeObjectURL(a.href)
  } catch (error) {
    console.error('Erreur téléchargement document:', error)
  }
}

onMounted(() => {
  loadCandidat()
})
</script>
