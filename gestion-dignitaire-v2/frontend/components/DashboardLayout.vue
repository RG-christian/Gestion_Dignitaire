<template>
  <div class="min-h-screen bg-gray-100 transition-all duration-300">
    <!-- Header -->
    <header class="bg-white text-gray-800 h-16 flex items-center px-3 sm:px-4 shadow-lg fixed top-0 left-0 right-0 z-20">
      <button @click="toggleSidebarCollapse" class="text-gray-800 focus:outline-none mr-3 hover:bg-gray-100 p-2 rounded-lg transition">
        <i :class="isSidebarCollapsed ? 'fas fa-bars' : 'fas fa-times'" class="text-lg transition duration-200"></i>
      </button>
      <div class="flex items-center space-x-2">
        <i class="fas fa-crown text-lg text-blue-500"></i>
        <NuxtLink to="/dashboard" class="text-sm sm:text-base font-medium tracking-tight hidden sm:block">
          Gestion Dignitaires
        </NuxtLink>
      </div>

      <!-- Recherche globale -->
      <div ref="globalSearchRef" class="relative ml-4 sm:ml-8 w-full max-w-xs sm:max-w-sm">
        <div class="relative">
          <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
          <input
            v-model="globalSearchQuery"
            @input="onGlobalSearchInput"
            @focus="globalSearchOpen = globalSearchResults.length > 0"
            @keydown.esc="closeGlobalSearch"
            type="text"
            placeholder="Rechercher un dignitaire, une nomination, un diplôme..."
            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
          >
        </div>
        <div
          v-if="globalSearchOpen"
          class="absolute left-0 mt-1 w-full sm:w-96 bg-white rounded-lg shadow-2xl border border-gray-100 max-h-96 overflow-y-auto z-40"
        >
          <div v-if="globalSearchLoading" class="p-4 text-center text-sm text-gray-500">
            <i class="fas fa-spinner fa-spin mr-2"></i>Recherche…
          </div>
          <div v-else-if="globalSearchResults.length === 0" class="p-4 text-center text-sm text-gray-500">
            Aucun résultat pour « {{ globalSearchQuery }} »
          </div>
          <ul v-else class="py-1">
            <li v-for="(r, i) in globalSearchResults" :key="r.type + '-' + r.id + '-' + i">
              <component
                :is="r.url ? 'NuxtLink' : 'div'"
                :to="r.url || undefined"
                @click="r.url && closeGlobalSearch()"
                class="flex items-center justify-between px-4 py-2 text-sm"
                :class="r.url ? 'hover:bg-blue-50 cursor-pointer' : 'opacity-60 cursor-default'"
              >
                <span>
                  <span class="font-medium text-gray-800">{{ r.label }}</span>
                  <span v-if="r.sublabel" class="text-gray-400"> — {{ r.sublabel }}</span>
                </span>
                <span class="text-xs uppercase tracking-wide text-blue-500 ml-2 whitespace-nowrap">{{ r.type_label }}</span>
              </component>
            </li>
          </ul>
        </div>
      </div>

      <nav class="ml-auto flex items-center space-x-2">
        <button @click="toggleTheme" class="text-gray-800 focus:outline-none transition duration-200 hover:bg-gray-100 p-2 rounded-lg">
          <i :class="isDark ? 'fas fa-sun' : 'fas fa-adjust'" class="text-lg"></i>
        </button>
        <div class="relative">
          <button @click="toggleProfileMenu" class="text-gray-800 focus:outline-none transition duration-200 hover:bg-gray-100 p-2 rounded-lg">
            <i class="fas fa-user-circle text-lg"></i>
          </button>
          <ul v-if="showProfileMenu" class="absolute right-0 top-full mt-2 bg-white shadow-lg rounded py-1 min-w-[10rem] z-30">
            <li>
              <NuxtLink to="/profil" class="block px-3 py-1.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                <i class="fas fa-user mr-2"></i>Profil
              </NuxtLink>
            </li>
            <li>
              <button @click="logout" class="w-full text-left block px-3 py-1.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
              </button>
            </li>
          </ul>
          <NuxtLink 
            v-if="permissions.estSuperAdmin.value"
            to="/admin/create"
            class="ml-4 px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm"
          >
            <i class="fas fa-user-plus"></i> Ajouter un utilisateur
          </NuxtLink>
        </div>
      </nav>
    </header>

    <!-- Layout principal -->
    <div class="flex pt-16">
      <!-- Sidebar -->
      <aside 
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        :class="[
          'bg-white text-gray-800 min-h-[calc(100vh-64px)] fixed top-16 left-0 shadow-md transition-all duration-300 z-10',
          isSidebarCollapsed ? 'w-16' : 'w-72'
        ]"
      >
        <div class="pt-4 px-2 w-full h-full flex flex-col">
          <!-- Header avec bouton toggle -->
          <div class="flex justify-between items-center mb-4 px-2">
            <h2 v-show="!isSidebarCollapsed" class="text-xs font-semibold text-gray-600 transition-opacity duration-200 uppercase tracking-wider">Menu Principal</h2>
            <button 
              @click="toggleSidebarCollapse" 
              class="text-gray-800 focus:outline-none hover:bg-gray-100 p-2 rounded-lg transition ml-auto"
              :title="isSidebarCollapsed ? 'Déplier le menu' : 'Replier le menu'"
            >
              <i :class="isSidebarCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'" class="text-xs"></i>
            </button>
          </div>

          <!-- Menu items -->
          <div class="flex-1 overflow-visible">
            <ul class="space-y-2">
              <!-- Tableau de Bord -->
              <li>
                <NuxtLink
                  to="/dashboard"
                  class="flex items-center p-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition duration-200 group relative"
                  :title="isSidebarCollapsed ? 'Tableau de Bord' : ''"
                >
                  <i class="fas fa-tachometer-alt w-5 text-base text-blue-500"></i>
                  <span v-show="!isSidebarCollapsed" class="ml-3 text-sm font-medium">Tableau de Bord</span>
                  <!-- Tooltip pour mode réduit -->
                  <div v-if="isSidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                    Tableau de Bord
                  </div>
                </NuxtLink>
              </li>


              <!-- Menus dynamiques basés sur les droits d'accès -->
              <li v-for="fonction in fonctionsAvecSousfonctions" :key="fonction.id" class="relative">
                <button
                  @click="toggleDropdown(fonction.fonction_name)"
                  class="w-full flex items-center p-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition duration-200 group relative"
                  :title="isSidebarCollapsed ? fonction.fonction_name : ''"
                >
                  <i :class="['fas', getIcon(fonction.fonction_name), 'w-5', 'text-base', 'text-blue-500']"></i>
                  <span v-show="!isSidebarCollapsed" class="ml-3 text-sm font-medium flex-1 text-left">{{ fonction.fonction_name }}</span>
                  <i v-show="!isSidebarCollapsed" :class="openDropdown === fonction.fonction_name ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas ml-auto text-xs"></i>
                </button>

                <!-- Sous-menu en mode étendu -->
                <ul 
                  v-show="openDropdown === fonction.fonction_name && !isSidebarCollapsed" 
                  class="dropdown-content bg-gray-50 pl-3 mt-1 space-y-1 rounded overflow-hidden"
                >
                  <!-- Sous-fonctions existantes -->
                  <li v-for="sf in getSousFonctions(fonction.id)" :key="sf.id">
                    <NuxtLink
                      :to="getRouteForSousfonction(sf.sousfonction_name)"
                      class="block px-3 py-1.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700 text-xs rounded transition"
                    >
                      {{ sf.sousfonction_name }}
                    </NuxtLink>
                  </li>
                </ul>

                <!-- Menu flottant en mode réduit -->
                <div 
                  v-if="isSidebarCollapsed && openDropdown === fonction.fonction_name"
                  class="fixed left-16 bg-white shadow-xl rounded-lg py-2 px-1 min-w-[200px] z-50 border border-gray-200"
                  :style="{ top: getDropdownPosition(fonction.id) }"
                >
                  <div class="px-3 py-1 text-[10px] font-semibold text-gray-500 border-b border-gray-200 mb-1">
                    {{ fonction.fonction_name }}
                  </div>
                  <!-- Sous-fonctions existantes -->
                  <NuxtLink
                    v-for="sf in getSousFonctions(fonction.id)"
                    :key="sf.id"
                    :to="getRouteForSousfonction(sf.sousfonction_name)"
                    class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-700 text-xs rounded transition"
                    @click="openDropdown = null"
                  >
                    {{ sf.sousfonction_name }}
                  </NuxtLink>
                </div>
              </li>
            </ul>

            <!-- Footer info utilisateur -->
            <div v-show="!isSidebarCollapsed" class="mt-auto pt-6 border-t border-gray-200">
              <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0">
                  <img
                    v-if="userPhotoUrl"
                    :src="userPhotoUrl"
                    class="w-10 h-10 rounded-full object-cover border border-gray-200"
                    alt="Photo de profil"
                  >
                  <div v-else class="w-10 h-10 bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ (authStore.user?.nom_complet || 'A').charAt(0).toUpperCase() }}
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-gray-800 text-sm truncate">
                    {{ authStore.user?.nom_complet || 'Administrateur' }}
                  </p>
                  <p class="text-xs text-gray-500 truncate">
                    {{ authStore.user?.email || 'admin@example.com' }}
                  </p>
                  <p class="text-xs text-gabon-green-600 font-medium mt-1">
                    {{ authStore.user?.role_name || 'Administrateur' }}
                  </p>
                </div>
              </div>
              <button
                @click="logout"
                class="mt-3 w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 text-sm font-medium"
              >
                <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
              </button>
            </div>

            <!-- Footer mode réduit -->
            <div v-show="isSidebarCollapsed" class="mt-auto pt-4 border-t border-gray-200 flex justify-center">
              <button
                @click="logout"
                class="p-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-200 group relative"
                title="Déconnexion"
              >
                <i class="fas fa-sign-out-alt"></i>
                <div class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                  Déconnexion
                </div>
              </button>
            </div>
          </div>
        </div>
      </aside>

      <!-- Main Content avec marge dynamique et pleine largeur -->
      <main 
        :class="[
          'flex-1 overflow-y-auto bg-gray-100 transition-all duration-300',
          isSidebarCollapsed ? 'ml-16' : 'ml-72'
        ]"
      >
        <div class="w-full">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const authStore = useAuthStore()
