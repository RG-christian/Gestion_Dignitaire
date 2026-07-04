<template>
  <DashboardLayout>
    <div class="max-w-5xl mx-auto p-6">
      <!-- Message d'erreur -->
      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ error }}
      </div>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <template v-else>
        <!-- Liste des utilisateurs existants -->
        <div class="bg-white p-6 rounded-lg shadow mb-10">
          <h2 class="text-xl font-bold mb-4">Utilisateurs existants ({{ users.length }})</h2>
          <div class="overflow-x-auto">
            <table v-if="users.length > 0" class="min-w-full text-sm">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-4 py-2 text-left">Nom d'utilisateur</th>
                  <th class="px-4 py-2 text-left">Nom complet</th>
                  <th class="px-4 py-2 text-left">Email</th>
                  <th class="px-4 py-2 text-left">Rôle</th>
                  <th class="px-4 py-2 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id" class="border-b">
                  <td class="px-4 py-2">{{ user.username }}</td>
                  <td class="px-4 py-2">{{ user.nom_complet }}</td>
                  <td class="px-4 py-2">{{ user.email }}</td>
                  <td class="px-4 py-2">
                    <span :class="roleBadgeClass(user.role_name)" class="px-2 py-1 rounded text-xs">
                      {{ user.role_name }}
                    </span>
                  </td>
                  <td class="px-4 py-2 text-center">
                    <button @click="editUser(user)" class="text-blue-600 hover:text-blue-800 mr-2">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button @click="deleteUser(user)" class="text-red-600 hover:text-red-800">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-else class="text-center py-8 text-gray-500">Aucun utilisateur trouvé</p>
          </div>
        </div>

        <!-- Formulaire de création -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-xl font-semibold mb-4">Créer un nouvel utilisateur</h2>
        <form @submit.prevent="createUser">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block mb-2 text-sm">Nom d'utilisateur</label>
              <input v-model="form.username" type="text" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div>
              <label class="block mb-2 text-sm">Nom complet</label>
              <input v-model="form.nom_complet" type="text" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div>
              <label class="block mb-2 text-sm">Email</label>
              <input v-model="form.email" type="email" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div>
              <label class="block mb-2 text-sm">Mot de passe</label>
              <input v-model="form.password" type="password" class="w-full px-3 py-2 border rounded" required>
            </div>
          </div>
          
          <div class="mt-4">
            <label class="block mb-2 text-sm">Rôle</label>
            <select v-model="form.role_id" class="w-full px-3 py-2 border rounded" required>
              <option v-for="role in roles" :key="role.id" :value="role.id">
                {{ role.role_name }}
              </option>
            </select>
          </div>

          <div class="mt-4">
            <label class="block mb-2 text-sm">Fonctions autorisées</label>
            <div class="flex flex-wrap gap-3">
              <label v-for="fonction in fonctions" :key="fonction.id" class="flex items-center">
                <input v-model="form.fonctions" :value="fonction.id" type="checkbox" class="mr-2">
                {{ fonction.fonction_name }}
              </label>
            </div>
          </div>

          <div class="mt-4">
            <label class="block mb-2 text-sm">Sous-fonctions autorisées</label>
            <p class="text-xs text-gray-500 mb-2">Le niveau (lecture seule / modification) ne s'applique qu'aux rôles Assistant et Gestionnaire — Administrateur et Super Administrateur ont toujours un accès complet.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
              <div v-for="sf in filteredSousfonctions" :key="sf.id" class="flex items-center justify-between border rounded px-3 py-2">
                <label class="flex items-center">
                  <input v-model="form.sousfonctionIds" :value="sf.id" type="checkbox" class="mr-2">
                  {{ sf.sousfonction_name }}
                  <span class="text-xs text-gray-400 ml-1">({{ sf.fonction_name }})</span>
                </label>
                <select v-if="form.sousfonctionIds.includes(sf.id)" v-model="form.niveaux[sf.id]" class="text-xs border rounded px-2 py-1">
                  <option value="lecture">Lecture seule</option>
                  <option value="ecriture">Modification</option>
                </select>
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button type="button" v-if="editingUserId" @click="cancelEdit" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded">
              Annuler
            </button>
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
              {{ editingUserId ? 'Modifier l\'utilisateur' : 'Créer l\'utilisateur' }}
            </button>
          </div>
        </form>
      </div>
    </template>
    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const config = useRuntimeConfig()
const authStore = useAuthStore()

const users = ref([])
const roles = ref([])
const fonctions = ref([])
const sousfonctions = ref([])
const loading = ref(true)
const error = ref('')

const editingUserId = ref<number | null>(null)

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
    alert('Sélectionnez au moins une fonction')
    return
  }
  if (form.sousfonctionIds.length === 0) {
    alert('Sélectionnez au moins une sous-fonction')
    return
  }

  try {
    if (editingUserId.value) {
      await $fetch(`${config.public.apiBase}/admin/users/${editingUserId.value}`, {
        method: 'PUT',
        body: buildPayload(),
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      alert('Utilisateur modifié avec succès')
    } else {
      await $fetch(`${config.public.apiBase}/admin/users`, {
        method: 'POST',
        body: buildPayload(),
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      alert('Utilisateur créé avec succès')
    }

    resetForm()
    loadData()
  } catch (error: any) {
    console.error('Erreur:', error)
    alert(error.data?.message || 'Erreur lors de l\'enregistrement')
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

    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })
  } catch (error: any) {
    console.error('Erreur:', error)
    alert(error.data?.message || 'Erreur lors du chargement de l\'utilisateur')
  }
}

function cancelEdit() {
  resetForm()
}

async function deleteUser(user: any) {
  if (confirm(`Supprimer l'utilisateur ${user.username} ?`)) {
    try {
      await $fetch(`${config.public.apiBase}/admin/users/${user.id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      
      alert('Utilisateur supprimé avec succès')
      loadData()
    } catch (error) {
      console.error('Erreur:', error)
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(() => {
  loadData()
})
</script>
