<template>
  <div class="min-h-screen bg-gray-50 w-full max-w-full">
    <!-- Navbar moderne -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
      <div class="px-6 py-4">
        <div class="flex items-center justify-between">
          <!-- Logo et titre -->
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-lg flex items-center justify-center shadow-md">
                <Building2 class="w-6 h-6 text-white" />
              </div>
              <div>
                <h1 class="text-lg font-bold text-gray-900">Gestion Dignitaires</h1>
                <p class="text-xs text-gray-500">Espace Candidat</p>
              </div>
            </div>
          </div>

          <!-- User info et actions -->
          <div class="flex items-center gap-4">
            <!-- Nom utilisateur / menu profil -->
            <div ref="profileMenuRef" class="relative">
              <button
                @click="showProfileMenu = !showProfileMenu"
                class="hidden md:flex items-center gap-3 px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors"
              >
                <div class="w-8 h-8 bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-full flex items-center justify-center overflow-hidden">
                  <img
                    v-if="candidat?.photo"
                    :src="siteRoot + '/storage/' + candidat.photo"
                    :alt="candidat?.nom_complet"
                    class="w-full h-full object-cover"
                  />
                  <span v-else class="text-sm font-bold text-white">{{ getInitials(candidat?.nom_complet) }}</span>
                </div>
                <div class="text-left">
                  <p class="text-sm font-semibold text-gray-900">{{ candidat?.nom_complet }}</p>
                  <p class="text-xs text-gray-500">{{ candidat?.email }}</p>
                </div>
                <ChevronDown class="w-4 h-4 text-gray-400" />
              </button>

              <div
                v-if="showProfileMenu"
                class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50"
              >
                <div class="flex items-center gap-3 px-4 py-4">
                  <div class="w-10 h-10 bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                    <img
                      v-if="candidat?.photo"
                      :src="siteRoot + '/storage/' + candidat.photo"
                      :alt="candidat?.nom_complet"
                      class="w-full h-full object-cover"
                    />
                    <span v-else class="text-sm font-bold text-white">{{ getInitials(candidat?.nom_complet) }}</span>
                  </div>
                  <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ candidat?.nom_complet }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ candidat?.email }}</p>
                  </div>
                </div>
                <div class="border-t border-gray-100"></div>
                <NuxtLink
                  to="/candidat/profil?tab=infos"
                  @click="showProfileMenu = false"
                  class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3"
                >
                  <User class="w-5 h-5 text-gray-400" />
                  Modifier le profil
                </NuxtLink>
                <NuxtLink
                  to="/candidat/profil?tab=password"
                  @click="showProfileMenu = false"
                  class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3"
                >
                  <Lock class="w-5 h-5 text-gray-400" />
                  Changer le mot de passe
                </NuxtLink>
              </div>
            </div>

            <!-- Notifications -->
            <button
              @click="afficherNotifications"
              class="relative flex items-center justify-center w-10 h-10 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
              title="Messages et recommandations"
            >
              <Bell class="w-5 h-5" />
              <span v-if="messagesNonLus > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                {{ messagesNonLus > 9 ? '9+' : messagesNonLus }}
              </span>
            </button>

            <!-- Bouton déconnexion -->
            <button @click="logout" class="flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg font-medium transition-all duration-200">
              <LogOut class="w-5 h-5" />
              <span class="hidden sm:inline">Déconnexion</span>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Sidebar navigation moderne -->
    <aside class="hidden lg:block fixed left-0 top-16 bottom-0 w-72 bg-white border-r border-gray-200 overflow-y-auto">
      <div class="p-6">
        <!-- Menu de navigation -->
        <nav class="space-y-2">
          <button
            v-for="item in navItems"
            :key="item.id"
            @click="scrollToSection(item.id)"
            :class="activeSection === item.id 
              ? 'bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 text-white shadow-md' 
              : 'text-gray-700 hover:bg-gray-50'"
            class="w-full flex items-center gap-4 px-5 py-4 rounded-xl transition-all duration-200 group"
          >
            <!-- Icône SVG -->
            <component :is="item.iconComponent" class="w-6 h-6 flex-shrink-0" :class="activeSection === item.id ? 'text-white' : 'text-gray-500 group-hover:text-gabon-green-600'" />
            
            <!-- Label -->
            <span class="text-base font-semibold flex-1 text-left">{{ item.label }}</span>
            
            <!-- Indicateur actif -->
            <div v-if="activeSection === item.id" class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
          </button>
        </nav>
      </div>
    </aside>

    <!-- Bouton menu mobile -->
    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden fixed bottom-6 right-6 z-50 w-14 h-14 bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-full shadow-2xl flex items-center justify-center text-white hover:shadow-xl transition-shadow">
      <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
      <X v-else class="w-6 h-6" />
    </button>

    <!-- Menu mobile -->
    <transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="mobileMenuOpen" class="lg:hidden fixed inset-0 z-40 bg-black/50 backdrop-blur-sm" @click="mobileMenuOpen = false">
        <transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="translate-y-full"
          enter-to-class="translate-y-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="translate-y-0"
          leave-to-class="translate-y-full"
        >
          <div v-if="mobileMenuOpen" class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl p-6" @click.stop>
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold text-gray-900">Navigation</h3>
              <button @click="mobileMenuOpen = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <X class="w-5 h-5 text-gray-500" />
              </button>
            </div>
            <div class="grid grid-cols-2 gap-3 max-h-[60vh] overflow-y-auto">
              <button
                v-for="item in navItems"
                :key="item.id"
                @click="scrollToSection(item.id); mobileMenuOpen = false"
                :class="activeSection === item.id ? 'bg-gradient-to-br from-gabon-green-600 to-gabon-green-700 text-white shadow-lg' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                class="flex flex-col items-center gap-3 px-4 py-4 rounded-xl transition-all"
              >
                <component :is="item.iconComponent" class="w-6 h-6" :class="activeSection === item.id ? 'text-white' : 'text-gabon-green-600'" />
                <span class="text-xs font-semibold text-center">{{ item.label }}</span>
              </button>
            </div>
          </div>
        </transition>
      </div>
    </transition>

    <!-- Contenu principal -->
    <main class="lg:ml-72 pt-20 min-h-screen">
      <div class="w-full max-w-full px-4 sm:px-6 lg:px-8 py-8">
        <!-- Loader -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <div class="relative">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gabon-green-600 border-t-transparent absolute top-0 left-0"></div>
          </div>
        </div>

        <!-- Contenu chargé -->
        <div v-else class="text-sm">
          <!-- Header moderne avec illustration -->
          <div class="mb-8">
            <div class="relative bg-gradient-to-br from-gabon-green-600 via-gabon-green-700 to-gabon-blue-700 rounded-3xl shadow-2xl overflow-hidden">
              <!-- Motif de fond décoratif -->
              <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
              </div>

              <div class="relative px-8 py-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                  <!-- Profil candidat -->
                  <div class="flex items-center gap-5">
                    <div class="relative">
                      <div class="w-24 h-24 bg-white rounded-2xl flex items-center justify-center shadow-xl ring-4 ring-white/20 overflow-hidden">
                        <img
                          v-if="candidat?.photo"
                          :src="siteRoot + '/storage/' + candidat.photo"
                          :alt="candidat?.nom_complet"
                          class="w-full h-full object-cover"
                        />
                        <span v-else class="text-3xl font-bold bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 bg-clip-text text-transparent">{{ getInitials(candidat?.nom_complet) }}</span>
                      </div>
                      <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
                        <CheckCircle2 v-if="candidat?.statut === 'valide'" class="w-6 h-6 text-green-600" />
                        <XCircle v-else-if="candidat?.statut === 'refuse'" class="w-6 h-6 text-red-600" />
                        <Clock v-else class="w-6 h-6 text-yellow-500" />
                      </div>
                    </div>

                    <div class="text-white">
                      <h2 class="text-2xl md:text-3xl font-bold mb-2">{{ candidat?.nom_complet }}</h2>
                      <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                        <p class="flex items-center gap-2 text-white/90">
                          <Mail class="w-4 h-4" />
                          <span class="text-sm font-medium truncate max-w-[200px]">{{ candidat?.email }}</span>
                        </p>
                        <p v-if="candidat?.telephone" class="flex items-center gap-2 text-white/90">
                          <Phone class="w-4 h-4" />
                          <span class="text-sm font-medium">{{ candidat?.telephone }}</span>
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Badge statut moderne -->
                  <div class="flex items-center gap-3">
                    <div :class="{
                      'bg-yellow-500/20 border-yellow-300/30 backdrop-blur-xl': candidat?.statut === 'en_attente',
                      'bg-green-500/20 border-green-300/30 backdrop-blur-xl': candidat?.statut === 'valide',
                      'bg-red-500/20 border-red-300/30 backdrop-blur-xl': candidat?.statut === 'refuse'
                    }" class="px-6 py-4 rounded-2xl border-2 text-white min-w-[140px]">
                      <div class="flex items-center justify-center gap-2 mb-2">
                        <Clock v-if="candidat?.statut === 'en_attente'" class="w-6 h-6" />
                        <CheckCircle2 v-else-if="candidat?.statut === 'valide'" class="w-6 h-6" />
                        <XCircle v-else class="w-6 h-6" />
                      </div>
                      <div class="text-sm font-bold uppercase tracking-wide text-center">
                        {{ candidat?.statut === 'en_attente' ? 'En attente' : candidat?.statut === 'valide' ? 'Validé' : 'Refusé' }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Progression du dossier améliorée -->
          <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8">
              <div class="flex items-center justify-between mb-6">
                <div>
                  <h3 class="text-xl font-bold text-gray-900 mb-1">Progression de mon dossier</h3>
                  <p class="text-sm text-gray-500">{{ progressItems.filter(i => i.done).length }}/{{ progressItems.length }} étapes complétées</p>
                </div>
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-gabon-green-100 to-gabon-blue-100">
                  <span class="text-2xl font-bold bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 bg-clip-text text-transparent">
                    {{ Math.round((progressItems.filter(i => i.done).length / progressItems.length) * 100) }}%
                  </span>
                </div>
              </div>

              <!-- Barre de progression -->
              <div class="relative w-full h-3 bg-gray-100 rounded-full overflow-hidden mb-6">
                <div
                  class="absolute top-0 left-0 h-full bg-gradient-to-r from-gabon-green-600 to-gabon-green-500 rounded-full transition-all duration-700 ease-out"
                  :style="{ width: `${(progressItems.filter(i => i.done).length / progressItems.length) * 100}%` }"
                >
                  <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                  v-for="(item, index) in progressItems"
                  :key="item.label"
                  class="group relative flex items-center gap-3 p-4 rounded-xl border-2 transition-all duration-300 hover:shadow-md"
                  :class="item.done
                    ? 'bg-gradient-to-br from-green-50 to-green-100/50 border-green-200 hover:border-green-300'
                    : 'bg-gray-50/50 border-gray-200 hover:border-gray-300'"
                >
                  <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-all duration-300"
                    :class="item.done
                      ? 'bg-gradient-to-br from-green-500 to-green-600 shadow-lg shadow-green-500/30'
                      : 'bg-gray-300 group-hover:bg-gray-400'"
                  >
                    <Check v-if="item.done" class="w-5 h-5 text-white" />
                    <span v-else class="text-sm font-bold text-white">{{ index + 1 }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <span
                      class="text-sm font-bold block truncate"
                      :class="item.done ? 'text-green-800' : 'text-gray-700'"
                    >
                      {{ item.label }}
                    </span>
                    <span class="text-xs text-gray-500">
                      {{ item.done ? 'Complété' : 'En attente' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Message selon le statut -->
          <div v-if="candidat?.statut === 'en_attente'" class="mb-8">
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-500 rounded-xl p-6">
              <div class="flex items-start gap-4">
                <Info class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-1" />
                <div>
                  <h3 class="font-bold text-yellow-900 mb-2">Candidature en cours d'examen</h3>
                  <p class="text-yellow-800">Votre dossier est actuellement examiné par nos administrateurs. Vous recevrez un email dès qu'une décision sera prise.</p>
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="candidat?.statut === 'valide'" class="mb-8">
            <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-xl p-6">
              <div class="flex items-start gap-4">
                <CheckCircle2 class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" />
                <div>
                  <h3 class="font-bold text-green-900 mb-2">Félicitations ! Votre candidature a été acceptée</h3>
                  <p class="text-green-800">Vous êtes maintenant officiellement enregistré en tant que dignitaire. Un email de confirmation a été envoyé à votre adresse.</p>
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="candidat?.statut === 'refuse'" class="mb-8">
            <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-xl p-6">
              <div class="flex items-start gap-4">
                <AlertCircle class="w-6 h-6 text-red-600 flex-shrink-0 mt-1" />
                <div>
                  <h3 class="font-bold text-red-900 mb-2">Candidature refusée</h3>
                  <p class="text-red-800 mb-3">{{ candidat?.motif_refus || 'Votre candidature n\'a pas été retenue.' }}</p>
                  <p class="text-red-700 text-sm">Vous pouvez soumettre une nouvelle candidature après avoir complété votre dossier.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Grid principal -->
          <div class="grid lg:grid-cols-12 gap-4 w-full max-w-full">
            <!-- Informations personnelles -->
            <div class="lg:col-span-7 space-y-4 min-w-0">
              <!-- Section Profil (Informations Personnelles) -->
              <div id="section-profil" class="scroll-mt-32 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gabon-green-50 to-gabon-blue-50 px-6 py-4 border-b border-gray-100">
                  <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                      <div class="w-10 h-10 bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-lg flex items-center justify-center shadow-md">
                        <User class="w-5 h-5 text-white" />
                      </div>
                      Informations Personnelles
                    </h3>
                  </div>
                </div>
                <div class="p-6">
                  <!-- Photo de profil centrée -->
                  <div class="flex justify-center mb-6">
                    <div class="relative">
                      <div class="w-32 h-32 rounded-full overflow-hidden bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 flex items-center justify-center shadow-lg ring-4 ring-white">
                        <img
                          v-if="candidat?.photo"
                          :src="siteRoot + '/storage/' + candidat.photo"
                          class="w-full h-full object-cover"
                          alt="Photo de profil"
                        >
                        <span v-else class="text-4xl font-bold text-white">{{ getInitials(candidat?.nom_complet) }}</span>
                      </div>
                      <div class="absolute bottom-2 right-2 w-8 h-8 rounded-full flex items-center justify-center shadow-md"
                        :class="candidat?.statut === 'valide' ? 'bg-green-500' : candidat?.statut === 'refuse' ? 'bg-red-500' : 'bg-yellow-500'"
                      >
                        <CheckCircle2 v-if="candidat?.statut === 'valide'" class="w-5 h-5 text-white" />
                        <XCircle v-else-if="candidat?.statut === 'refuse'" class="w-5 h-5 text-white" />
                        <Clock v-else class="w-5 h-5 text-white" />
                      </div>
                    </div>
                  </div>

                  <!-- Grille d'informations -->
                  <div class="space-y-3">
                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                      <User class="w-5 h-5 text-gabon-green-600 flex-shrink-0" />
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Nom complet</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ candidat?.nom_complet || '—' }}</p>
                      </div>
                    </div>

                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                      <Mail class="w-5 h-5 text-gabon-blue-600 flex-shrink-0" />
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Email</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ candidat?.email || '—' }}</p>
                      </div>
                    </div>

                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                      <Phone class="w-5 h-5 text-gabon-yellow-600 flex-shrink-0" />
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Téléphone</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ candidat?.telephone || '—' }}</p>
                      </div>
                    </div>

                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                      <Calendar class="w-5 h-5 text-indigo-600 flex-shrink-0" />
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Date de naissance</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ formatDate(candidat?.date_naissance) || '—' }}</p>
                      </div>
                    </div>

                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                      <MapPin class="w-5 h-5 text-rose-600 flex-shrink-0" />
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Ville de résidence</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ candidat?.ville_residence?.nom || '—' }}</p>
                      </div>
                    </div>

                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                      <Home class="w-5 h-5 text-purple-600 flex-shrink-0" />
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Adresse</p>
                        <p class="text-sm font-semibold text-gray-900">{{ candidat?.adresse || '—' }}</p>
                      </div>
                    </div>

                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                      <CreditCard class="w-5 h-5 text-teal-600 flex-shrink-0" />
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Matricule</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ candidat?.matricule || 'Non attribué' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section Documents améliorée -->
              <div id="section-documents" class="scroll-mt-32 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gabon-blue-50 to-gabon-green-50 px-6 py-4 border-b border-gray-100">
                  <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gabon-blue-600 to-gabon-green-600 flex items-center justify-center shadow-md">
                        <FileText class="w-5 h-5 text-white" />
                      </div>
                      Mes Documents
                    </h3>
                    <span class="px-4 py-2 bg-white/60 backdrop-blur-sm text-gabon-blue-700 rounded-xl text-sm font-bold border border-gabon-blue-200">
                      {{ documents?.length || 0 }} document{{ (documents?.length || 0) > 1 ? 's' : '' }}
                    </span>
                  </div>
                </div>

                <div class="p-6">
                  <div v-if="documents && documents.length > 0" class="space-y-3">
                    <a
                      v-for="doc in documents"
                      :key="doc.id"
                      :href="siteRoot + doc.url_complete"
                      target="_blank"
                      class="group flex items-center justify-between bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border-2 border-gray-100 hover:border-gabon-blue-400 hover:shadow-lg transition-all duration-300"
                    >
                      <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-gabon-blue-100 to-gabon-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                          <component :is="getDocumentIconComponent(doc.type_document)" class="w-6 h-6 text-gabon-blue-600" />
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="font-bold text-gray-900 truncate group-hover:text-gabon-blue-700 transition-colors">{{ doc.nom_fichier }}</p>
                          <p class="text-sm text-gray-500 flex items-center gap-2">
                            <span class="font-semibold">{{ doc.taille_lisible }}</span>
                            <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                            <span>{{ getDocumentTypeLabel(doc.type_document) }}</span>
                          </p>
                        </div>
                      </div>
                      <div class="flex items-center gap-2 flex-shrink-0 ml-3">
                        <ExternalLink class="w-5 h-5 text-gray-400 group-hover:text-gabon-blue-600 group-hover:translate-x-1 transition-all duration-300" />
                      </div>
                    </a>
                  </div>

                  <div v-else class="text-center py-16">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                      <File class="w-10 h-10 text-gray-300" />
                    </div>
                    <p class="text-gray-500 font-medium">Aucun document téléchargé</p>
                    <p class="text-sm text-gray-400 mt-1">Vos documents apparaîtront ici</p>
                  </div>
                </div>
              </div>

              <!-- Section Langues -->
              <div id="section-langues" class="scroll-mt-32 bg-white rounded-2xl shadow-xl border border-gray-200 p-5">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Mes langues</h3>

                <div v-if="langues.length > 0" class="space-y-2 mb-6">
                  <div v-for="l in langues" :key="l.id" class="flex items-center justify-between bg-gray-50 rounded-xl p-3 border border-gray-200">
                    <span class="font-medium text-gray-900">{{ l.langue?.nom }} <span class="text-gray-500 text-sm">({{ l.niveau || 'niveau non précisé' }})</span></span>
                    <button v-if="estModifiable" @click="deleteLangue(l.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </div>
                <p v-else class="text-gray-500 mb-6">Aucune langue ajoutée</p>

                <form v-if="estModifiable" @submit.prevent="addLangue" class="flex flex-col sm:flex-row gap-3">
                  <select v-model="newLangue.langue_id" required class="flex-1 px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    <option value="">Sélectionnez une langue...</option>
                    <option v-for="l in referenceLangues" :key="l.id" :value="l.id">{{ l.nom }}</option>
                  </select>
                  <select v-model="newLangue.niveau" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    <option value="">Niveau...</option>
                    <option value="Débutant">Débutant</option>
                    <option value="Intermédiaire">Intermédiaire</option>
                    <option value="Courant">Courant</option>
                    <option value="Natif">Natif</option>
                  </select>
                  <button type="submit" class="px-4 py-2 bg-gabon-green-600 hover:bg-gabon-green-700 text-white font-semibold rounded-lg transition-colors">Ajouter</button>
                </form>
              </div>

              <!-- Section Diplômes -->
              <div id="section-diplomes" class="scroll-mt-32 bg-white rounded-2xl shadow-xl border border-gray-200 p-5">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Mes diplômes</h3>

                <div v-if="diplomes.length > 0" class="space-y-2 mb-6">
                  <div v-for="d in diplomes" :key="d.id" class="flex items-center justify-between bg-gray-50 rounded-xl p-3 border border-gray-200">
                    <div>
                      <p class="font-medium text-gray-900">{{ d.intitule }} <span v-if="d.type" class="text-xs font-normal text-gray-500">({{ d.type }})</span></p>
                      <p class="text-sm text-gray-500">{{ d.etablissement?.nom || 'Établissement non précisé' }} {{ d.annee ? `• ${d.annee}` : '' }}</p>
                      <a v-if="d.justificatif_url" :href="siteRoot + d.justificatif_url" target="_blank" class="text-xs text-gabon-blue-700 hover:underline">Voir le justificatif</a>
                    </div>
                    <button v-if="estModifiable" @click="deleteDiplome(d.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </div>
                <p v-else class="text-gray-500 mb-6">Aucun diplôme ajouté</p>

                <form v-if="estModifiable" @submit.prevent="addDiplome" class="space-y-3">
                  <div class="grid md:grid-cols-2 gap-3">
                    <input v-model="newDiplome.intitule" type="text" required placeholder="Intitulé du diplôme" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    <input v-model="newDiplome.annee" type="text" placeholder="Année" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    <EtablissementPicker v-model="newDiplome.etablissement_id" />
                    <select v-model="newDiplome.domaine_id" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                      <option value="">Domaine...</option>
                      <option v-for="dm in referenceDomaines" :key="dm.id" :value="dm.id">{{ dm.nom }}</option>
                    </select>
                    <select v-model="newDiplome.type" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                      <option value="">Type de diplôme...</option>
                      <option v-for="t in TYPES_DIPLOME" :key="t" :value="t">{{ t }}</option>
                    </select>
                    <div class="md:col-span-2">
                      <FileUploadZone
                        v-model="newDiplome.justificatif"
                        accept="application/pdf"
                        label="Justificatif"
                        hint="PDF, 10 Mo max"
                        :max-size-mb="10"
                      />
                    </div>
                  </div>
                  <button type="submit" class="px-4 py-2 bg-gabon-green-600 hover:bg-gabon-green-700 text-white font-semibold rounded-lg transition-colors">Ajouter le diplôme</button>
                </form>
              </div>

              <!-- Section Expériences professionnelles -->
              <div id="section-experiences" class="scroll-mt-32 bg-white rounded-2xl shadow-xl border border-gray-200 p-5">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Mes expériences professionnelles</h3>

                <div v-if="experiences.length > 0" class="space-y-2 mb-6">
                  <div v-for="ex in experiences" :key="ex.id" class="flex items-center justify-between bg-gray-50 rounded-xl p-3 border border-gray-200">
                    <div>
                      <p class="font-medium text-gray-900">{{ ex.intitule }}</p>
                      <p class="text-sm text-gray-500">{{ ex.structure?.nom || 'Structure non précisée' }} • {{ formatDate(ex.date_debut) }} — {{ ex.date_fin ? formatDate(ex.date_fin) : 'en cours' }}</p>
                    </div>
                    <button v-if="estModifiable" @click="deleteExperience(ex.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </div>
                <p v-else class="text-gray-500 mb-6">Aucune expérience ajoutée</p>

                <form v-if="estModifiable" @submit.prevent="addExperience" class="space-y-3">
                  <div class="grid md:grid-cols-2 gap-3">
                    <input v-model="newExperience.intitule" type="text" required placeholder="Intitulé du poste" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm md:col-span-2">
                    <select v-model="newExperience.structure_id" class="px-3 py-2 border-2 border-gray-300 rounded-lg text-sm md:col-span-2">
                      <option value="">Structure / institution...</option>
                      <option v-for="s in referenceStructures" :key="s.id" :value="s.id">{{ s.nom }}</option>
                    </select>
                    <div>
                      <label class="text-xs font-semibold text-gray-600 block mb-1">Date de début</label>
                      <input v-model="newExperience.date_debut" type="date" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                      <label class="text-xs font-semibold text-gray-600 block mb-1">Date de fin</label>
                      <input v-model="newExperience.date_fin" type="date" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-sm">
                    </div>
                    <div class="md:col-span-2">
                      <FileUploadZone
                        v-model="newExperience.justificatif"
                        accept=".pdf,.jpg,.jpeg,.png"
                        label="Justificatif (facultatif)"
                        hint="PDF ou image, 10 Mo max"
                        :max-size-mb="10"
                      />
                    </div>
                  </div>
                  <button type="submit" class="px-4 py-2 bg-gabon-green-600 hover:bg-gabon-green-700 text-white font-semibold rounded-lg transition-colors">Ajouter l'expérience</button>
                </form>
              </div>

            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-5 space-y-4 min-w-0 overflow-hidden">
              <!-- Widget Messages permanent -->
              <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gabon-blue-600 to-gabon-green-600 px-6 py-4 flex items-center justify-between">
                  <h3 class="text-lg font-bold text-white flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                      <MessageCircle class="w-5 h-5 text-white" />
                    </div>
                    Mes Messages
                  </h3>
                  <span v-if="messagesNonLus > 0" class="px-3 py-1.5 bg-red-500 text-white text-xs font-bold rounded-full animate-pulse">
                    {{ messagesNonLus > 9 ? '9+' : messagesNonLus }}
                  </span>
                </div>

                <div class="p-5">
                  <div v-if="loadingMessages" class="text-center py-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-2 border-gabon-green-600 border-t-transparent mx-auto"></div>
                  </div>

                  <div v-else-if="messages.length === 0" class="text-center py-6">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                      <Inbox class="w-8 h-8 text-gray-300" />
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Aucun message</p>
                    <p class="text-xs text-gray-400 mt-1">Vous serez notifié ici</p>
                  </div>

                  <div v-else class="space-y-2 max-h-80 overflow-y-auto">
                    <div
                      v-for="message in messages"
                      :key="message.id"
                      :class="[
                        'p-4 rounded-xl border-2 transition-all cursor-pointer',
                        message.type === 'recommandation' ? 'bg-blue-50 border-blue-200 hover:border-blue-300' :
                        message.type === 'validation' ? 'bg-green-50 border-green-200 hover:border-green-300' :
                        'bg-red-50 border-red-200 hover:border-red-300',
                        !message.lu ? 'ring-2 ring-offset-2' : '',
                        !message.lu ? (message.type === 'recommandation' ? 'ring-blue-400' : message.type === 'validation' ? 'ring-green-400' : 'ring-red-400') : ''
                      ]"
                      @click="marquerLu(message.id)"
                    >
                      <div class="flex items-start gap-3">
                        <div :class="[
                          'w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0',
                          message.type === 'recommandation' ? 'bg-blue-500' :
                          message.type === 'validation' ? 'bg-green-500' : 'bg-red-500'
                        ]">
                          <MessageCircle v-if="message.type === 'recommandation'" class="w-5 h-5 text-white" />
                          <CheckCircle2 v-else-if="message.type === 'validation'" class="w-5 h-5 text-white" />
                          <XCircle v-else class="w-5 h-5 text-white" />
                        </div>
                        <div class="flex-1 min-w-0">
                          <div class="flex items-center gap-2 mb-1">
                            <span :class="[
                              'text-xs font-bold uppercase tracking-wide',
                              message.type === 'recommandation' ? 'text-blue-700' :
                              message.type === 'validation' ? 'text-green-700' : 'text-red-700'
                            ]">
                              {{ messageTypeLabel(message.type) }}
                            </span>
                            <span v-if="!message.lu" class="px-2 py-0.5 bg-blue-500 text-white text-xs font-bold rounded-full">
                              Nouveau
                            </span>
                          </div>
                          <p class="text-sm text-gray-800 mb-2 line-clamp-3">{{ message.contenu }}</p>
                          <div class="flex items-center gap-2 text-xs text-gray-500">
                            <Calendar class="w-3 h-3" />
                            {{ formatDate(message.created_at) }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <button
                    v-if="messages.length > 0 && messagesNonLus > 0"
                    @click="toutMarquerLu"
                    class="w-full mt-4 px-4 py-2 bg-gradient-to-r from-gabon-green-600 to-gabon-blue-600 text-white hover:from-gabon-green-700 hover:to-gabon-blue-700 font-semibold rounded-xl transition-all flex items-center justify-center gap-2"
                  >
                    <CheckCircle2 class="w-4 h-4" />
                    Tout marquer comme lu
                  </button>
                </div>
              </div>

              <!-- Récapitulatif moderne -->
              <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gabon-green-600 to-gabon-blue-600 px-6 py-4">
                  <h3 class="text-lg font-bold text-white flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                      <ClipboardCheck class="w-5 h-5 text-white" />
                    </div>
                    Récapitulatif de ma candidature
                  </h3>
                </div>

                <div class="p-6 space-y-6">
                  <!-- Informations personnelles -->
                  <div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                      <User class="w-4 h-4" />
                      Informations personnelles
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                      <div class="bg-white rounded-lg p-3 border border-gray-100">
                        <dt class="text-xs text-gray-500 mb-1">Nom complet</dt>
                        <dd class="font-semibold text-gray-900 text-sm truncate">{{ candidat?.nom_complet }}</dd>
                      </div>
                      <div class="bg-white rounded-lg p-3 border border-gray-100">
                        <dt class="text-xs text-gray-500 mb-1">Email</dt>
                        <dd class="font-semibold text-gray-900 text-sm truncate">{{ candidat?.email }}</dd>
                      </div>
                      <div class="bg-white rounded-lg p-3 border border-gray-100">
                        <dt class="text-xs text-gray-500 mb-1">Date de naissance</dt>
                        <dd class="font-semibold text-gray-900 text-sm">{{ formatDate(candidat?.date_naissance) }}</dd>
                      </div>
                      <div class="bg-white rounded-lg p-3 border border-gray-100">
                        <dt class="text-xs text-gray-500 mb-1">Téléphone</dt>
                        <dd class="font-semibold text-gray-900 text-sm">{{ candidat?.telephone || 'Non renseigné' }}</dd>
                      </div>
                    </div>
                  </div>

                  <!-- Documents -->
                  <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                      <FileText class="w-4 h-4" />
                      Documents ({{ documents?.length || 0 }})
                    </h4>
                    <div v-if="!documents?.length" class="bg-gray-50 rounded-lg p-4 text-center">
                      <p class="text-sm text-gray-400">Aucun document téléchargé</p>
                    </div>
                    <div v-else class="bg-white rounded-lg border border-gray-100 divide-y divide-gray-100">
                      <div v-for="doc in documents" :key="doc.id" class="px-3 py-2 text-sm flex items-center gap-2">
                        <component :is="getDocumentIconComponent(doc.type_document)" class="w-4 h-4 text-gabon-blue-600" />
                        <span class="text-gray-900 font-medium truncate flex-1">{{ doc.nom_fichier }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Langues -->
                  <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                      <Languages class="w-4 h-4" />
                      Langues ({{ langues.length }})
                    </h4>
                    <div v-if="!langues.length" class="bg-gray-50 rounded-lg p-4 text-center">
                      <p class="text-sm text-gray-400">Aucune langue ajoutée</p>
                    </div>
                    <div v-else class="flex flex-wrap gap-2">
                      <span
                        v-for="l in langues"
                        :key="l.id"
                        class="px-3 py-1.5 bg-gabon-green-50 text-gabon-green-700 rounded-lg text-sm font-medium border border-gabon-green-200"
                      >
                        {{ l.langue?.nom }}{{ l.niveau ? ` (${l.niveau})` : '' }}
                      </span>
                    </div>
                  </div>

                  <!-- Diplômes -->
                  <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                      <GraduationCap class="w-4 h-4" />
                      Diplômes ({{ diplomes.length }})
                    </h4>
                    <div v-if="!diplomes.length" class="bg-gray-50 rounded-lg p-4 text-center">
                      <p class="text-sm text-gray-400">Aucun diplôme ajouté</p>
                    </div>
                    <ul v-else class="space-y-2">
                      <li v-for="d in diplomes" :key="d.id" class="bg-white rounded-lg p-3 border border-gray-100 text-sm">
                        <p class="font-semibold text-gray-900">{{ d.intitule }}</p>
                        <p v-if="d.annee" class="text-xs text-gray-500 mt-0.5">{{ d.annee }}</p>
                      </li>
                    </ul>
                  </div>

                  <!-- Expériences -->
                  <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                      <Briefcase class="w-4 h-4" />
                      Expériences ({{ experiences.length }})
                    </h4>
                    <div v-if="!experiences.length" class="bg-gray-50 rounded-lg p-4 text-center">
                      <p class="text-sm text-gray-400">Aucune expérience ajoutée</p>
                    </div>
                    <ul v-else class="space-y-2">
                      <li v-for="ex in experiences" :key="ex.id" class="bg-white rounded-lg p-3 border border-gray-100 text-sm">
                        <p class="font-semibold text-gray-900">{{ ex.intitule }}</p>
                        <p v-if="ex.structure?.nom" class="text-xs text-gray-500 mt-0.5">{{ ex.structure.nom }}</p>
                      </li>
                    </ul>
                  </div>

                  <!-- Bouton export -->
                  <button
                    type="button"
                    @click="() => window.print()"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 text-white hover:from-gabon-green-700 hover:to-gabon-green-800 font-semibold rounded-xl transition-all shadow-md hover:shadow-lg"
                  >
                    <Printer class="w-5 h-5" />
                    Imprimer / Exporter en PDF
                  </button>
                </div>
              </div>

              <!-- Section Timeline -->
              <div id="section-timeline" class="scroll-mt-32 bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                  <Calendar class="w-5 h-5 text-gabon-green-600" />
                  Chronologie
                </h3>
                <div class="space-y-4">
                  <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                      <div class="w-10 h-10 bg-gradient-to-br from-gabon-green-500 to-gabon-green-600 rounded-full flex items-center justify-center shadow-md">
                        <Check class="w-5 h-5 text-white" />
                      </div>
                      <div class="w-0.5 h-16 bg-gradient-to-b from-gabon-green-300 to-gray-200"></div>
                    </div>
                    <div class="flex-1 pb-4">
                      <p class="font-bold text-gray-900">Candidature soumise</p>
                      <p class="text-sm text-gray-500 mt-1">{{ formatDate(candidat?.date_candidature) }}</p>
                    </div>
                  </div>

                  <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                      <div :class="candidat?.statut !== 'en_attente' ? 'bg-gradient-to-br from-gabon-green-500 to-gabon-green-600 shadow-md' : 'bg-gray-300'" class="w-10 h-10 rounded-full flex items-center justify-center">
                        <Check v-if="candidat?.statut !== 'en_attente'" class="w-5 h-5 text-white" />
                        <span v-else class="text-white text-sm font-bold">2</span>
                      </div>
                      <div v-if="candidat?.statut !== 'en_attente'" class="w-0.5 h-16 bg-gradient-to-b from-gabon-green-300 to-gray-200"></div>
                    </div>
                    <div class="flex-1 pb-4">
                      <p class="font-bold text-gray-900">Examen du dossier</p>
                      <p v-if="candidat?.date_validation" class="text-sm text-gray-500 mt-1">{{ formatDate(candidat?.date_validation) }}</p>
                      <p v-else class="text-sm text-yellow-600 mt-1 flex items-center gap-1">
                        <Clock class="w-4 h-4" />
                        En cours d'examen...
                      </p>
                    </div>
                  </div>

                  <div v-if="candidat?.statut === 'valide'" class="flex gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-gabon-green-500 to-gabon-green-600 rounded-full flex items-center justify-center shadow-md">
                      <Check class="w-5 h-5 text-white" />
                    </div>
                    <div class="flex-1">
                      <p class="font-bold text-gray-900">Candidature acceptée</p>
                      <p class="text-sm text-green-600 mt-1">Vous êtes maintenant dignitaire</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Actions rapides -->
              <div class="bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-2xl shadow-xl p-6 text-white">
                <h3 class="text-lg font-bold mb-4">Besoin d'aide ?</h3>
                <p class="text-sm text-gabon-green-50 mb-4 break-words">Contactez notre support si vous avez des questions</p>
                <a href="mailto:contact@dignitaires.ga" class="block px-4 py-3 bg-white/20 hover:bg-white/30 rounded-xl font-semibold text-center transition-colors backdrop-blur-sm">
                  Contacter le support
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  AlertCircle, Bell, Briefcase, Building2, Calendar, Check, CheckCircle2, ChevronDown,
  Circle, ClipboardCheck, Clock, CreditCard, ExternalLink, File, FileText, GraduationCap, Heart, Home, Inbox, Info,
  Languages, Lock, LogOut, Mail, MapPin, Menu, MessageCircle, Paperclip, Phone, Printer, Shield, Trash2, User, X, XCircle
} from 'lucide-vue-next'

const router = useRouter()
const { $api, $swal } = useNuxtApp()
const config = useRuntimeConfig()
const siteRoot = computed(() => config.public.apiBase.replace(/\/api\/?$/, ''))

const loading = ref(true)
const candidat = ref(null)
const documents = ref([])
const langues = ref([])
const diplomes = ref([])
const experiences = ref([])
const messages = ref([])
const messagesNonLus = ref(0)
const loadingMessages = ref(true)

const referenceLangues = ref([])
const referenceEtablissements = ref([])
const referenceDomaines = ref([])
const referenceStructures = ref([])

const TYPES_DIPLOME = ['BEPC', 'Baccalauréat', 'BTS/DUT', 'Licence', 'Master', 'Doctorat', "Diplôme d'ingénieur", 'Autre']

const newLangue = ref({ langue_id: '', niveau: '' })
const newDiplome = ref({ intitule: '', etablissement_id: '', domaine_id: '', annee: '', type: '', justificatif: null })
const newExperience = ref({ intitule: '', structure_id: '', date_debut: '', date_fin: '', justificatif: null })

// Scroll-spy navigation
const activeSection = ref('section-profil')
const mobileMenuOpen = ref(false)

// Menu profil (renvoie vers /candidat/profil)
const showProfileMenu = ref(false)
const profileMenuRef = ref(null)

const navItems = [
  { id: 'section-profil', label: 'Profil', iconComponent: User },
  { id: 'section-documents', label: 'Documents', iconComponent: FileText },
  { id: 'section-langues', label: 'Langues', iconComponent: Languages },
  { id: 'section-diplomes', label: 'Diplômes', iconComponent: GraduationCap },
  { id: 'section-experiences', label: 'Expériences', iconComponent: Briefcase },
  { id: 'section-timeline', label: 'Chronologie', iconComponent: Calendar },
]

const estModifiable = computed(() => candidat.value?.statut === 'en_attente')

const progressItems = computed(() => [
  { label: 'Documents', done: documents.value.length > 0 },
  { label: 'Langues', done: langues.value.length > 0 },
  { label: 'Diplômes', done: diplomes.value.length > 0 },
  { label: 'Expériences', done: experiences.value.length > 0 },
])

// Fonction pour scroller vers une section
const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    const yOffset = -100 // Offset pour ne pas cacher sous la navbar
    const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset
    
    window.scrollTo({
      top: y,
      behavior: 'smooth'
    })
    
    activeSection.value = sectionId
  }
}

