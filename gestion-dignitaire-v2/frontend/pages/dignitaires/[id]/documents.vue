<template>
  <DashboardLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <!-- Bouton retour -->
      <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 mb-6">
        <NuxtLink :to="`/dignitaires/${route.params.id}`" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-all hover:translate-x-[-4px]">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Retour à la fiche
        </NuxtLink>
      </div>

      <div class="max-w-5xl mx-auto px-6 pb-12">
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 rounded-2xl shadow-2xl p-6 mb-8 text-white flex items-center gap-4">
          <img
            :src="dignitaire?.photo ? `/uploads/photos/${dignitaire.photo}` : '/default-avatar.svg'"
            :alt="`Photo de ${dignitaire?.prenom || ''}`"
            class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md"
            @error="(e) => (e.target as HTMLImageElement).src = '/default-avatar.svg'"
          >
          <div>
            <h1 class="text-2xl font-bold">Documents de {{ dignitaire?.prenom }} {{ dignitaire?.nom }}</h1>
            <p class="text-white/90 text-sm">Matricule : {{ dignitaire?.matricule || 'N/A' }}</p>
          </div>
        </div>

        <!-- Zone d'upload -->
        <div v-if="permissions.peutEcrire('Dignitaire')" class="bg-white rounded-2xl shadow-lg p-6 mb-8">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Ajouter des documents</h2>

          <div
            @drop.prevent="handleFileDrop"
            @dragover.prevent="isDragging = true"
            @dragleave="isDragging = false"
            :class="{ 'border-gabon-green-500 bg-gabon-green-50': isDragging }"
            class="border-4 border-dashed border-gray-300 rounded-2xl p-10 text-center transition-all duration-300 cursor-pointer hover:border-gabon-green-500 hover:bg-gabon-green-50"
            @click="fileInput?.click()"
          >
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="text-base font-semibold text-gray-700 mb-1">Glissez-déposez vos fichiers ici</p>
            <p class="text-sm text-gray-500 mb-2">ou cliquez pour parcourir</p>
            <p class="text-xs text-gray-400">PDF, DOC, DOCX, JPG, PNG (Max 10 Mo)</p>
            <input ref="fileInput" type="file" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" @change="handleFileSelect" class="hidden">
          </div>

          <!-- Fichiers en attente -->
          <div v-if="pendingFiles.length > 0" class="space-y-4 mt-6">
            <h3 class="font-bold text-gray-900">Fichiers à envoyer ({{ pendingFiles.length }})</h3>
            <div v-for="(pf, index) in pendingFiles" :key="index" class="bg-gray-50 rounded-xl p-4 border border-gray-200">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gabon-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gabon-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900">{{ pf.file.name }}</p>
                    <p class="text-sm text-gray-500">{{ formatFileSize(pf.file.size) }}</p>
                  </div>
                </div>
                <button type="button" @click="pendingFiles.splice(index, 1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <select v-model="pf.type_document" class="border rounded-lg px-3 py-2 text-sm">
                  <option value="">-- Type --</option>
                  <option value="diplome">Diplôme</option>
                  <option value="passeport">Passeport</option>
                  <option value="casier">Casier judiciaire</option>
                  <option value="medical">Certificat médical</option>
                  <option value="attestation">Attestation</option>
                  <option value="autre">Autre</option>
                </select>
                <input v-model="pf.nom_document" type="text" placeholder="Nom du document" class="border rounded-lg px-3 py-2 text-sm">
                <input v-model="pf.numero_document" type="text" placeholder="Numéro (optionnel)" class="border rounded-lg px-3 py-2 text-sm">
                <input v-model="pf.date_emission" type="date" placeholder="Date d'émission" class="border rounded-lg px-3 py-2 text-sm">
                <input v-model="pf.date_expiration" type="date" :min="minDateFin(pf.date_emission)" placeholder="Date d'expiration" class="border rounded-lg px-3 py-2 text-sm">
                <input v-model="pf.organisme_emetteur" type="text" placeholder="Organisme émetteur" class="border rounded-lg px-3 py-2 text-sm">
              </div>
            </div>

            <button
              @click="uploadAll"
              :disabled="uploading"
              class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 disabled:opacity-50"
            >
              {{ uploading ? 'Envoi en cours...' : `Envoyer ${pendingFiles.length} fichier(s)` }}
            </button>
          </div>
        </div>

        <!-- Liste des documents -->
        <div v-if="loading" class="bg-white rounded-2xl shadow-lg p-12 text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-4 border-green-600 border-t-transparent mx-auto"></div>
        </div>

        <div v-else-if="documents.length === 0" class="bg-white rounded-2xl shadow-lg p-12 text-center text-gray-500">
          Aucun document enregistré pour ce dignitaire
        </div>

        <div v-else class="space-y-6">
          <div v-for="(years, type) in grouped" :key="type" class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gabon-blue-600 to-gabon-blue-700 px-6 py-3">
              <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span>{{ iconeForType(type) }}</span>
                {{ typeLabel(type) }}
              </h3>
            </div>
            <div class="p-6 space-y-4">
              <div v-for="(docs, year) in years" :key="year">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ year }}</p>
                <div class="space-y-3">
                  <div v-for="doc in docs" :key="doc.id" class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-4">
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                          <p class="font-bold text-gray-800">{{ doc.nom_document || doc.nom_fichier }}</p>
                          <span v-if="doc.statut === 'expire'" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                            Expiré le {{ formatDate(doc.date_expiration) }}
                          </span>
                          <span v-else-if="doc.expire_bientot" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                            Expire bientôt ({{ formatDate(doc.date_expiration) }})
                          </span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-0.5">
                          <p v-if="doc.numero_document">N° : {{ doc.numero_document }}</p>
                          <p v-if="doc.organisme_emetteur">Organisme : {{ doc.organisme_emetteur }}</p>
                          <p v-if="doc.date_emission">Émis le : {{ formatDate(doc.date_emission) }}</p>
                          <p>{{ doc.taille_lisible }}</p>
                        </div>
                      </div>
                      <div class="flex gap-2 flex-shrink-0">
                        <button @click="voirDocument(doc)" class="text-xs font-semibold text-gabon-blue-700 bg-gabon-blue-50 hover:bg-gabon-blue-100 px-3 py-1.5 rounded-lg transition">
                          Voir
                        </button>
                        <button @click="telechargerDocument(doc)" class="text-xs font-semibold text-gabon-green-700 bg-gabon-green-50 hover:bg-gabon-green-100 px-3 py-1.5 rounded-lg transition">
                          Télécharger
                        </button>
                        <button
                          v-if="permissions.peutSupprimer()"
                          @click="supprimerDocument(doc)"
                          class="text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition"
                        >
                          Supprimer
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const route = useRoute()
const config = useRuntimeConfig()
const authStore = useAuthStore()
const api = useApi()
const fileDownload = useFileDownload()
const permissions = usePermissions()
const { $swal } = useNuxtApp()
const { minDateFin } = useDateHelpers()

