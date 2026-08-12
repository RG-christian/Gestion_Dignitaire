<template>
  <DashboardLayout>
    <div class="p-6">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        <p class="text-sm text-gray-600 mt-1">
          Suivez les mises à jour des candidatures et les actions des candidats
        </p>
      </div>

      <!-- Filtres -->
      <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
          <label class="text-sm font-medium text-gray-700">Filtrer :</label>
          <button
            @click="filtreNonLues = false"
            :class="[
              'px-4 py-2 text-sm rounded-lg transition-colors',
              !filtreNonLues 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Toutes
          </button>
          <button
            @click="filtreNonLues = true"
            :class="[
              'px-4 py-2 text-sm rounded-lg transition-colors',
              filtreNonLues 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Non lues ({{ countNonLues }})
          </button>
        </div>

        <div class="ml-auto">
          <button
            v-if="countNonLues > 0"
            @click="marquerToutesLues"
            class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
          >
            <i class="fas fa-check-double mr-2"></i>Tout marquer comme lu
          </button>
        </div>
      </div>

      <!-- Liste des notifications -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div v-if="loading" class="p-12 text-center text-gray-500">
          <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
          <p>Chargement des notifications...</p>
        </div>

        <div v-else-if="notifications.length === 0" class="p-12 text-center text-gray-400">
          <i class="fas fa-bell-slash text-4xl mb-3"></i>
          <p class="text-lg font-medium">Aucune notification</p>
          <p class="text-sm mt-1">
            {{ filtreNonLues ? 'Toutes vos notifications ont été lues' : 'Vous n\'avez aucune notification pour le moment' }}
          </p>
        </div>

        <ul v-else class="divide-y divide-gray-100">
          <li
            v-for="notif in notifications"
            :key="notif.id"
            class="hover:bg-gray-50 transition-colors cursor-pointer"
            :class="{ 'bg-blue-50': !notif.lu }"
            @click="handleNotificationClick(notif)"
          >
            <div class="flex items-start gap-4 p-4">
              <!-- Icône selon le type -->
              <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center" :class="getTypeClass(notif.type)">
                <i :class="getTypeIcon(notif.type)" class="text-lg"></i>
              </div>

              <!-- Contenu -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4">
                  <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900" :class="{ 'font-bold': !notif.lu }">
                      {{ notif.titre }}
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                      {{ notif.message }}
                    </p>
                    <div class="flex items-center gap-4 mt-2">
                      <span class="text-xs text-gray-400">
                        <i class="far fa-clock mr-1"></i>{{ formatDateTime(notif.created_at) }}
                      </span>
                      <span v-if="notif.candidat_nom" class="text-xs text-gray-500">
                        <i class="far fa-user mr-1"></i>{{ notif.candidat_nom }}
                      </span>
                      <span class="text-xs px-2 py-1 rounded-full" :class="getTypeBadgeClass(notif.type)">
                        {{ getTypeLabel(notif.type) }}
                      </span>
                    </div>
                  </div>

                  <!-- Indicateur non lu + bouton marquer lu -->
                  <div class="flex flex-col items-end gap-2">
                    <div v-if="!notif.lu" class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <button
                      v-if="!notif.lu"
                      @click.stop="marquerLue(notif.id)"
                      class="text-xs text-blue-600 hover:text-blue-800"
                      title="Marquer comme lu"
                    >
                      <i class="fas fa-check"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </li>
        </ul>

        <!-- Pagination -->
        <div v-if="pagination && pagination.last_page > 1" class="border-t border-gray-100 p-4 flex items-center justify-between">
          <p class="text-sm text-gray-600">
            Affichage de {{ pagination.from }} à {{ pagination.to }} sur {{ pagination.total }} notifications
          </p>
          <div class="flex items-center gap-2">
            <button
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="px-3 py-1 text-sm border rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Précédent
            </button>
            <span class="text-sm text-gray-600">
              Page {{ pagination.current_page }} / {{ pagination.last_page }}
            </span>
            <button
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="px-3 py-1 text-sm border rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Suivant
            </button>
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