// Détection automatique de la section visible (Scroll-Spy)
const handleScroll = () => {
  const sections = navItems.map(item => document.getElementById(item.id)).filter(el => el !== null)
  
  let currentSection = activeSection.value
  
  for (const section of sections) {
    const rect = section.getBoundingClientRect()
    // Si la section est visible dans le viewport (avec une marge)
    if (rect.top <= 200 && rect.bottom >= 200) {
      currentSection = section.id
      break
    }
  }
  
  activeSection.value = currentSection
}

// Fermer le menu profil quand on clique ailleurs
const handleClickOutsideProfileMenu = (e) => {
  if (profileMenuRef.value && !profileMenuRef.value.contains(e.target)) {
    showProfileMenu.value = false
  }
}

// Afficher les notifications au chargement
const afficherNotifications = () => {
  if (!messages.value || messages.value.length === 0) {
    $swal.fire({
      icon: 'info',
      title: 'Aucune notification',
      text: 'Vous n\'avez aucun message pour le moment.',
      confirmButtonColor: '#16a34a'
    })
    return
  }

  const messagesHTML = messages.value.map(m => {
    const iconSvg = m.type === 'recommandation'
      ? '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>'
      : m.type === 'validation'
      ? '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
      : '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>'

    const color = m.type === 'recommandation' ? 'text-blue-700' : m.type === 'validation' ? 'text-green-700' : 'text-red-700'
    const badge = !m.lu ? '<span class="text-xs bg-blue-500 text-white px-2 py-0.5 rounded-full ml-2">Nouveau</span>' : ''

    return `
      <div class="text-left mb-3 p-3 rounded-lg ${m.type === 'recommandation' ? 'bg-blue-50' : m.type === 'validation' ? 'bg-green-50' : 'bg-red-50'}">
        <div class="flex items-center gap-2 mb-1">
          <div class="${color}">${iconSvg}</div>
          <span class="font-bold ${color}">${messageTypeLabel(m.type)}</span>
          ${badge}
        </div>
        <p class="text-sm text-gray-700 whitespace-pre-line">${m.contenu}</p>
        <p class="text-xs text-gray-500 mt-1">${formatDate(m.created_at)}</p>
      </div>
    `
  }).join('')

  $swal.fire({
    title: `Vos messages (${messages.value.length})`,
    html: `<div class="max-h-96 overflow-y-auto">${messagesHTML}</div>`,
    confirmButtonText: 'Tout marquer comme lu',
    showCancelButton: true,
    cancelButtonText: 'Fermer',
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#6b7280',
    width: '600px'
  }).then((result) => {
    if (result.isConfirmed) {
      toutMarquerLu()
    }
  })
}

