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
      <div v-if="candidat.statut === 'en_attente'" class="lg:col-span-3">
        <TipBanner id="candidature-3-actions" title="Trois actions distinctes" icon="fa-scale-balanced">
          <strong>Valider</strong> crée automatiquement un dignitaire à partir de ce dossier.
          <strong>Refuser</strong> exige un motif d'au moins 10 caractères, envoyé au candidat par email.
          <strong>Recommandation</strong> (colonne de droite) est un simple message envoyé au candidat, sans changer le statut du dossier.
        </TipBanner>
      </div>

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
          <div v-if="candidat.documents?.length" class="space-y-3">
            <div
              v-for="doc in candidat.documents"
              :key="doc.id"
              class="bg-gray-50 rounded-lg px-4 py-3"
            >
              <div class="flex items-center justify-between mb-2">
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-medium text-gray-900">{{ doc.nom_fichier }}</div>
                  <div class="text-xs text-gray-500">{{ doc.type_document }} • {{ doc.taille_lisible }}</div>
                </div>
                <div class="flex items-center gap-2 ml-3">
                  <button
                    @click="visualiserDocument(doc)"
                    class="px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors flex items-center gap-1"
                    title="Visualiser le document"
                  >
                    <i class="fas fa-eye"></i>
                    <span>Voir</span>
                  </button>
                  <button
                    @click="downloadDocument(doc)"
                    class="px-3 py-1.5 text-xs bg-gray-600 hover:bg-gray-700 text-white rounded transition-colors"
                    title="Télécharger"
                  >
                    <i class="fas fa-download"></i>
                  </button>
                </div>
              </div>
              
              <!-- Badge de statut et boutons d'action -->
              <div class="flex items-center gap-2 mt-2">
                <span 
                  v-if="doc.statut_validation === 'valide'" 
                  class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700"
                >
                  ✓ Validé
                </span>
                <span 
                  v-else-if="doc.statut_validation === 'rejete'" 
                  class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700"
                  :title="doc.motif_rejet"
                >
                  ✕ Rejeté
                </span>
                <span 
                  v-else 
                  class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700"
                >
                  ⏳ En attente
                </span>

                <!-- Boutons de validation si en attente -->
                <div v-if="doc.statut_validation === 'en_attente'" class="flex gap-2 ml-auto">
                  <button
                    @click="validerDocument(doc.id)"
                    class="px-3 py-1 text-xs bg-green-600 hover:bg-green-700 text-white rounded transition-colors"
                  >
                    Valider
                  </button>
                  <button
                    @click="ouvrirModalRejet('document', doc.id)"
                    class="px-3 py-1 text-xs bg-red-600 hover:bg-red-700 text-white rounded transition-colors"
                  >
                    Rejeter
                  </button>
                </div>
              </div>

              <!-- Afficher le motif de rejet si rejeté -->
              <div v-if="doc.statut_validation === 'rejete' && doc.motif_rejet" class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded">
                <strong>Motif :</strong> {{ doc.motif_rejet }}
              </div>
            </div>
          </div>
          <p v-else class="text-sm text-gray-400">Aucun document joint.</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-4">Diplômes ({{ candidat.diplomes?.length || 0 }})</h3>
          <ul v-if="candidat.diplomes?.length" class="space-y-3">
            <li v-for="d in candidat.diplomes" :key="d.id" class="bg-gray-50 rounded-lg px-4 py-3">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="font-semibold text-gray-800">{{ d.intitule }} <span class="text-gray-400 font-normal">— {{ d.annee }}</span></div>
                  <div class="text-gray-500 text-sm">{{ d.etablissement?.nom }} <span v-if="d.domaine">· {{ d.domaine.nom }}</span></div>
                </div>
                
                <!-- Bouton voir justificatif si présent -->
                <button
                  v-if="d.justificatif_path"
                  @click="visualiserJustificatif('diplome', d)"
                  class="ml-3 px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors flex items-center gap-1"
                  title="Voir le justificatif"
                >
                  <i class="fas fa-eye"></i>
                  <span>Voir justificatif</span>
                </button>
              </div>
              
              <!-- Badge de statut et boutons d'action -->
              <div class="flex items-center gap-2 mt-2">
                <span 
                  v-if="d.statut_validation === 'valide'" 
                  class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700"
                >
                  ✓ Validé
                </span>
                <span 
                  v-else-if="d.statut_validation === 'rejete'" 
                  class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700"
                  :title="d.motif_rejet"
                >
                  ✕ Rejeté
                </span>
                <span 
                  v-else 
                  class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700"
                >
                  ⏳ En attente
                </span>

                <!-- Boutons de validation si en attente -->
                <div v-if="d.statut_validation === 'en_attente'" class="flex gap-2 ml-auto">
                  <button
                    @click="validerDiplome(d.id)"
                    class="px-3 py-1 text-xs bg-green-600 hover:bg-green-700 text-white rounded transition-colors"
                  >
                    Valider
                  </button>
                  <button
                    @click="ouvrirModalRejet('diplome', d.id)"
                    class="px-3 py-1 text-xs bg-red-600 hover:bg-red-700 text-white rounded transition-colors"
                  >
                    Rejeter
                  </button>
                </div>
              </div>

              <!-- Afficher le motif de rejet si rejeté -->
              <div v-if="d.statut_validation === 'rejete' && d.motif_rejet" class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded">
                <strong>Motif :</strong> {{ d.motif_rejet }}
              </div>
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
          <ul v-if="candidat.experiences?.length" class="space-y-3">
            <li v-for="e in candidat.experiences" :key="e.id" class="bg-gray-50 rounded-lg px-4 py-3">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="font-semibold text-gray-800">{{ e.intitule }}</div>
                  <div class="text-gray-500 text-sm">{{ e.structure?.nom }} · {{ formatDate(e.date_debut) }} — {{ e.date_fin ? formatDate(e.date_fin) : 'en cours' }}</div>
                </div>
                
                <!-- Bouton voir justificatif si présent -->
                <button
                  v-if="e.justificatif_path"
                  @click="visualiserJustificatif('experience', e)"
                  class="ml-3 px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors flex items-center gap-1"
                  title="Voir le justificatif"
                >
                  <i class="fas fa-eye"></i>
                  <span>Voir justificatif</span>
                </button>
              </div>
              
              <!-- Badge de statut et boutons d'action -->
              <div class="flex items-center gap-2 mt-2">
                <span 
                  v-if="e.statut_validation === 'valide'" 
                  class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700"
                >
                  ✓ Validé
                </span>
                <span 
                  v-else-if="e.statut_validation === 'rejete'" 
                  class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700"
                  :title="e.motif_rejet"
                >
                  ✕ Rejeté
                </span>
                <span 
                  v-else 
                  class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700"
                >
                  ⏳ En attente
                </span>

                <!-- Boutons de validation si en attente -->
                <div v-if="e.statut_validation === 'en_attente'" class="flex gap-2 ml-auto">
                  <button
                    @click="validerExperience(e.id)"
                    class="px-3 py-1 text-xs bg-green-600 hover:bg-green-700 text-white rounded transition-colors"
                  >
                    Valider
                  </button>
                  <button
                    @click="ouvrirModalRejet('experience', e.id)"
                    class="px-3 py-1 text-xs bg-red-600 hover:bg-red-700 text-white rounded transition-colors"
                  >
                    Rejeter
                  </button>
                </div>
              </div>

              <!-- Afficher le motif de rejet si rejeté -->
              <div v-if="e.statut_validation === 'rejete' && e.motif_rejet" class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded">
                <strong>Motif :</strong> {{ e.motif_rejet }}
              </div>
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

    <!-- Modal de rejet -->
    <div v-if="modalRejet.visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="fermerModalRejet">
      <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Motif du rejet</h3>
        <p class="text-sm text-gray-600 mb-4">
          Veuillez indiquer pourquoi vous rejetez ce {{ modalRejet.type }} (minimum 10 caractères).
        </p>
        <textarea
          v-model="modalRejet.motif"
          rows="4"
          placeholder="Exemple : Le document fourni n'est pas lisible..."
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent"
        ></textarea>
        <div class="flex justify-end gap-3 mt-4">
          <button
            @click="fermerModalRejet"
            class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors"
          >
            Annuler
          </button>
          <button
            @click="confirmerRejet"
            :disabled="modalRejet.motif.trim().length < 10"
            class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Rejeter
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de visualisation de document -->
    <div v-if="modalDocument.visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" @click.self="fermerModalDocument">
      <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full mx-4 h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b">
          <div class="flex-1 min-w-0">
            <h3 class="text-lg font-bold text-gray-900 truncate">{{ modalDocument.nom }}</h3>
            <p class="text-sm text-gray-500">{{ modalDocument.type }}</p>
          </div>
          <div class="flex items-center gap-2 ml-4">
            <button
              @click="telechargerDocumentModal"
              class="px-3 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
            >
              <i class="fas fa-download mr-2"></i>Télécharger
            </button>
            <button
              @click="fermerModalDocument"
              class="px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>

        <!-- Contenu (iframe pour PDF ou image) -->
        <div class="flex-1 overflow-auto bg-gray-100 p-4">
          <div v-if="modalDocument.loading" class="h-full flex items-center justify-center">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
          </div>
          <div v-else-if="modalDocument.error" class="h-full flex flex-col items-center justify-center text-gray-500">
            <i class="fas fa-exclamation-triangle text-4xl mb-3 text-red-500"></i>
            <p class="text-lg font-medium">Impossible de charger le document</p>
            <p class="text-sm mt-2">{{ modalDocument.error }}</p>
          </div>
          <div v-else class="h-full flex items-center justify-center">
            <!-- Pour les PDF -->
            <iframe
              v-if="modalDocument.isPdf"
              :src="modalDocument.url"
              class="w-full h-full rounded-lg border border-gray-300"
              frameborder="0"
            ></iframe>
            <!-- Pour les images -->
            <img
              v-else-if="modalDocument.isImage"
              :src="modalDocument.url"
              :alt="modalDocument.nom"
              class="max-w-full max-h-full object-contain rounded-lg shadow-lg"
            />
            <!-- Autre type de fichier -->
            <div v-else class="text-center">
              <i class="fas fa-file text-6xl text-gray-400 mb-4"></i>
              <p class="text-gray-600">Prévisualisation non disponible pour ce type de fichier</p>
              <button
                @click="telechargerDocumentModal"
                class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
              >
                <i class="fas fa-download mr-2"></i>Télécharger pour consulter
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
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
const helpPanel = useHelpPanel()
const toast = useToast()