const config = useRuntimeConfig()
const authStore = useAuthStore()
const toast = useToast()

const loading = ref(false)
const notifications = ref([])
const pagination = ref(null)
const filtreNonLues = ref(false)
const countNonLues = ref(0)
const currentPage = ref(1)

// Charger les notifications
async function loadNotifications() {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: 20
    })
    
    if (filtreNonLues.value) {
      params.append('non_lues', 'true')
    }

    const response = await $fetch(`${config.public.apiBase}/admin/notifications?${params}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    
    notifications.value = response.data || []
    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      from: response.from,
      to: response.to,
      total: response.total
    }
  } catch (error) {
    console.error('Erreur chargement notifications:', error)
  } finally {
    loading.value = false
  }
}

// Charger le compteur de non lues
async function loadCount() {
  try {
    const response = await $fetch(`${config.public.apiBase}/admin/notifications/count`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    countNonLues.value = response.count || 0
  } catch (error) {
    console.error('Erreur chargement compteur:', error)
  }
}

// Marquer une notification comme lue
async function marquerLue(id) {
  try {
    await $fetch(`${config.public.apiBase}/admin/notifications/${id}/lue`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    toast.success('Notification marquée comme lue')
    await loadCount()
    await loadNotifications()
  } catch (error) {
    console.error('Erreur marquage notification:', error)
  }
}

// Marquer toutes comme lues
async function marquerToutesLues() {
  const { $swal } = useNuxtApp()
  const result = await $swal.fire({
    title: 'Marquer toutes les notifications comme lues ?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Oui',
    cancelButtonText: 'Annuler'
  })
  
  if (!result.isConfirmed) return

  try {
    await $fetch(`${config.public.apiBase}/admin/notifications/toutes-lues`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    toast.success('Toutes les notifications ont été marquées comme lues')
    await loadCount()
    await loadNotifications()
  } catch (error) {
    console.error('Erreur marquage toutes lues:', error)
  }
}

// Clic sur une notification
async function handleNotificationClick(notif) {
  // Marquer comme lue si non lue
  if (!notif.lu) {
    await marquerLue(notif.id)
  }

  // Naviguer vers la candidature
  if (notif.candidat_id) {
    navigateTo(`/admin/candidatures/${notif.candidat_id}`)
  }
}

// Changer de page
function changePage(page) {
  currentPage.value = page
  loadNotifications()
}

// Classes selon le type
function getTypeClass(type) {
  const classes = {
    nouveau_document: 'bg-blue-100 text-blue-600',
    nouveau_diplome: 'bg-purple-100 text-purple-600',
    nouvelle_experience: 'bg-green-100 text-green-600',
    modification_profil: 'bg-orange-100 text-orange-600'
  }
  return classes[type] || 'bg-gray-100 text-gray-600'
}

function getTypeBadgeClass(type) {
  const classes = {
    nouveau_document: 'bg-blue-100 text-blue-700',
    nouveau_diplome: 'bg-purple-100 text-purple-700',
    nouvelle_experience: 'bg-green-100 text-green-700',
    modification_profil: 'bg-orange-100 text-orange-700'
  }
  return classes[type] || 'bg-gray-100 text-gray-700'
}

function getTypeIcon(type) {
  const icons = {
    nouveau_document: 'fas fa-file',
    nouveau_diplome: 'fas fa-graduation-cap',
    nouvelle_experience: 'fas fa-briefcase',
    modification_profil: 'fas fa-user-edit'
  }
  return icons[type] || 'fas fa-bell'
}

function getTypeLabel(type) {
  const labels = {
    nouveau_document: 'Document',
    nouveau_diplome: 'Diplôme',
    nouvelle_experience: 'Expérience',
    modification_profil: 'Profil'
  }
  return labels[type] || type
}

function formatDateTime(dateStr) {
  return new Date(dateStr).toLocaleString('fr-FR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Watch sur le filtre
watch(filtreNonLues, () => {
  currentPage.value = 1
  loadNotifications()
})

// Charger au montage
onMounted(() => {
  loadCount()
  loadNotifications()
})
</script>