// Système de rappel toutes les 24h
const NOTIFICATION_INTERVAL_KEY = 'last_notification_check'
const VINGT_QUATRE_HEURES = 24 * 60 * 60 * 1000 // 24h en millisecondes
let notificationInterval = null

const doitAfficherRappel = () => {
  const lastCheck = localStorage.getItem(NOTIFICATION_INTERVAL_KEY)
  if (!lastCheck) return true

  const now = Date.now()
  const lastCheckTime = parseInt(lastCheck)
  return (now - lastCheckTime) >= VINGT_QUATRE_HEURES
}

const marquerRappelAffiche = () => {
  localStorage.setItem(NOTIFICATION_INTERVAL_KEY, Date.now().toString())
}

// Afficher automatiquement les notifications non lues au chargement
const afficherNotificationsAuChargement = () => {
  if (messagesNonLus.value > 0 && doitAfficherRappel()) {
    setTimeout(() => {
      $swal.fire({
        icon: 'info',
        title: 'Nouveaux messages',
        html: `Vous avez <strong>${messagesNonLus.value}</strong> nouveau${messagesNonLus.value > 1 ? 'x' : ''} message${messagesNonLus.value > 1 ? 's' : ''}.`,
        confirmButtonText: 'Voir les messages',
        showCancelButton: true,
        cancelButtonText: 'Plus tard',
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280'
      }).then((result) => {
        marquerRappelAffiche()
        if (result.isConfirmed) {
          afficherNotifications()
        }
      })
    }, 1500) // Délai de 1.5s après le chargement
  }
}