const candidat = ref(null)
const loading = ref(true)
const deciding = ref(false)
const sendingMessage = ref(false)
const motifRefus = ref('')
const nouvelleRecommandation = ref('')
const modalRejet = ref({
  visible: false,
  type: '', // 'document', 'diplome', 'experience'
  id: null,
  motif: ''
})
const modalDocument = ref({
  visible: false,
  loading: false,
  error: null,
  url: null,
  nom: '',
  type: '',
  isPdf: false,
  isImage: false,
  documentId: null
})

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
    toast.success('Candidature validée')
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
    toast.success('Candidature refusée')
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
    toast.success('Recommandation envoyée')
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

// Visualiser un document dans le modal
async function visualiserDocument(doc) {
  modalDocument.value = {
    visible: true,
    loading: true,
    error: null,
    url: null,
    nom: doc.nom_fichier,
    type: doc.type_document,
    isPdf: false,
    isImage: false,
    documentId: doc.id
  }

  try {
    const response = await fetch(`${config.public.apiBase}/admin/candidats/${candidat.value.id}/documents/${doc.id}/download`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    if (!response.ok) throw new Error('Échec du chargement')
    
    const blob = await response.blob()
    const url = URL.createObjectURL(blob)
    
    // Détecter le type de fichier
    const extension = doc.nom_fichier.split('.').pop().toLowerCase()
    const isPdf = extension === 'pdf' || blob.type === 'application/pdf'
    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension) || blob.type.startsWith('image/')
    
    modalDocument.value = {
      ...modalDocument.value,
      loading: false,
      url,
      isPdf,
      isImage
    }
  } catch (error) {
    console.error('Erreur chargement document:', error)
    modalDocument.value.loading = false
    modalDocument.value.error = 'Impossible de charger le document. Veuillez réessayer.'
  }
}