const permissions = usePermissions()
const config = useRuntimeConfig()
const { debounce } = useDebounce()
const isSidebarCollapsed = ref(false)
const isDark = ref(false)
const showProfileMenu = ref(false)
const openDropdown = ref<string | null>(null)
const hoverTimeout = ref<any>(null)

// Récupérer les fonctions et sous-fonctions depuis le store
const fonctions = computed(() => authStore.user?.fonctions || [])
const sousfonctions = computed(() => authStore.user?.sousfonctions || [])

const userPhotoUrl = computed(() => {
  const photo = authStore.user?.photo
  if (!photo) return null
  return `${config.public.apiBase.replace(/\/api\/?$/, '')}/storage/photos/${photo}`
})

// Filtrer les fonctions qui ont au moins une sous-fonction accessible
const fonctionsAvecSousfonctions = computed(() => {
  return fonctions.value.filter((fonction: any) => {
    const sousFonctionsDeCetteFonction = sousfonctions.value.filter(
      (sf: any) => sf.fonction_id === fonction.id
    )
    return sousFonctionsDeCetteFonction.length > 0
  })
})

function getIcon(fonctionName: string): string {
  const icons: Record<string, string> = {
    'Gest. Pers.': 'fa-users',
    'Éduc. & Qualif.': 'fa-graduation-cap',
    'Organisation': 'fa-building',
    'organisation': 'fa-building',
    'Parcours Pro.': 'fa-briefcase',
    'Langues': 'fa-language',
    'Géographie': 'fa-globe',
    'Récomp. & Rec.': 'fa-medal',
    'Rapports & Traçabilité': 'fa-chart-line'
  }
  return icons[fonctionName] || 'fa-star'
}