// Vérifier périodiquement les nouveaux messages et afficher un rappel toutes les 24h
const demarrerRappelPeriodique = () => {
  // Vérifier toutes les 30 minutes si on doit afficher le rappel
  notificationInterval = setInterval(() => {
    if (messagesNonLus.value > 0 && doitAfficherRappel()) {
      $swal.fire({
        icon: 'info',
        title: 'Rappel : Messages non lus',
        html: `Vous avez toujours <strong>${messagesNonLus.value}</strong> message${messagesNonLus.value > 1 ? 's' : ''} non lu${messagesNonLus.value > 1 ? 's' : ''}.`,
        confirmButtonText: 'Voir maintenant',
        showCancelButton: true,
        cancelButtonText: 'Plus tard',
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        timer: 10000, // Auto-close après 10 secondes
        timerProgressBar: true
      }).then((result) => {
        marquerRappelAffiche()
        if (result.isConfirmed) {
          afficherNotifications()
        }
      })
    }
  }, 30 * 60 * 1000) // Vérifier toutes les 30 minutes
}

// Ajouter l'écouteur de scroll
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  document.addEventListener('click', handleClickOutsideProfileMenu)
  loadCandidatData().then(() => {
    // Afficher les notifications après le chargement des données
    afficherNotificationsAuChargement()
    // Démarrer le système de rappel périodique toutes les 24h
    demarrerRappelPeriodique()
  })
})

