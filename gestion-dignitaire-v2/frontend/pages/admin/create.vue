<template>
  <DashboardLayout>
    <div class="max-w-7xl mx-auto p-6">
      <!-- En-tête de page -->
      <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 flex items-center justify-center">
            <Users class="w-6 h-6 text-white" />
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des utilisateurs</h1>
            <p class="text-gray-600">Créez et gérez les comptes administrateurs</p>
          </div>
        </div>
      </div>

      <!-- Message d'erreur -->
      <div v-if="error" class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg mb-6 flex items-start gap-3">
        <AlertCircle class="w-5 h-5 flex-shrink-0 mt-0.5" />
        <div>
          <p class="font-semibold">Erreur</p>
          <p class="text-sm">{{ error }}</p>
        </div>
      </div>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gabon-green-600"></div>
      </div>

      <template v-else>
        <!-- Liste des utilisateurs existants -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8 overflow-hidden">
          <div class="bg-gradient-to-r from-gabon-green-50 to-gabon-blue-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center">
                  <Users class="w-5 h-5 text-gabon-green-600" />
                </div>
                <div>
                  <h2 class="text-xl font-bold text-gray-900">Utilisateurs existants</h2>
                  <p class="text-sm text-gray-600">{{ users.length }} compte(s) enregistré(s)</p>
                </div>
              </div>
              <button
                @click="toggleFormVisibility"
                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-gabon-green-600 to-gabon-blue-600 hover:from-gabon-green-700 hover:to-gabon-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all"
              >
                <UserPlus class="w-5 h-5" />
                <span>{{ showForm ? 'Masquer le formulaire' : 'Créer un utilisateur' }}</span>
              </button>
            </div>
          </div>

          <div class="p-6">
            <div class="overflow-x-auto">
              <table v-if="users.length > 0" class="min-w-full">
                <thead>
                  <tr class="border-b-2 border-gray-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom d'utilisateur</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom complet</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rôle</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-4">
                      <div class="flex items-center gap-2">
                        <User class="w-4 h-4 text-gray-400" />
                        <span class="font-medium text-gray-900">{{ user.username }}</span>
                      </div>
                    </td>
                    <td class="px-4 py-4 text-gray-700">{{ user.nom_complet }}</td>
                    <td class="px-4 py-4">
                      <div class="flex items-center gap-2 text-gray-600">
                        <Mail class="w-4 h-4 text-gray-400" />
                        <span class="text-sm">{{ user.email }}</span>
                      </div>
                    </td>
                    <td class="px-4 py-4">
                      <span :class="roleBadgeClass(user.role_name)" class="px-3 py-1.5 rounded-full text-xs font-semibold">
                        {{ user.role_name }}
                      </span>
                    </td>
                    <td class="px-4 py-4">
                      <div class="flex items-center justify-center gap-2">
                        <!-- Bouton Modifier -->
                        <button
                          @click="editUser(user)"
                          class="group flex items-center gap-2 px-3 py-2 text-sm font-medium text-gabon-blue-600 bg-gabon-blue-50 hover:bg-gabon-blue-600 hover:text-white rounded-lg transition-all duration-200 hover:shadow-md hover:scale-105 border border-gabon-blue-200"
                          title="Modifier l'utilisateur"
                        >
                          <Edit2 class="w-4 h-4 group-hover:scale-110 transition-transform" />
                          <span class="hidden lg:inline">Modifier</span>
                        </button>

                        <!-- Bouton Supprimer -->
                        <button
                          @click="deleteUser(user)"
                          class="group flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-600 hover:text-white rounded-lg transition-all duration-200 hover:shadow-md hover:scale-105 border border-red-200"
                          title="Supprimer l'utilisateur"
                        >
                          <Trash2 class="w-4 h-4 group-hover:scale-110 transition-transform" />
                          <span class="hidden lg:inline">Supprimer</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-else class="text-center py-12">
                <UserX class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                <p class="text-gray-500 font-medium">Aucun utilisateur trouvé</p>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="users.length > perPage" class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
              <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                  Affichage de <span class="font-semibold text-gray-900">{{ ((currentPage - 1) * perPage) + 1 }}</span> à
                  <span class="font-semibold text-gray-900">{{ Math.min(currentPage * perPage, users.length) }}</span> sur
                  <span class="font-semibold text-gray-900">{{ users.length }}</span> utilisateurs
                </div>
                <div class="flex items-center gap-2">
                  <button
                    @click="currentPage--"
                    :disabled="currentPage === 1"
                    :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-white hover:shadow-md'"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition-all"
                  >
                    Précédent
                  </button>
                  <div class="flex items-center gap-1">
                    <button
                      v-for="page in totalPages"
                      :key="page"
                      @click="currentPage = page"
                      :class="currentPage === page ? 'bg-gradient-to-r from-gabon-green-600 to-gabon-blue-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100'"
                      class="w-10 h-10 rounded-lg text-sm font-semibold transition-all"
                    >
                      {{ page }}
                    </button>
                  </div>
                  <button
                    @click="currentPage++"
                    :disabled="currentPage === totalPages"
                    :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-white hover:shadow-md'"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition-all"
                  >
                    Suivant
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Formulaire de création -->
        <Transition name="slide-down">
          <form v-if="showForm" @submit.prevent="createUser" class="space-y-6" ref="formRef">
          <!-- En-tête du formulaire -->
          <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-blue-600 px-6 py-4">
              <div class="flex items-center gap-3 text-white">
                <UserPlus class="w-6 h-6" />
                <div>
                  <h2 class="text-xl font-bold">{{ editingUserId ? 'Modifier l\'utilisateur' : 'Créer un nouvel utilisateur' }}</h2>
                  <p class="text-sm text-white/80">{{ editingUserId ? 'Mettez à jour les informations de l\'utilisateur' : 'Remplissez les informations ci-dessous pour créer un nouveau compte' }}</p>
                </div>
              </div>
            </div>

            <div class="p-6 space-y-6">
              <!-- Section 1: Informations de base -->
              <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="flex items-center gap-3 mb-5">
                  <div class="w-10 h-10 rounded-lg bg-gabon-green-100 flex items-center justify-center">
                    <User class="w-5 h-5 text-gabon-green-600" />
                  </div>
                  <div>
                    <h3 class="font-bold text-gray-900">Informations de base</h3>
                    <p class="text-sm text-gray-600">Identifiant et nom complet</p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      <div class="flex items-center gap-2">
                        <User class="w-4 h-4 text-gray-500" />
                        <span>Nom d'utilisateur <span class="text-red-500">*</span></span>
                      </div>
                    </label>
                    <input
                      v-model="form.username"
                      type="text"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all"
                      placeholder="ex: jdupont"
                      required
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      <div class="flex items-center gap-2">
                        <UserCheck class="w-4 h-4 text-gray-500" />
                        <span>Nom complet <span class="text-red-500">*</span></span>
                      </div>
                    </label>
                    <input
                      v-model="form.nom_complet"
                      type="text"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all"
                      placeholder="ex: Jean Dupont"
                      required
                    >
                  </div>
                </div>
              </div>

              <!-- Section 2: Email et Sécurité -->
              <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="flex items-center gap-3 mb-5">
                  <div class="w-10 h-10 rounded-lg bg-gabon-blue-100 flex items-center justify-center">
                    <Lock class="w-5 h-5 text-gabon-blue-600" />
                  </div>
                  <div>
                    <h3 class="font-bold text-gray-900">Email et Sécurité</h3>
                    <p class="text-sm text-gray-600">Coordonnées et mot de passe</p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      <div class="flex items-center gap-2">
                        <Mail class="w-4 h-4 text-gray-500" />
                        <span>Adresse email <span class="text-red-500">*</span></span>
                      </div>
                    </label>
                    <input
                      v-model="form.email"
                      type="email"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition-all"
                      placeholder="ex: jean.dupont@gabon.ga"
                      required
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      <div class="flex items-center gap-2">
                        <Key class="w-4 h-4 text-gray-500" />
                        <span>Mot de passe <span class="text-red-500">*</span></span>
                      </div>
                    </label>
                    <input
                      v-model="form.password"
                      type="password"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent transition-all"
                      placeholder="••••••••"
                      :required="!editingUserId"
                    >
                    <p v-if="editingUserId" class="text-xs text-gray-500 mt-1">Laissez vide pour conserver le mot de passe actuel</p>
                  </div>
                </div>
              </div>

              <!-- Section 3: Rôle -->
              <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="flex items-center gap-3 mb-5">
                  <div class="w-10 h-10 rounded-lg bg-gabon-yellow-100 flex items-center justify-center">
                    <Shield class="w-5 h-5 text-gabon-yellow-600" />
                  </div>
                  <div>
                    <h3 class="font-bold text-gray-900">Rôle système</h3>
                    <p class="text-sm text-gray-600">Niveau d'accès de l'utilisateur</p>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <div class="flex items-center gap-2">
                      <ShieldCheck class="w-4 h-4 text-gray-500" />
                      <span>Sélectionnez un rôle <span class="text-red-500">*</span></span>
                    </div>
                  </label>
                  <select
                    v-model="form.role_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gabon-yellow-500 focus:border-transparent transition-all font-medium"
                    required
                  >
                    <option value="" disabled>-- Choisir un rôle --</option>
                    <option v-for="role in roles" :key="role.id" :value="role.id">
                      {{ role.role_name }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Section 4: Fonctions autorisées -->
              <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="flex items-center gap-3 mb-5">
                  <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <Layers class="w-5 h-5 text-purple-600" />
                  </div>
                  <div>
                    <h3 class="font-bold text-gray-900">Fonctions autorisées</h3>
                    <p class="text-sm text-gray-600">Modules accessibles par l'utilisateur</p>
                  </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                  <label
                    v-for="fonction in fonctions"
                    :key="fonction.id"
                    class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-gabon-green-400 hover:bg-white"
                    :class="form.fonctions.includes(fonction.id) ? 'border-gabon-green-500 bg-gabon-green-50' : 'border-gray-200 bg-white'"
                  >
                    <input
                      v-model="form.fonctions"
                      :value="fonction.id"
                      type="checkbox"
                      class="w-5 h-5 text-gabon-green-600 rounded focus:ring-gabon-green-500"
                    >
                    <span class="font-medium text-gray-900">{{ fonction.fonction_name }}</span>
                    <Check v-if="form.fonctions.includes(fonction.id)" class="w-5 h-5 text-gabon-green-600 absolute top-2 right-2" />
                  </label>
                </div>
              </div>

              <!-- Section 5: Sous-fonctions et niveaux -->
              <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="flex items-center gap-3 mb-5">
                  <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <Settings class="w-5 h-5 text-indigo-600" />
                  </div>
                  <div>
                    <h3 class="font-bold text-gray-900">Sous-fonctions et niveaux d'accès</h3>
                    <p class="text-sm text-gray-600">Permissions détaillées par module</p>
                  </div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 px-4 py-3 rounded-lg mb-4">
                  <div class="flex items-start gap-2">
                    <Info class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                    <p class="text-sm text-blue-800">
                      Le niveau (lecture seule / modification) ne s'applique qu'aux rôles <strong>Assistant</strong> et <strong>Gestionnaire</strong>.
                      Les <strong>Administrateur</strong> et <strong>Super Administrateur</strong> ont toujours un accès complet.
                    </p>
                  </div>
                </div>

                <div class="space-y-3">
                  <div
                    v-for="sf in filteredSousfonctions"
                    :key="sf.id"
                    class="flex items-center justify-between gap-4 p-4 bg-white border border-gray-200 rounded-lg hover:border-gabon-blue-400 transition-all"
                  >
                    <label class="flex items-center gap-3 flex-1 cursor-pointer">
                      <input
                        v-model="form.sousfonctionIds"
                        :value="sf.id"
                        type="checkbox"
                        class="w-5 h-5 text-gabon-blue-600 rounded focus:ring-gabon-blue-500"
                      >
                      <div>
                        <div class="font-medium text-gray-900">{{ sf.sousfonction_name }}</div>
                        <div class="text-xs text-gray-500">Module: {{ sf.fonction_name }}</div>
                      </div>
                    </label>

                    <select
                      v-if="form.sousfonctionIds.includes(sf.id)"
                      v-model="form.niveaux[sf.id]"
                      class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-gabon-blue-500 focus:border-transparent"
                    >
                      <option value="lecture" class="flex items-center">
                        <Eye class="w-4 h-4 mr-2" /> Lecture seule
                      </option>
                      <option value="ecriture">
                        <Edit3 class="w-4 h-4 mr-2" /> Modification
                      </option>
                    </select>
                  </div>

                  <div v-if="filteredSousfonctions.length === 0" class="text-center py-8">
                    <AlertCircle class="w-10 h-10 text-gray-400 mx-auto mb-2" />
                    <p class="text-gray-500 text-sm">Sélectionnez d'abord une ou plusieurs fonctions ci-dessus</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="flex gap-4">
            <button
              v-if="editingUserId"
              type="button"
              @click="cancelEdit"
              class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all"
            >
              <X class="w-5 h-5" />
              <span>Annuler</span>
            </button>
            <button
              type="submit"
              class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-gabon-green-600 to-gabon-blue-600 hover:from-gabon-green-700 hover:to-gabon-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all"
            >
              <Save class="w-5 h-5" />
              <span>{{ editingUserId ? 'Mettre à jour l\'utilisateur' : 'Créer l\'utilisateur' }}</span>
            </button>
          </div>
        </form>
      </Transition>
    </template>
    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
import {
  Users, User, UserPlus, UserCheck, UserX,
  Mail, Lock, Key, Shield, ShieldCheck,
  Layers, Settings, Info, AlertCircle,
  Edit2, Edit3, Trash2, Eye, Check, X, Save
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth'
})