function fermerModalDocument() {
  if (modalDocument.value.url) {
    URL.revokeObjectURL(modalDocument.value.url)
  }
  modalDocument.value = {
    visible: false,
    loading: false,
    error: null,
    url: null,
    nom: '',
    type: '',
    isPdf: false,
    isImage: false,
    documentId: null
  }
}

async function telechargerDocumentModal() {
  if (!modalDocument.value.documentId) return
  
  const doc = candidat.value.documents.find(d => d.id === modalDocument.value.documentId)
  if (doc) {
    await downloadDocument(doc)
  }
}

// Visualiser un justificatif de diplôme ou expérience
async function visualiserJustificatif(type, item) {
  modalDocument.value = {
    visible: true,
    loading: true,
    error: null,
    url: null,
    nom: type === 'diplome' ? `Justificatif - ${item.intitule}` : `Justificatif - ${item.intitule}`,
    type: type,
    isPdf: false,
    isImage: false,
    documentId: null
  }

  try {
    // Pour les justificatifs, ils sont stockés dans le storage Laravel
    // On doit les charger via une route dédiée ou directement depuis le storage public
    const filePath = item.justificatif_path
    
    // Option 1: Essayer de charger directement depuis le storage public
    let fileUrl = `${config.public.apiBase.replace('/api', '')}/storage/${filePath}`
    
    console.log('Tentative de chargement du justificatif:', fileUrl)
    
    let response = await fetch(fileUrl)
    
    // Si ça échoue, essayer sans le préfixe 'candidats/'
    if (!response.ok && filePath.startsWith('candidats/')) {
      fileUrl = `${config.public.apiBase.replace('/api', '')}/storage/${filePath.replace('candidats/', '')}`
      console.log('Tentative alternative:', fileUrl)
      response = await fetch(fileUrl)
    }
    
    // Si ça échoue encore, le fichier n'existe probablement pas
    if (!response.ok) {
      throw new Error(`Fichier introuvable (Status: ${response.status})`)
    }
    
    const blob = await response.blob()
    const url = URL.createObjectURL(blob)
    
    // Détecter le type de fichier
    const extension = filePath.split('.').pop().toLowerCase()
    const isPdf = extension === 'pdf' || blob.type === 'application/pdf'
    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension) || blob.type.startsWith('image/')
    
    modalDocument.value = {
      ...modalDocument.value,
      loading: false,
      url,
      isPdf,
      isImage
    }
  } catch (error) {
    console.error('Erreur chargement justificatif:', error)
    modalDocument.value.loading = false
    modalDocument.value.error = `Impossible de charger le justificatif. ${error.message || 'Le fichier est peut-être manquant.'}`
  }
}