// Retirer l'écouteur lors du démontage
onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('click', handleClickOutsideProfileMenu)
  // Nettoyer l'intervalle de rappel
  if (notificationInterval) {
    clearInterval(notificationInterval)
  }
})

// Charger les données du candidat
const loadCandidatData = async () => {
  try {
    const token = localStorage.getItem('candidat_token')
    
    if (!token) {
      router.push('/candidature/login')
      return
    }

    const response = await $api.get('/candidats/me', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })

    if (response.success) {
      candidat.value = response.candidat
      documents.value = response.candidat.documents || []
    }

    await Promise.all([loadLangues(), loadDiplomes(), loadExperiences(), loadReferenceLists(), loadMessages()])
  } catch (error) {
    console.error('Erreur de chargement:', error)
    
    if (error.response?.status === 401) {
      $swal.fire({
        icon: 'error',
        title: 'Session expirée',
        text: 'Veuillez vous reconnecter',
        confirmButtonColor: '#16a34a'
      }).then(() => {
        localStorage.removeItem('candidat_token')
        router.push('/candidature/login')
      })
    }
  } finally {
    loading.value = false
  }
}

const authHeaders = () => ({ Authorization: `Bearer ${localStorage.getItem('candidat_token')}` })

// Messages / recommandations reçus sur la candidature
const loadMessages = async () => {
  loadingMessages.value = true
  try {
    const response = await $api.get('/candidats/me/messages', { headers: authHeaders() })
    messages.value = response.messages || []
    messagesNonLus.value = response.non_lus || 0
  } catch (error) {
    console.error('Erreur chargement messages:', error)
  } finally {
    loadingMessages.value = false
  }
}

