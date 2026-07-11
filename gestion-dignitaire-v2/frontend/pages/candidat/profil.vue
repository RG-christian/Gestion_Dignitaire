<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
      <div class="px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-lg flex items-center justify-center shadow-md">
            <Building2 class="w-6 h-6 text-white" />
          </div>
          <div>
            <h1 class="text-lg font-bold text-gray-900">Gestion Dignitaires</h1>
            <p class="text-xs text-gray-500">Espace Candidat</p>
          </div>
        </div>
        <button @click="logout" class="flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg font-medium transition-all duration-200">
          <LogOut class="w-5 h-5" />
          <span class="hidden sm:inline">Déconnexion</span>
        </button>
      </div>
    </nav>

    <!-- Sidebar navigation (identique au dashboard) -->
    <aside class="hidden lg:block fixed left-0 top-16 bottom-0 w-72 bg-white border-r border-gray-200 overflow-y-auto">
      <div class="p-6">
        <nav class="space-y-2">
          <NuxtLink
            v-for="item in navItems"
            :key="item.id"
            :to="item.to"
            class="w-full flex items-center gap-4 px-5 py-4 rounded-xl transition-all duration-200 group"
            :class="item.active ? 'bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50'"
          >
            <component :is="item.iconComponent" class="w-6 h-6 flex-shrink-0" :class="item.active ? 'text-white' : 'text-gray-500 group-hover:text-gabon-green-600'" />
            <span class="text-base font-semibold flex-1 text-left">{{ item.label }}</span>
            <div v-if="item.active" class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
          </NuxtLink>
        </nav>
      </div>
    </aside>

    <main class="lg:ml-72 pt-24 pb-12 px-4 sm:px-6 lg:px-10">
      <div class="w-full max-w-full space-y-6">
        <div v-if="loading" class="flex justify-center py-20">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gabon-green-600"></div>
        </div>

        <template v-else>
          <!-- En-tête de page -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-200 border-l-4 border-l-gabon-green-600 px-6 py-5 flex items-center gap-4">
            <div class="w-11 h-11 bg-gabon-green-600 rounded-full flex items-center justify-center flex-shrink-0">
              <User class="w-6 h-6 text-white" />
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Mon Profil</h1>
              <p class="text-sm text-gray-500">Gérez vos informations personnelles et le statut de votre candidature</p>
            </div>
          </div>

          <div class="grid lg:grid-cols-3 gap-6 items-start">
            <!-- Colonne gauche : carte profil -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
              <div class="flex flex-col items-center text-center">
                <div class="relative">
                  <div class="w-24 h-24 rounded-full overflow-hidden bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 flex items-center justify-center shadow-lg">
                    <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                    <span v-else class="text-2xl font-bold text-white">{{ getInitials(candidat?.nom_complet) }}</span>
                  </div>
                  <label
                    v-if="estModifiable"
                    class="absolute -bottom-1 -right-1 w-8 h-8 bg-white border-2 border-gray-100 rounded-full flex items-center justify-center shadow-md cursor-pointer hover:bg-gray-50 transition-colors"
                    title="Changer la photo"
                  >
                    <Pencil class="w-4 h-4 text-gabon-green-700" />
                    <input type="file" accept="image/jpeg,image/png,image/jpg" class="hidden" @change="handlePhotoChange">
                  </label>
                </div>
                <h2 class="mt-4 text-lg font-bold text-gray-900">{{ candidat?.nom_complet }}</h2>
                <p class="text-sm text-gray-500">{{ candidat?.email }}</p>
              </div>

              <!-- Statut candidature (mis en avant) -->
              <div class="mt-5 rounded-xl px-4 py-3 flex items-center justify-between" :class="statutHighlightClass">
                <div>
                  <p class="text-xs font-semibold uppercase tracking-wide opacity-80">Ma candidature</p>
                  <p class="text-lg font-bold">{{ statutLabel }}</p>
                </div>
                <CheckCircle2 v-if="candidat?.statut === 'valide'" class="w-8 h-8 opacity-80" />
                <XCircle v-else-if="candidat?.statut === 'refuse'" class="w-8 h-8 opacity-80" />
                <Clock v-else class="w-8 h-8 opacity-80" />
              </div>
              <p v-if="candidat?.statut === 'refuse' && candidat?.motif_refus" class="mt-2 text-xs text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                <strong>Motif :</strong> {{ candidat.motif_refus }}
              </p>

              <!-- Infos rapides -->
              <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2.5">
                  <span class="text-xs text-gray-500 flex items-center gap-2">
                    <CreditCard class="w-4 h-4" />
                    Matricule
                  </span>
                  <span class="text-sm font-semibold text-gray-800">{{ candidat?.matricule || 'Non attribué' }}</span>
                </div>
                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2.5">
                  <span class="text-xs text-gray-500 flex items-center gap-2">
                    <Phone class="w-4 h-4" />
                    Téléphone
                  </span>
                  <span class="text-sm font-semibold text-gray-800">{{ candidat?.telephone || 'Non renseigné' }}</span>
                </div>
                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2.5">
                  <span class="text-xs text-gray-500 flex items-center gap-2">
                    <Calendar class="w-4 h-4" />
                    Soumise le
                  </span>
                  <span class="text-sm font-semibold text-gray-800">{{ formatDate(candidat?.date_candidature) }}</span>
                </div>
              </div>
            </div>

            <!-- Colonne droite : onglets Informations / Sécurité -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
              <div class="flex border-b border-gray-200">
                <button
                  @click="activeTab = 'infos'"
                  :class="activeTab === 'infos' ? 'text-gabon-green-700 border-gabon-green-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                  class="flex-1 flex items-center justify-center gap-2 px-4 py-4 text-sm font-semibold border-b-2 transition-colors"
                >
                  <User class="w-4 h-4" />
                  Informations
                </button>
                <button
                  @click="activeTab = 'password'"
                  :class="activeTab === 'password' ? 'text-gabon-green-700 border-gabon-green-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                  class="flex-1 flex items-center justify-center gap-2 px-4 py-4 text-sm font-semibold border-b-2 transition-colors"
                >
                  <Lock class="w-4 h-4" />
                  Sécurité
                </button>
              </div>

              <div class="p-6">
                <!-- Onglet Informations -->
                <form v-if="activeTab === 'infos'" @submit.prevent="saveInfos" class="space-y-4">
                  <p v-if="!estModifiable" class="text-sm text-yellow-800 bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2">
                    Votre dossier a déjà été examiné : ces informations ne sont plus modifiables.
                  </p>

                  <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                      <label class="text-xs font-semibold text-gray-600 flex items-center gap-1.5 mb-1">
                        <User class="w-3.5 h-3.5 text-gray-400" />
                        Prénom
                      </label>
                      <input v-model="form.prenom" :disabled="!estModifiable" type="text" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                    </div>
                    <div>
                      <label class="text-xs font-semibold text-gray-600 flex items-center gap-1.5 mb-1">
                        <User class="w-3.5 h-3.5 text-gray-400" />
                        Nom
                      </label>
                      <input v-model="form.nom" :disabled="!estModifiable" type="text" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                      <label class="text-xs font-semibold text-gray-600 flex items-center gap-1.5 mb-1">
                        <Mail class="w-3.5 h-3.5 text-gray-400" />
                        Email
                      </label>
                      <input :value="candidat?.email" disabled type="email" class="w-full px-3 py-2.5 border border-gray-200 bg-gray-100 text-gray-500 rounded-lg text-sm">
                      <p class="text-xs text-gray-400 mt-1 italic">Ce champ n'est pas modifiable.</p>
                    </div>
                    <div>
                      <label class="text-xs font-semibold text-gray-600 flex items-center gap-1.5 mb-1">
                        <Phone class="w-3.5 h-3.5 text-gray-400" />
                        Téléphone
                      </label>
                      <input v-model="form.telephone" :disabled="!estModifiable" type="text" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                      <label class="text-xs font-semibold text-gray-600 block mb-1">NIP</label>
                      <input v-model="form.nip" :disabled="!estModifiable" type="text" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                    </div>
                    <div>
                      <label class="text-xs font-semibold text-gray-600 block mb-1">Matricule</label>
                      <input v-model="form.matricule" :disabled="!estModifiable" type="text" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                    </div>

                    <div>
                      <label class="text-xs font-semibold text-gray-600 flex items-center gap-1.5 mb-1">
                        <Calendar class="w-3.5 h-3.5 text-gray-400" />
                        Date de naissance
                      </label>
                      <input v-model="form.date_naissance" :disabled="!estModifiable" type="date" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                    </div>
                    <div>
                      <label class="text-xs font-semibold text-gray-600 block mb-1">Genre</label>
                      <select v-model="form.genre" :disabled="!estModifiable" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                      </select>
                    </div>

                    <div>
                      <label class="text-xs font-semibold text-gray-600 block mb-1">État civil</label>
                      <select v-model="form.etat_civil" :disabled="!estModifiable" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                        <option value="">Non renseigné</option>
                        <option value="Célibataire">Célibataire</option>
                        <option value="Marié(e)">Marié(e)</option>
                        <option value="Divorcé(e)">Divorcé(e)</option>
                        <option value="Veuf(ve)">Veuf(ve)</option>
                      </select>
                    </div>
                    <div>
                      <label class="text-xs font-semibold text-gray-600 flex items-center gap-1.5 mb-1">
                        <MapPin class="w-3.5 h-3.5 text-gray-400" />
                        Ville de naissance
                      </label>
                      <select v-model="form.lieu_naissance_id" :disabled="!estModifiable" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                        <option value="">Non renseignée</option>
                        <option v-for="v in villes" :key="v.id" :value="v.id">{{ v.nom }}</option>
                      </select>
                    </div>

                    <div>
                      <label class="text-xs font-semibold text-gray-600 flex items-center gap-1.5 mb-1">
                        <MapPin class="w-3.5 h-3.5 text-gray-400" />
                        Ville de résidence
                      </label>
                      <select v-model="form.ville_residence_id" :disabled="!estModifiable" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                        <option value="">Non renseignée</option>
                        <option v-for="v in villes" :key="v.id" :value="v.id">{{ v.nom }}</option>
                      </select>
                    </div>
                    <div>
                      <label class="text-xs font-semibold text-gray-600 block mb-1">Adresse</label>
                      <input v-model="form.adresse" :disabled="!estModifiable" type="text" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                    </div>
                  </div>

                  <div v-if="estModifiable" class="flex justify-end pt-2">
                    <button
                      type="submit"
                      :disabled="savingInfos"
                      class="flex items-center gap-2 px-6 py-2.5 bg-gabon-blue-800 hover:bg-gabon-blue-900 text-white font-semibold rounded-lg transition-colors disabled:opacity-50"
                    >
                      <Save class="w-4 h-4" />
                      {{ savingInfos ? 'Enregistrement…' : 'Enregistrer les modifications' }}
                    </button>
                  </div>
                </form>

                <!-- Onglet Sécurité -->
                <form v-else @submit.prevent="savePassword" class="space-y-4 max-w-md">
                  <div>
                    <label class="text-xs font-semibold text-gray-600 block mb-1">Mot de passe actuel</label>
                    <input v-model="passwordForm.current_password" type="password" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                  </div>
                  <div>
                    <label class="text-xs font-semibold text-gray-600 block mb-1">Nouveau mot de passe</label>
                    <input v-model="passwordForm.new_password" type="password" required minlength="8" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                  </div>
                  <div>
                    <label class="text-xs font-semibold text-gray-600 block mb-1">Confirmer le nouveau mot de passe</label>
                    <input v-model="passwordForm.new_password_confirmation" type="password" required minlength="8" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                  </div>
                  <div class="flex justify-end pt-2">
                    <button type="submit" :disabled="savingPassword" class="flex items-center gap-2 px-6 py-2.5 bg-gabon-blue-800 hover:bg-gabon-blue-900 text-white font-semibold rounded-lg transition-colors disabled:opacity-50">
                      <Lock class="w-4 h-4" />
                      {{ savingPassword ? 'Enregistrement…' : 'Changer le mot de passe' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </template>
      </div>
    </main>
  </div>
</template>

<script setup>
import {
  Bell, Briefcase, Building2, Calendar, CheckCircle2, ClipboardCheck, Clock, CreditCard,
  FileText, GraduationCap, Languages, Lock, LogOut, Mail, MapPin, Pencil, Phone, Save,
  User, XCircle
} from 'lucide-vue-next'

definePageMeta({
  layout: false
})

const router = useRouter()
const route = useRoute()
const { $api, $swal } = useNuxtApp()
const config = useRuntimeConfig()

const navItems = [
  { id: 'dashboard', label: 'Tableau de bord', to: '/candidat/dashboard', iconComponent: ClipboardCheck },
  { id: 'mon-profil', label: 'Mon profil', to: '/candidat/profil', iconComponent: User, active: true },
]

const loading = ref(true)
const candidat = ref(null)
const villes = ref([])
const photoPreview = ref(null)
const savingInfos = ref(false)
const savingPassword = ref(false)
const activeTab = ref(route.query.tab === 'password' ? 'password' : 'infos')

const form = reactive({
  nom: '',
  prenom: '',
  date_naissance: '',
  genre: 'M',
  nip: '',
  matricule: '',
  etat_civil: '',
  telephone: '',
  adresse: '',
  lieu_naissance_id: '',
  ville_residence_id: '',
  photo: null
})

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const estModifiable = computed(() => candidat.value?.statut === 'en_attente')

const statutLabel = computed(() => {
  return { en_attente: 'En attente', valide: 'Validée', refuse: 'Refusée' }[candidat.value?.statut] || '—'
})

const statutHighlightClass = computed(() => ({
  'bg-yellow-50 text-yellow-800': candidat.value?.statut === 'en_attente',
  'bg-green-50 text-green-800': candidat.value?.statut === 'valide',
  'bg-red-50 text-red-800': candidat.value?.statut === 'refuse'
}))

function getInitials(name) {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase()
}

function formatDate(date) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
}

function authHeaders() {
  return { Authorization: `Bearer ${localStorage.getItem('candidat_token')}` }
}

function handlePhotoChange(event) {
  const file = event.target.files?.[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) {
    $swal.fire({ icon: 'error', title: 'Fichier trop volumineux', text: "L'image dépasse 2 Mo" })
    return
  }
  const reader = new FileReader()
  reader.onload = () => {
    form.photo = reader.result
    photoPreview.value = reader.result
  }
  reader.readAsDataURL(file)
}

async function loadCandidat() {
  loading.value = true
  const token = localStorage.getItem('candidat_token')
  if (!token) {
    router.push('/candidature/login')
    return
  }

  try {
    const response = await $api.get('/candidats/me', { headers: authHeaders() })
    if (response.success) {
      candidat.value = response.candidat
      form.nom = candidat.value.nom || ''
      form.prenom = candidat.value.prenom || ''
      form.date_naissance = candidat.value.date_naissance ? candidat.value.date_naissance.substring(0, 10) : ''
      form.genre = candidat.value.genre || 'M'
      form.nip = candidat.value.nip || ''
      form.matricule = candidat.value.matricule || ''
      form.etat_civil = candidat.value.etat_civil || ''
      form.telephone = candidat.value.telephone || ''
      form.adresse = candidat.value.adresse || ''
      form.lieu_naissance_id = candidat.value.lieu_naissance_id || ''
      form.ville_residence_id = candidat.value.ville_residence_id || ''
      if (candidat.value.photo) {
        photoPreview.value = `${config.public.apiBase.replace(/\/api\/?$/, '')}/storage/${candidat.value.photo}`
      }
    }
  } catch (error) {
    console.error('Erreur chargement profil:', error)
    if (error.response?.status === 401) {
      localStorage.removeItem('candidat_token')
      router.push('/candidature/login')
    }
  } finally {
    loading.value = false
  }
}

async function loadVilles() {
  try {
    villes.value = await $api.get('/villes', { headers: authHeaders() })
  } catch (error) {
    console.error('Erreur chargement villes:', error)
  }
}

async function saveInfos() {
  savingInfos.value = true
  try {
    const payload = { ...form }
    if (!payload.photo) delete payload.photo
    const response = await $api.put('/candidats/me', payload, { headers: authHeaders() })
    if (response.success) {
      candidat.value = { ...candidat.value, ...response.candidat }
    }
    $swal.fire({ icon: 'success', title: 'Informations mises à jour', timer: 2000, showConfirmButton: false })
  } catch (error) {
    console.error('Erreur mise à jour informations:', error)
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Erreur lors de la mise à jour' })
  } finally {
    savingInfos.value = false
  }
}

async function savePassword() {
  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: 'Les mots de passe ne correspondent pas' })
    return
  }
  savingPassword.value = true
  try {
    await $api.put('/candidats/me/password', passwordForm, { headers: authHeaders() })
    $swal.fire({ icon: 'success', title: 'Mot de passe changé', timer: 2000, showConfirmButton: false })
    passwordForm.current_password = ''
    passwordForm.new_password = ''
    passwordForm.new_password_confirmation = ''
  } catch (error) {
    console.error('Erreur changement mot de passe:', error)
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Le mot de passe actuel est incorrect' })
  } finally {
    savingPassword.value = false
  }
}

async function logout() {
  try {
    await $api.post('/candidats/logout', {}, { headers: authHeaders() })
  } catch (error) {
    console.error('Erreur déconnexion:', error)
  } finally {
    localStorage.removeItem('candidat_token')
    router.push('/accueil')
  }
}

onMounted(() => {
  loadCandidat()
  loadVilles()
})

useHead({
  title: 'Mon profil - Gestion Dignitaires'
})
</script>

<style scoped>
/* assets/css/global.css force `main { width: 100% }` pour toute l'app
   (pensé pour les layouts flex). Ce <main> utilise lg:ml-72 (margin-left) :
   width:100% + margin-left additionne 288px en trop et fait déborder toute
   la page hors de l'écran (même bug que candidat/dashboard.vue, corrigé de
   la même façon ici). */
main {
  width: auto !important;
  max-width: none !important;
}
</style>