const config = useRuntimeConfig()
const authStore = useAuthStore()
const { $swal } = useNuxtApp()

const users = ref([])
const roles = ref([])
const fonctions = ref([])
const sousfonctions = ref([])
const loading = ref(true)
const error = ref('')

const editingUserId = ref<number | null>(null)
const showForm = ref(false)
const formRef = ref(null)

// Pagination
const currentPage = ref(1)
const perPage = ref(10)

const form = reactive({
  username: '',
  nom_complet: '',
  email: '',
  password: '',
  role_id: '',
  fonctions: [] as number[],
  sousfonctionIds: [] as number[],
  niveaux: {} as Record<number, string>
})

const filteredSousfonctions = computed(() => {
  return sousfonctions.value.filter((sf: any) =>
    form.fonctions.includes(sf.fonction_id)
  )
})

// Pagination computeds
const totalPages = computed(() => Math.ceil(users.value.length / perPage.value))

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  const end = start + perPage.value
  return users.value.slice(start, end)
})

function roleBadgeClass(roleName: string) {
  const classes: Record<string, string> = {
    'Super Administrateur': 'bg-blue-200 text-blue-800',
    'Administrateur': 'bg-indigo-200 text-indigo-800',
    'Gestionnaire': 'bg-yellow-200 text-yellow-800',
    'Assistant': 'bg-green-200 text-green-800'
  }
  return classes[roleName] || 'bg-gray-200 text-gray-800'
}