const marquerLu = async (id) => {
  try {
    await $api.post(`/candidats/me/messages/${id}/lu`, {}, { headers: authHeaders() })
    const message = messages.value.find(m => m.id === id)
    if (message && !message.lu) {
      message.lu = true
      messagesNonLus.value = Math.max(0, messagesNonLus.value - 1)
    }
  } catch (error) {
    console.error('Erreur marquage message lu:', error)
  }
}

const toutMarquerLu = async () => {
  try {
    await $api.post('/candidats/me/messages/tout-lu', {}, { headers: authHeaders() })
    messages.value.forEach(m => { m.lu = true })
    messagesNonLus.value = 0
  } catch (error) {
    console.error('Erreur marquage messages lus:', error)
  }
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

// Langues, diplômes, expériences du candidat
const loadLangues = async () => {
  const response = await $api.get('/candidats/me/langues', { headers: authHeaders() })
  langues.value = response.langues || []
}

const loadDiplomes = async () => {
  const response = await $api.get('/candidats/me/diplomes', { headers: authHeaders() })
  diplomes.value = response.diplomes || []
}

const loadExperiences = async () => {
  const response = await $api.get('/candidats/me/experiences', { headers: authHeaders() })
  experiences.value = response.experiences || []
}

// Référentiels pour les select (langue, établissement, domaine, structure)
const loadReferenceLists = async () => {
  const [l, e, d, s] = await Promise.all([
    $api.get('/langues', { headers: authHeaders() }),
    $api.get('/etablissements', { headers: authHeaders() }),
    $api.get('/domaines', { headers: authHeaders() }),
    $api.get('/structures', { headers: authHeaders() }),
  ])
  referenceLangues.value = Array.isArray(l) ? l : (l?.data || [])
  referenceEtablissements.value = Array.isArray(e) ? e : (e?.data || [])
  referenceDomaines.value = Array.isArray(d) ? d : (d?.data || [])
  referenceStructures.value = Array.isArray(s) ? s : (s?.data || [])
}

const addLangue = async () => {
  try {
    await $api.post('/candidats/me/langues', newLangue.value, { headers: authHeaders() })
    newLangue.value = { langue_id: '', niveau: '' }
    await loadLangues()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Impossible d\'ajouter cette langue' })
  }
}

