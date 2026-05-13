<template>
  <div class="min-h-screen bg-gray-100 transition-all duration-300">
    <!-- Header -->
    <header class="bg-white text-gray-800 h-16 flex items-center px-3 sm:px-4 shadow-lg fixed top-0 left-0 right-0 z-20">
      <button @click="toggleSidebar" class="text-gray-800 focus:outline-none mr-3">
        <i class="fas fa-bars text-lg transition duration-200"></i>
      </button>
      <div class="flex items-center space-x-2">
        <i class="fas fa-crown text-lg text-blue-500"></i>
        <NuxtLink to="/" class="text-sm sm:text-base font-medium tracking-tight">
          Gestion Dignitaires
        </NuxtLink>
      </div>
      <nav class="ml-auto flex items-center space-x-2">
        <button @click="toggleTheme" class="text-gray-800 focus:outline-none transition duration-200">
          <i :class="isDark ? 'fas fa-sun' : 'fas fa-adjust'" class="text-lg"></i>
        </button>
        <div class="relative">
          <button @click="toggleProfileMenu" class="text-gray-800 focus:outline-none transition duration-200">
            <i class="fas fa-user-circle text-lg"></i>
          </button>
          <ul v-if="showProfileMenu" class="absolute right-0 top-full mt-2 bg-white shadow-lg rounded py-1 min-w-[10rem] z-30">
            <li>
              <a href="#" class="block px-3 py-1.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                Profil
              </a>
            </li>
            <li>
              <button @click="logout" class="w-full text-left block px-3 py-1.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                Déconnexion
              </button>
            </li>
          </ul>
          <NuxtLink 
            v-if="authStore.user?.role_name === 'Superadmin'"
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
        :class="[
          'bg-white text-gray-800 w-64 min-h-[calc(100vh-64px)] fixed top-16 left-0 shadow-md transition-all duration-300 z-10',
          isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]"
      >
        <div class="pt-6 px-3 w-full h-full flex flex-col">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-medium text-gray-800 px-2">Menu</h2>
            <button @click="toggleSidebar" class="text-gray-800 focus:outline-none lg:hidden">
              <i class="fas fa-times text-lg"></i>
            </button>
          </div>
          <div class="flex-1 overflow-y-auto sidebar">
            <ul class="space-y-1">
              <!-- Tableau de Bord -->
              <li>
                <NuxtLink
                  to="/"
                  class="flex items-center p-2 rounded-md text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition duration-200"
                >
                  <i class="fas fa-tachometer-alt w-5 text-blue-500"></i>
                  <span class="ml-2 text-sm">Tableau</span>
                </NuxtLink>
              </li>

              <!-- Menus dynamiques basés sur les droits d'accès -->
              <li v-for="fonction in fonctionsAvecSousfonctions" :key="fonction.id" class="relative">
                <button
                  @click="toggleDropdown(fonction.fonction_name)"
                  class="w-full flex items-center p-2 rounded-md text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition duration-200"
                >
                  <i :class="['fas', getIcon(fonction.fonction_name), 'w-5', 'text-blue-500']"></i>
                  <span class="ml-2 text-sm">{{ fonction.fonction_name }}</span>
                  <i :class="openDropdown === fonction.fonction_name ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas ml-auto text-xs"></i>
                </button>
                <ul v-show="openDropdown === fonction.fonction_name" class="dropdown-content bg-gray-50 pl-3 mt-1 space-y-1 rounded">
                  <li v-for="sf in getSousFonctions(fonction.id)" :key="sf.id">
                    <NuxtLink
                      :to="getRouteForSousfonction(sf.sousfonction_name)"
                      class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 text-sm rounded"
                    >
                      {{ sf.sousfonction_name }}
                    </NuxtLink>
                  </li>
                </ul>
              </li>
            </ul>
            <div class="mt-auto pt-4 border-t border-gray-200">
              <p class="text-gray-500 text-xs">
                Vous êtes connecté en tant que <b>{{ authStore.user?.role_name || 'Administrateur' }}</b>.
              </p>
              <p class="font-medium text-gray-800 text-sm">
                Bienvenue {{ authStore.user?.nom_complet || 'Admin' }} !
              </p>
              <button
                @click="logout"
                class="mt-2 inline-block px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-200 text-sm"
              >
                Déconnexion
              </button>
            </div>
          </div>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto bg-gray-100 transition-all duration-300 lg:ml-64">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const isSidebarOpen = ref(false)
const isDark = ref(false)
const showProfileMenu = ref(false)
const openDropdown = ref<string | null>(null)

// Récupérer les fonctions et sous-fonctions depuis le store
const fonctions = computed(() => authStore.user?.fonctions || [])
const sousfonctions = computed(() => authStore.user?.sousfonctions || [])

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
    'Récomp. & Rec.': 'fa-medal'
  }
  return icons[fonctionName] || 'fa-star'
}

function getSousFonctions(fonctionId: number) {
  return sousfonctions.value.filter((sf: any) => sf.fonction_id === fonctionId)
}

function getRouteForSousfonction(name: string): string {
  const routes: Record<string, string> = {
    'Dignitaire': '/dignitaires',
    'Enfant': '/enfants',
    'Poste': '/postes',
    'Diplôme': '/diplomes',
    'Diplome': '/diplomes',
    'Expérience': '/experiences',
    'Experience': '/experiences',
    'Langues': '/langues-parlees',
    'Pays': '/pays',
    'Ville': '/villes',
    'Nomination': '/nominations',
    'Décoration': '/decorations',
    'Decoration': '/decorations',
    'Structure': '/structures',
    'Région': '/regions',
    'Region': '/regions'
  }
  return routes[name] || `/${name.toLowerCase()}`
}

function toggleSidebar() {
  isSidebarOpen.value = !isSidebarOpen.value
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

function logout() {
  if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
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
  })
})
</script>

<style scoped>
.sidebar {
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: #6b7280 #ffffff;
}

.sidebar::-webkit-scrollbar {
  width: 6px;
}

.sidebar::-webkit-scrollbar-track {
  background: #ffffff;
}

.sidebar::-webkit-scrollbar-thumb {
  background-color: #6b7280;
  border-radius: 3px;
  border: 2px solid #ffffff;
}

.dropdown-content {
  overflow: hidden;
  transition: all 0.3s ease-in-out;
}
</style>