function getSousFonctions(fonctionId: number) {
  return sousfonctions.value.filter((sf: any) => {
    if (sf.fonction_id !== fonctionId) return false
    // Verrouillées derrière une garde supplémentaire côté backend (cf.
    // routes/api.php) même si la sous-fonction est attribuée — inutile de
    // proposer un lien qui renverra systématiquement une 403.
    if (sf.sousfonction_name === 'Paramètres (OTP)' && !permissions.estSuperAdmin.value) return false
    if (sf.sousfonction_name === 'Candidature' && !permissions.aAccesComplet.value) return false
    return true
  })
}

function getRouteForSousfonction(name: string): string {
  const routes: Record<string, string> = {
    'Dignitaire': '/dignitaires',
    'Enfant': '/enfants',
    'Conjoint': '/conjoints',
    'Affectation': '/affectations',
    'Candidature': '/admin/candidatures',
    'Poste': '/postes',
    'Entité': '/entites',
    'Entite': '/entites',
    'Diplôme': '/diplomes',
    'Diplome': '/diplomes',
    'Expérience': '/experiences',
    'Experience': '/experiences',
    'Langues': '/langues',
    'Pays': '/pays',
    'Ville': '/villes',
    'Nomination': '/nominations',
    'Décoration': '/decorations',
    'Decoration': '/decorations',
    'Structure': '/structures',
    'Région': '/regions',
    'Region': '/regions',
    'Rapports & Exports': '/admin/rapports',
    'Journal des actions': '/admin/audit-logs',
    'Paramètres (OTP)': '/admin/parametres'
  }
  return routes[name] || `/${name.toLowerCase()}`
}