const deleteLangue = async (id) => {
  try {
    await $api.delete(`/candidats/me/langues/${id}`, { headers: authHeaders() })
    await loadLangues()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Suppression impossible' })
  }
}

const addDiplome = async () => {
  try {
    const formData = new FormData()
    formData.append('intitule', newDiplome.value.intitule)
    if (newDiplome.value.etablissement_id) formData.append('etablissement_id', newDiplome.value.etablissement_id)
    if (newDiplome.value.domaine_id) formData.append('domaine_id', newDiplome.value.domaine_id)
    if (newDiplome.value.annee) formData.append('annee', newDiplome.value.annee)
    if (newDiplome.value.type) formData.append('type', newDiplome.value.type)
    if (newDiplome.value.justificatif) formData.append('justificatif', newDiplome.value.justificatif)

    await $api.post('/candidats/me/diplomes', formData, {
      headers: { ...authHeaders(), 'Content-Type': 'multipart/form-data' }
    })
    newDiplome.value = { intitule: '', etablissement_id: '', domaine_id: '', annee: '', type: '', justificatif: null }
    await loadDiplomes()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Impossible d\'ajouter ce diplôme' })
  }
}

const deleteDiplome = async (id) => {
  try {
    await $api.delete(`/candidats/me/diplomes/${id}`, { headers: authHeaders() })
    await loadDiplomes()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Suppression impossible' })
  }
}