const dignitaire = ref<any>(null)
const documents = ref<any[]>([])
const loading = ref(true)
const isDragging = ref(false)
const fileInput = ref<HTMLInputElement | null>(null)
const uploading = ref(false)

const pendingFiles = ref<any[]>([])

function handleFileSelect(event: Event) {
  const files = Array.from((event.target as HTMLInputElement).files || [])
  addFiles(files)
}

function handleFileDrop(event: DragEvent) {
  isDragging.value = false
  const files = Array.from(event.dataTransfer?.files || [])
  addFiles(files)
}

function addFiles(files: File[]) {
  files.forEach(file => {
    if (file.size > 10 * 1024 * 1024) {
      $swal.fire({
        icon: 'error',
        title: 'Fichier trop volumineux',
        text: `Le fichier "${file.name}" dépasse 10 Mo`
      })
      return
    }
    pendingFiles.value.push({
      file,
      type_document: '',
      nom_document: '',
      numero_document: '',
      date_emission: '',
      date_expiration: '',
      organisme_emetteur: ''
    })
  })
}

function formatFileSize(bytes: number) {
  if (bytes === 0) return '0 o'
  const k = 1024
  const sizes = ['o', 'Ko', 'Mo', 'Go']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(2))} ${sizes[i]}`
}

async function loadDocuments() {
  try {
    const response: any = await api.getDignitaireDocuments(Number(route.params.id))
    documents.value = response.documents || []
  } catch (error) {
    console.error('Erreur chargement documents:', error)
  }
}

async function uploadAll() {
  const missingType = pendingFiles.value.find(pf => !pf.type_document)
  if (missingType) {
    $swal.fire({ icon: 'error', title: 'Type manquant', text: 'Veuillez sélectionner un type pour chaque fichier.' })
    return
  }

  uploading.value = true
  let success = 0
  let failed = 0

  for (const pf of pendingFiles.value) {
    try {
      const formData = new FormData()
      formData.append('fichier', pf.file)
      formData.append('type_document', pf.type_document)
      if (pf.nom_document) formData.append('nom_document', pf.nom_document)
      if (pf.numero_document) formData.append('numero_document', pf.numero_document)
      if (pf.date_emission) formData.append('date_emission', pf.date_emission)
      if (pf.date_expiration) formData.append('date_expiration', pf.date_expiration)
      if (pf.organisme_emetteur) formData.append('organisme_emetteur', pf.organisme_emetteur)

      await api.uploadDignitaireDocument(Number(route.params.id), formData)
      success++
    } catch (error) {
      console.error('Erreur upload document:', error)
      failed++
    }
  }

  uploading.value = false
  pendingFiles.value = []
  await loadDocuments()

  if (failed === 0) {
    $swal.fire({ icon: 'success', title: 'Succès', text: `${success} document(s) ajouté(s) avec succès`, timer: 2000, showConfirmButton: false })
  } else {
    $swal.fire({ icon: 'warning', title: 'Terminé avec erreurs', text: `${success} réussi(s), ${failed} échoué(s)` })
  }
}

async function voirDocument(doc: any) {
  const siteRoot = (config.public.apiBase as string).replace(/\/api\/?$/, '')
  window.open(`${siteRoot}${doc.url_complete}`, '_blank')
}

async function telechargerDocument(doc: any) {
  try {
    await fileDownload.download(`/dignitaire-documents/${doc.id}/download`, {}, doc.nom_fichier)
  } catch (error) {
    console.error('Erreur téléchargement document:', error)
  }
}

async function supprimerDocument(doc: any) {
  const result = await $swal.fire({
    title: 'Êtes-vous sûr ?',
    text: 'Cette action est irréversible',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#dc2626',
    confirmButtonText: 'Oui, supprimer',
    cancelButtonText: 'Annuler'
  })

  if (result.isConfirmed) {
    try {
      await api.deleteDignitaireDocument(doc.id)
      $swal.fire({ icon: 'success', title: 'Supprimé', text: 'Document supprimé avec succès', timer: 2000, showConfirmButton: false })
      loadDocuments()
    } catch (error) {
      console.error('Erreur suppression document:', error)
      $swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur lors de la suppression' })
    }
  }
}

const grouped = computed(() => {
  const byType: Record<string, Record<string, any[]>> = {}
  for (const doc of documents.value) {
    const type = doc.type_document
    const year = doc.date_emission ? new Date(doc.date_emission).getFullYear().toString() : 'Sans date'
    byType[type] ??= {}
    byType[type][year] ??= []
    byType[type][year].push(doc)
  }
  return byType
})

function typeLabel(type: string) {
  const labels: Record<string, string> = {
    diplome: 'Diplômes',
    passeport: 'Passeports',
    casier: 'Casiers judiciaires',
    medical: 'Certificats médicaux',
    attestation: 'Attestations',
    autre: 'Autres documents'
  }
  return labels[type] || type
}

function iconeForType(type: string) {
  const icons: Record<string, string> = {
    diplome: '🎓',
    passeport: '🛂',
    casier: '🔒',
    medical: '⚕️',
    attestation: '📜',
    autre: '📎'
  }
  return icons[type] || '📎'
}

function formatDate(date: string | null) {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR')
}

onMounted(async () => {
  try {
    dignitaire.value = await api.getDignitaire(Number(route.params.id))
  } catch (error) {
    console.error('Erreur chargement dignitaire:', error)
  }

  await loadDocuments()
  loading.value = false
})

useHead({ title: 'Documents - Gestion Dignitaires' })
</script>