function resetForm() {
  editingUserId.value = null
  Object.assign(form, {
    username: '',
    nom_complet: '',
    email: '',
    password: '',
    role_id: '',
    fonctions: [],
    sousfonctionIds: [],
    niveaux: {}
  })
}

async function loadData() {
  loading.value = true
  error.value = ''
  try {
    console.log('Chargement des données admin...')
    
    // Charger les utilisateurs
    const usersRes = await $fetch(`${config.public.apiBase}/admin/users`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    console.log('Utilisateurs:', usersRes)
    users.value = Array.isArray(usersRes) ? usersRes : []
    
    // Charger les rôles
    const rolesRes = await $fetch(`${config.public.apiBase}/admin/roles`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    console.log('Rôles:', rolesRes)
    roles.value = Array.isArray(rolesRes) ? rolesRes : []
    
    // Charger les fonctions
    const fonctionsRes = await $fetch(`${config.public.apiBase}/admin/fonctions`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    console.log('Fonctions:', fonctionsRes)
    fonctions.value = Array.isArray(fonctionsRes) ? fonctionsRes : []
    
    // Charger les sous-fonctions
    const sousfonctionsRes = await $fetch(`${config.public.apiBase}/admin/sousfonctions`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    console.log('Sous-fonctions:', sousfonctionsRes)
    sousfonctions.value = Array.isArray(sousfonctionsRes) ? sousfonctionsRes : []
    
  } catch (err: any) {
    console.error('Erreur chargement:', err)
    error.value = err.message || 'Erreur lors du chargement des données'
  } finally {
    loading.value = false
  }
}

function buildPayload() {
  return {
    username: form.username,
    nom_complet: form.nom_complet,
    email: form.email,
    password: form.password,
    role_id: form.role_id,
    fonctions: form.fonctions,
    sousfonctions: form.sousfonctionIds.map((id: number) => ({
      id,
      niveau: form.niveaux[id] || 'lecture'
    }))
  }
}

async function createUser() {
  // Validation
  if (form.fonctions.length === 0) {
    await $swal.fire({
      icon: 'warning',
      title: 'Attention',
      text: 'Sélectionnez au moins une fonction',
      confirmButtonColor: '#047857'
    })
    return
  }
  if (form.sousfonctionIds.length === 0) {
    await $swal.fire({
      icon: 'warning',
      title: 'Attention',
      text: 'Sélectionnez au moins une sous-fonction',
      confirmButtonColor: '#047857'
    })
    return
  }

  try {
    if (editingUserId.value) {
      await $fetch(`${config.public.apiBase}/admin/users/${editingUserId.value}`, {
        method: 'PUT',
        body: buildPayload(),
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      await $swal.fire({
        icon: 'success',
        title: 'Succès !',
        text: 'Utilisateur modifié avec succès',
        confirmButtonColor: '#047857',
        timer: 2000,
        timerProgressBar: true
      })
    } else {
      await $fetch(`${config.public.apiBase}/admin/users`, {
        method: 'POST',
        body: buildPayload(),
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      await $swal.fire({
        icon: 'success',
        title: 'Succès !',
        text: 'Utilisateur créé avec succès',
        confirmButtonColor: '#047857',
        timer: 2000,
        timerProgressBar: true
      })
    }

    currentPage.value = 1
    resetForm()
    showForm.value = false
    loadData()
  } catch (error: any) {
    console.error('Erreur:', error)
    await $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors de l\'enregistrement',
      confirmButtonColor: '#dc2626'
    })
  }
}

async function editUser(user: any) {
  try {
    const detail: any = await $fetch(`${config.public.apiBase}/admin/users/${user.id}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    editingUserId.value = detail.id
    form.username = detail.username
    form.nom_complet = detail.nom_complet
    form.email = detail.email
    form.password = ''
    form.role_id = detail.role_id
    form.fonctions = detail.fonctions
    form.sousfonctionIds = detail.sousfonctions.map((sf: any) => sf.id)
    form.niveaux = Object.fromEntries(detail.sousfonctions.map((sf: any) => [sf.id, sf.niveau]))

    showForm.value = true
    nextTick(() => {
      formRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    })
  } catch (error: any) {
    console.error('Erreur:', error)
    await $swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: error.data?.message || 'Erreur lors du chargement de l\'utilisateur',
      confirmButtonColor: '#dc2626'
    })
  }
}

function cancelEdit() {
  resetForm()
  showForm.value = false
}

function toggleFormVisibility() {
  showForm.value = !showForm.value
  if (showForm.value) {
    nextTick(() => {
      formRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    })
  } else {
    resetForm()
  }
}

async function deleteUser(user: any) {
  const result = await $swal.fire({
    icon: 'warning',
    title: 'Confirmer la suppression',
    html: `Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>${user.username}</strong> ?<br><small class="text-gray-600">Cette action est irréversible.</small>`,
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Oui, supprimer',
    cancelButtonText: 'Annuler',
    reverseButtons: true
  })

  if (result.isConfirmed) {
    try {
      await $fetch(`${config.public.apiBase}/admin/users/${user.id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })

      await $swal.fire({
        icon: 'success',
        title: 'Supprimé !',
        text: 'Utilisateur supprimé avec succès',
        confirmButtonColor: '#047857',
        timer: 2000,
        timerProgressBar: true
      })
      currentPage.value = 1
      loadData()
    } catch (error) {
      console.error('Erreur:', error)
      await $swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Erreur lors de la suppression',
        confirmButtonColor: '#dc2626'
      })
    }
  }
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
/* Animation slide-down pour le formulaire */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-20px);
  max-height: 0;
}

.slide-down-enter-to {
  opacity: 1;
  transform: translateY(0);
  max-height: 5000px;
}

.slide-down-leave-from {
  opacity: 1;
  transform: translateY(0);
  max-height: 5000px;
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-20px);
  max-height: 0;
}
</style>
