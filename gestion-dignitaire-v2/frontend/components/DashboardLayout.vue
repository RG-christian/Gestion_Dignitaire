<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-800 text-white flex flex-col fixed h-full overflow-y-auto shadow-lg">
      <div class="p-4 border-b border-blue-700">
        <h1 class="text-xl font-bold">Gestion Dignitaires</h1>
      </div>
      
      <nav class="flex-1 overflow-y-auto p-4">
        <ul class="space-y-2">
          <!-- Tableau de Bord -->
          <li>
            <NuxtLink
              to="/"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              Tableau de Bord
            </NuxtLink>
          </li>
          
          <!-- Dignitaires -->
          <li>
            <NuxtLink
              to="/dignitaires"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              Dignitaires
            </NuxtLink>
          </li>
          
          <!-- Postes -->
          <li>
            <NuxtLink
              to="/postes"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              Postes
            </NuxtLink>
          </li>
          
          <!-- Décorations -->
          <li>
            <NuxtLink
              to="/decorations"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
              </svg>
              Décorations
            </NuxtLink>
          </li>
          
          <!-- Nominations -->
          <li>
            <NuxtLink
              to="/nominations"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Nominations
            </NuxtLink>
          </li>
          
          <!-- Diplômes -->
          <li>
            <NuxtLink
              to="/diplomes"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
              </svg>
              Diplômes
            </NuxtLink>
          </li>
          
          <!-- Enfants -->
          <li>
            <NuxtLink
              to="/enfants"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
              Enfants
            </NuxtLink>
          </li>
          
          <!-- Expériences -->
          <li>
            <NuxtLink
              to="/experiences"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
              Expériences
            </NuxtLink>
          </li>
          
          <!-- Langues Parlées -->
          <li>
            <NuxtLink
              to="/langues"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
              </svg>
              Langues Parlées
            </NuxtLink>
          </li>
          
          <!-- Section Géographie -->
          <li class="pt-4 border-t border-blue-700">
            <p class="px-4 py-2 text-xs text-blue-300 uppercase">Géographie</p>
          </li>
          
          <!-- Pays -->
          <li>
            <NuxtLink
              to="/pays"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Pays
            </NuxtLink>
          </li>
          
          <!-- Régions -->
          <li>
            <NuxtLink
              to="/regions"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
              </svg>
              Régions
            </NuxtLink>
          </li>
          
          <!-- Villes -->
          <li>
            <NuxtLink
              to="/villes"
              class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700 transition"
              active-class="bg-blue-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              Villes
            </NuxtLink>
          </li>
        </ul>
      </nav>
      
      <!-- User Profile & Logout -->
      <div class="p-4 border-t border-blue-700">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
            <span class="text-lg font-bold">{{ userInitial }}</span>
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium">{{ authStore.user?.nom_complet || 'Admin' }}</p>
            <p class="text-xs text-blue-300">{{ authStore.user?.role_name || 'Administrateur' }}</p>
          </div>
        </div>
        <button
          @click="logout"
          class="flex items-center justify-center gap-2 w-full bg-red-600 px-4 py-2 rounded hover:bg-red-700 transition"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          Déconnexion
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 overflow-y-auto bg-gray-100">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()

const userInitial = computed(() => {
  const name = authStore.user?.nom_complet || 'A'
  return name.charAt(0).toUpperCase()
})

function logout() {
  if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
    authStore.logout()
  }
}
</script>