const addExperience = async () => {
  try {
    const formData = new FormData()
    formData.append('intitule', newExperience.value.intitule)
    if (newExperience.value.structure_id) formData.append('structure_id', newExperience.value.structure_id)
    if (newExperience.value.date_debut) formData.append('date_debut', newExperience.value.date_debut)
    if (newExperience.value.date_fin) formData.append('date_fin', newExperience.value.date_fin)
    if (newExperience.value.justificatif) formData.append('justificatif', newExperience.value.justificatif)

    await $api.post('/candidats/me/experiences', formData, {
      headers: { ...authHeaders(), 'Content-Type': 'multipart/form-data' }
    })
    newExperience.value = { intitule: '', structure_id: '', date_debut: '', date_fin: '', justificatif: null }
    await loadExperiences()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Impossible d\'ajouter cette expérience' })
  }
}

const deleteExperience = async (id) => {
  try {
    await $api.delete(`/candidats/me/experiences/${id}`, { headers: authHeaders() })
    await loadExperiences()
  } catch (error) {
    $swal.fire({ icon: 'error', title: 'Erreur', text: error.response?.data?.message || 'Suppression impossible' })
  }
}

// Déconnexion
const logout = async () => {
  const result = await $swal.fire({
    title: 'Déconnexion',
    text: 'Êtes-vous sûr de vouloir vous déconnecter ?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#dc2626',
    confirmButtonText: 'Oui, me déconnecter',
    cancelButtonText: 'Annuler'
  })

  if (result.isConfirmed) {
    try {
      const token = localStorage.getItem('candidat_token')
      
      await $api.post('/candidats/logout', {}, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
    } catch (error) {
      console.error('Erreur de déconnexion:', error)
    } finally {
      localStorage.removeItem('candidat_token')
      router.push('/accueil')
    }
  }
}

// Utilitaires
const getInitials = (name) => {
  if (!name) return '?'
  const parts = name.split(' ')
  return parts.length >= 2 
    ? `${parts[0][0]}${parts[parts.length - 1][0]}`.toUpperCase()
    : name.substring(0, 2).toUpperCase()
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const getDocumentIcon = (type) => {
  const icons = {
    cv: 'FileText',
    diplome: 'GraduationCap',
    attestation: 'FileCheck',
    lettre: 'Mail',
    casier: 'Shield',
    medical: 'Heart',
    passeport: 'Passport',
    autre: 'Paperclip'
  }
  return icons[type] || 'Paperclip'
}

const getDocumentIconComponent = (type) => {
  const iconMap = {
    cv: FileText,
    diplome: GraduationCap,
    attestation: FileText, // FileCheck n'existe pas dans lucide-vue-next
    lettre: Mail,
    casier: Shield,
    medical: Heart,
    passeport: FileText, // Passport n'existe pas dans lucide-vue-next
    autre: Paperclip
  }
  return iconMap[type] || Paperclip
}

const getDocumentTypeLabel = (type) => {
  const labels = {
    cv: 'CV',
    diplome: 'Diplôme',
    attestation: 'Attestation',
    lettre: 'Lettre de motivation',
    casier: 'Casier judiciaire',
    medical: 'Certificat médical',
    passeport: 'Passeport',
    autre: 'Autre'
  }
  return labels[type] || 'Document'
}

// Chargement au montage
onMounted(() => {
  loadCandidatData()
})

// SEO
useHead({
  title: 'Mon Dashboard - Gestion Dignitaires',
  meta: [
    { name: 'description', content: 'Dashboard candidat - Suivez l\'état de votre candidature' }
  ]
})
</script>

<style scoped>
/* Filet de sécurité : si un élément déborde malgré tout, on peut au moins
   défilé horizontalement pour le voir, plutôt que de le perdre définitivement
   derrière un overflow-x: hidden. */
html, body {
  overflow-x: auto;
}

/* assets/css/global.css force `main { width: 100% }` pour toute l'app (pensé
   pour les layouts flex, cf. DashboardLayout). Ce <main> utilise lg:ml-72
   (margin-left) plutôt que flex : width:100% + margin-left additionne 288px
   en trop et fait déborder toute la page hors de l'écran. On neutralise la
   règle globale ici uniquement (le sélecteur scopé a une spécificité plus
   forte que le `main {}` global). */
main {
  width: auto !important;
  max-width: none !important;
}

/* Forcer tous les éléments à ne pas dépasser */
* {
  box-sizing: border-box;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* Tables et grids responsives */
table {
  max-width: 100% !important;
  table-layout: fixed;
}

.grid {
  max-width: 100% !important;
}

/* Truncate les textes longs */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Scroll smooth */
html {
  scroll-behavior: smooth;
}

.scroll-mt-32 {
  scroll-margin-top: 8rem;
}

/* Sidebar scrollbar */
aside::-webkit-scrollbar {
  width: 6px;
}

aside::-webkit-scrollbar-track {
  background: #f1f1f1;
}

aside::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 3px;
}

aside::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}

/* Animation pulse */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Hover effet sidebar */
aside nav button:hover:not(.bg-gradient-to-r) {
  transform: translateX(4px);
}

/* Line clamp utility */
.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}
</style>
