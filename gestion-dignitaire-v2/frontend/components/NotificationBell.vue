<template>
  <div class="relative">
    <button
      @click="toggleNotifications"
      class="w-10 h-10 flex items-center justify-center rounded-full text-gray-500 hover:text-blue-600 hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-colors duration-200 relative"
      title="Notifications"
    >
      <i class="fas fa-bell text-base"></i>
      <!-- Badge de compteur -->
      <span
        v-if="count > 0"
        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[1.25rem] h-5 flex items-center justify-center px-1"
      >
        {{ count > 99 ? '99+' : count }}
      </span>
    </button>

    <!-- Dropdown des notifications -->
    <div
      v-if="isOpen"
      class="absolute right-0 top-full mt-2 w-96 bg-white shadow-2xl rounded-lg border border-gray-100 z-40 max-h-[32rem] flex flex-col"
    >
      <!-- Header -->
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
        <button
          v-if="count > 0"
          @click="marquerToutesLues"
          class="text-xs text-blue-600 hover:text-blue-800 font-medium"
        >
          Tout marquer comme lu
        </button>
      </div>

      <!-- Liste des notifications -->
      <div class="overflow-y-auto flex-1">
        <div v-if="loading" class="p-8 text-center text-gray-500">
          <i class="fas fa-spinner fa-spin mr-2"></i>Chargement...
        </div>

        <div v-else-if="notifications.length === 0" class="p-8 text-center text-gray-400">
          <i class="fas fa-bell-slash text-3xl mb-2"></i>
          <p class="text-sm">Aucune notification</p>
        </div>

        <ul v-else class="divide-y divide-gray-100">
          <li
            v-for="notif in notifications"
            :key="notif.id"
            class="px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer"
            :class="{ 'bg-blue-50': !notif.lu }"
            @click="handleNotificationClick(notif)"
          >
            <div class="flex items-start gap-3">
              <!-- Icône selon le type -->
              <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm" :class="getTypeClass(notif.type)">
                <i :class="getTypeIcon(notif.type)"></i>
              </div>

              <!-- Contenu -->
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900" :class="{ 'font-semibold': !notif.lu }">
                  {{ notif.titre }}
                </p>
                <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">
                  {{ notif.message }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                  {{ formatRelativeTime(notif.created_at) }}
                </p>
              </div>

              <!-- Indicateur non lu -->
              <div v-if="!notif.lu" class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full"></div>
            </div>
          </li>
        </ul>
      </div>

      <!-- Footer -->
      <div class="border-t border-gray-100 px-4 py-2">
        <NuxtLink
          to="/admin/notifications"
          @click="closeNotifications"
          class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium py-1"
        >
          Voir toutes les notifications
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()
const authStore = useAuthStore()

const isOpen = ref(false)
const loading = ref(false)
const count = ref(0)
const notifications = ref([])
let refreshInterval = null

// Charger le compteur de notifications non lues
async function loadCount() {
  try {
    const response = await $fetch(`${config.public.apiBase}/admin/notifications/count`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    count.value = response.count || 0
  } catch (error) {
    console.error('Erreur chargement compteur notifications:', error)
  }
}

// Charger les notifications (limitées aux 10 plus récentes)
async function loadNotifications() {
  loading.value = true
  try {
    const response = await $fetch(`${config.public.apiBase}/admin/notifications?per_page=10`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    notifications.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement notifications:', error)
  } finally {
    loading.value = false
  }
}

// Toggle dropdown
function toggleNotifications() {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    loadNotifications()
  }
}

function closeNotifications() {
  isOpen.value = false
}

// Marquer toutes comme lues
async function marquerToutesLues() {
  try {
    await $fetch(`${config.public.apiBase}/admin/notifications/toutes-lues`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
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
    try {
      await $fetch(`${config.public.apiBase}/admin/notifications/${notif.id}/lue`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      await loadCount()
      notif.lu = true
    } catch (error) {
      console.error('Erreur marquage notification lue:', error)
    }
  }

  // Naviguer vers la candidature correspondante
  if (notif.candidat_id) {
    navigateTo(`/admin/candidatures/${notif.candidat_id}`)
    closeNotifications()
  }
}

// Classes selon le type de notification
function getTypeClass(type) {
  const classes = {
    nouveau_document: 'bg-blue-100 text-blue-600',
    nouveau_diplome: 'bg-purple-100 text-purple-600',
    nouvelle_experience: 'bg-green-100 text-green-600',
    modification_profil: 'bg-orange-100 text-orange-600'
  }
  return classes[type] || 'bg-gray-100 text-gray-600'
}

// Icône selon le type de notification
function getTypeIcon(type) {
  const icons = {
    nouveau_document: 'fas fa-file',
    nouveau_diplome: 'fas fa-graduation-cap',
    nouvelle_experience: 'fas fa-briefcase',
    modification_profil: 'fas fa-user-edit'
  }
  return icons[type] || 'fas fa-bell'
}

// Formater le temps relatif
function formatRelativeTime(dateStr) {
  const date = new Date(dateStr)
  const now = new Date()
  const diffInSeconds = Math.floor((now - date) / 1000)

  if (diffInSeconds < 60) return 'À l\'instant'
  if (diffInSeconds < 3600) return `Il y a ${Math.floor(diffInSeconds / 60)} min`
  if (diffInSeconds < 86400) return `Il y a ${Math.floor(diffInSeconds / 3600)} h`
  if (diffInSeconds < 604800) return `Il y a ${Math.floor(diffInSeconds / 86400)} j`
  
  return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
}

// Charger le compteur au montage et toutes les 30 secondes
onMounted(() => {
  loadCount()
  refreshInterval = setInterval(loadCount, 30000) // 30 secondes
  
  // Fermer le dropdown si on clique ailleurs
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
  document.removeEventListener('click', handleClickOutside)
})

// Gérer les clics en dehors du composant
function handleClickOutside(event) {
  const component = event.target.closest('.relative')
  if (!component && isOpen.value) {
    closeNotifications()
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