// Fonctions de validation
async function validerDocument(documentId) {
  const { $swal } = useNuxtApp()
  try {
    await $fetch(`${config.public.apiBase}/admin/candidats/${candidat.value.id}/documents/${documentId}/valider`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    toast.success('Document validé')
    await loadCandidat()
  } catch (error) {
    console.error('Erreur validation document:', error)
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || 'Erreur lors de la validation' })
  }
}

async function validerDiplome(diplomeId) {
  const { $swal } = useNuxtApp()
  try {
    await $fetch(`${config.public.apiBase}/admin/candidats/${candidat.value.id}/diplomes/${diplomeId}/valider`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    toast.success('Diplôme validé')
    await loadCandidat()
  } catch (error) {
    console.error('Erreur validation diplôme:', error)
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || 'Erreur lors de la validation' })
  }
}

async function validerExperience(experienceId) {
  const { $swal } = useNuxtApp()
  try {
    await $fetch(`${config.public.apiBase}/admin/candidats/${candidat.value.id}/experiences/${experienceId}/valider`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    toast.success('Expérience validée')
    await loadCandidat()
  } catch (error) {
    console.error('Erreur validation expérience:', error)
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || 'Erreur lors de la validation' })
  }
}

// Fonctions de rejet avec modal
function ouvrirModalRejet(type, id) {
  modalRejet.value = {
    visible: true,
    type,
    id,
    motif: ''
  }
}

function fermerModalRejet() {
  modalRejet.value = {
    visible: false,
    type: '',
    id: null,
    motif: ''
  }
}

async function confirmerRejet() {
  if (modalRejet.value.motif.trim().length < 10) return

  const { $swal } = useNuxtApp()
  const typeLabel = {
    document: 'document',
    diplome: 'diplôme',
    experience: 'expérience'
  }[modalRejet.value.type] || 'élément'

  try {
    const endpoint = `${config.public.apiBase}/admin/candidats/${candidat.value.id}/${modalRejet.value.type}s/${modalRejet.value.id}/rejeter`
    
    await $fetch(endpoint, {
      method: 'POST',
      body: { motif: modalRejet.value.motif },
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    toast.success(`${typeLabel.charAt(0).toUpperCase() + typeLabel.slice(1)} rejeté`)
    fermerModalRejet()
    await loadCandidat()
  } catch (error) {
    console.error(`Erreur rejet ${typeLabel}:`, error)
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.data?.message || `Erreur lors du rejet du ${typeLabel}` })
  }
}

onMounted(() => {
  loadCandidat()
  helpPanel.setContent(HELP_CONTENT['candidature-detail'])
})
</script>