function getDropdownPosition(fonctionId: number): string {
  // Calculer la position du menu flottant
  const index = fonctionsAvecSousfonctions.value.findIndex((f: any) => f.id === fonctionId)
  const topOffset = 64 + 80 + (index * 40) // header + padding + index * hauteur item
  return `${topOffset}px`
}

function toggleSidebarCollapse() {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
  // Fermer tous les dropdowns quand on réduit la sidebar
  if (isSidebarCollapsed.value) {
    openDropdown.value = null
  }
}

function handleMouseEnter() {
  // Déplier automatiquement au survol après 200ms
  if (isSidebarCollapsed.value) {
    hoverTimeout.value = setTimeout(() => {
      isSidebarCollapsed.value = false
    }, 200)
  }
}

function handleMouseLeave() {
  // Annuler le timer si la souris quitte avant 200ms
  if (hoverTimeout.value) {
    clearTimeout(hoverTimeout.value)
    hoverTimeout.value = null
  }
  // Replier automatiquement quand la souris quitte après 500ms
  hoverTimeout.value = setTimeout(() => {
    if (!isSidebarCollapsed.value) {
      isSidebarCollapsed.value = true
      openDropdown.value = null
    }
  }, 500)
}

function toggleTheme() {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}

function toggleProfileMenu() {
  showProfileMenu.value = !showProfileMenu.value
}

function toggleDropdown(name: string) {
  if (openDropdown.value === name) {
    openDropdown.value = null
  } else {
    openDropdown.value = name
  }
}

async function logout() {
  const { $swal } = useNuxtApp()
  const result = await $swal.fire({
    title: 'Déconnexion',
    text: 'Êtes-vous sûr de vouloir vous déconnecter ?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Oui, me déconnecter',
    cancelButtonText: 'Annuler'
  })
  
  if (result.isConfirmed) {
    authStore.logout()
  }
}

// Fermer le menu profil quand on clique ailleurs
onMounted(() => {
  document.addEventListener('click', (e) => {
    const target = e.target as HTMLElement
    if (!target.closest('.relative')) {
      showProfileMenu.value = false
    }
    if (globalSearchRef.value && !globalSearchRef.value.contains(target)) {
      globalSearchOpen.value = false
    }
  })
})

// ===== Recherche globale =====
const globalSearchRef = ref<HTMLElement | null>(null)
const globalSearchQuery = ref('')
const globalSearchResults = ref<any[]>([])
const globalSearchLoading = ref(false)
const globalSearchOpen = ref(false)

async function runGlobalSearch() {
  const q = globalSearchQuery.value.trim()
  if (q.length < 2) {
    globalSearchResults.value = []
    globalSearchOpen.value = false
    return
  }

  globalSearchLoading.value = true
  globalSearchOpen.value = true
  try {
    const response: any = await $fetch(`${config.public.apiBase}/search`, {
      params: { q },
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    globalSearchResults.value = response.results || []
  } catch (error) {
    console.error('Erreur recherche globale:', error)
    globalSearchResults.value = []
  } finally {
    globalSearchLoading.value = false
  }
}

const onGlobalSearchInput = debounce(runGlobalSearch, 350)

function closeGlobalSearch() {
  globalSearchOpen.value = false
}
</script>

<style scoped>
/* Pas de scroll - tout visible */
.sidebar {
  overflow: visible !important;
}

/* Pas de scroll en mode réduit */
aside:has(.w-16) .sidebar {
  overflow: visible !important;
}

.dropdown-content {
  overflow: hidden;
  transition: all 0.3s ease-in-out;
}

/* Animation pour les tooltips */
.group:hover .group-hover\:opacity-100 {
  opacity: 1;
}

/* Transition fluide pour la sidebar */
aside {
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Transition fluide pour le contenu principal */
main {
  transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Menu flottant en mode réduit */
.fixed.left-16 {
  animation: slideInRight 0.2s ease-out;
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Amélioration des boutons de menu */
aside button,
aside a {
  position: relative;
  overflow: visible;
}

/* Effet hover professionnel */
aside button:hover,
aside a:hover {
  transform: translateX(2px);
}

/* Icônes centrées en mode réduit */
aside.w-16 button,
aside.w-16 a {
  justify-content: center;
}
</style>
