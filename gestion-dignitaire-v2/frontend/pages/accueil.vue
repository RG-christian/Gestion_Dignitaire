<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gabon-green-50">
    <!-- Navbar Flottante Professionnelle -->
    <nav class="fixed top-4 left-4 right-4 z-50">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-gray-200/50 px-6 py-4">
          <div class="flex items-center justify-between">
            <!-- Logo et titre -->
            <div class="flex items-center gap-3">
              <div class="bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-xl p-2.5 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <h1 class="text-xl font-bold text-gray-900 leading-none">Gestion Dignitaires</h1>
                <p class="text-xs text-gray-600">République Gabonaise</p>
              </div>
            </div>

            <!-- Navigation Desktop -->
            <div class="hidden md:flex items-center gap-2">
              <a href="#accueil" class="px-4 py-2 text-gray-700 hover:text-gabon-green-600 hover:bg-gabon-green-50 rounded-lg font-medium transition-all duration-200">Accueil</a>
              <a href="#fonctionnalites" class="px-4 py-2 text-gray-700 hover:text-gabon-green-600 hover:bg-gabon-green-50 rounded-lg font-medium transition-all duration-200">Fonctionnalités</a>
              <a href="#documents" class="px-4 py-2 text-gray-700 hover:text-gabon-green-600 hover:bg-gabon-green-50 rounded-lg font-medium transition-all duration-200">Documents</a>
              <a href="#processus" class="px-4 py-2 text-gray-700 hover:text-gabon-green-600 hover:bg-gabon-green-50 rounded-lg font-medium transition-all duration-200">Processus</a>
              <a href="#temoignages" class="px-4 py-2 text-gray-700 hover:text-gabon-green-600 hover:bg-gabon-green-50 rounded-lg font-medium transition-all duration-200">Témoignages</a>
              <a href="#faq" class="px-4 py-2 text-gray-700 hover:text-gabon-green-600 hover:bg-gabon-green-50 rounded-lg font-medium transition-all duration-200">FAQ</a>
              <div class="w-px h-8 bg-gray-300 mx-2"></div>
              <NuxtLink to="/candidature/login" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg font-medium transition-colors duration-200">Connexion</NuxtLink>
              <NuxtLink to="/candidature" class="px-6 py-2.5 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-semibold rounded-xl shadow-lg shadow-gabon-green-600/30 hover:shadow-xl hover:shadow-gabon-green-600/40 transition-all duration-300">
                Devenir Dignitaire
              </NuxtLink>
            </div>

            <!-- Menu Mobile -->
            <button @click="showMobileMenu = !showMobileMenu" class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!showMobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Menu Mobile Déroulant -->
    <Transition
      enter-active-class="transition-all duration-300"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div v-if="showMobileMenu" class="md:hidden fixed top-24 left-4 right-4 z-40">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-4 space-y-2">
          <a href="#accueil" @click="showMobileMenu = false" class="block px-4 py-3 text-gray-700 hover:bg-gabon-green-50 hover:text-gabon-green-600 rounded-xl font-medium transition-colors">Accueil</a>
          <a href="#fonctionnalites" @click="showMobileMenu = false" class="block px-4 py-3 text-gray-700 hover:bg-gabon-green-50 hover:text-gabon-green-600 rounded-xl font-medium transition-colors">Fonctionnalités</a>
          <a href="#documents" @click="showMobileMenu = false" class="block px-4 py-3 text-gray-700 hover:bg-gabon-green-50 hover:text-gabon-green-600 rounded-xl font-medium transition-colors">Documents</a>
          <a href="#processus" @click="showMobileMenu = false" class="block px-4 py-3 text-gray-700 hover:bg-gabon-green-50 hover:text-gabon-green-600 rounded-xl font-medium transition-colors">Processus</a>
          <a href="#temoignages" @click="showMobileMenu = false" class="block px-4 py-3 text-gray-700 hover:bg-gabon-green-50 hover:text-gabon-green-600 rounded-xl font-medium transition-colors">Témoignages</a>
          <a href="#faq" @click="showMobileMenu = false" class="block px-4 py-3 text-gray-700 hover:bg-gabon-green-50 hover:text-gabon-green-600 rounded-xl font-medium transition-colors">FAQ</a>
          <div class="border-t border-gray-200 my-2"></div>
          <NuxtLink to="/candidature/login" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-xl font-medium transition-colors">Connexion</NuxtLink>
          <NuxtLink to="/candidature" class="block px-4 py-3 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 text-white text-center font-semibold rounded-xl shadow-lg">Devenir Dignitaire</NuxtLink>
        </div>
      </div>
    </Transition>

    <!-- Section Hero Moderne -->
    <section id="accueil" class="relative min-h-screen flex items-center pt-32 pb-20 px-4 overflow-hidden">
      <!-- Background decoratif -->
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-20 right-0 w-96 h-96 bg-gabon-green-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-0 w-96 h-96 bg-gabon-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-gabon-yellow-500/5 rounded-full blur-3xl"></div>
      </div>

      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <!-- Contenu Hero -->
          <div class="space-y-8">
            <div class="inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg border border-gray-200">
              <span class="w-2 h-2 bg-gabon-green-500 rounded-full animate-pulse"></span>
              <span class="text-sm font-semibold text-gray-700">Plateforme Officielle</span>
            </div>

            <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
              Plateforme de
              <span class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-600 to-gabon-blue-600 bg-clip-text text-transparent">Gestion</span>
              des Dignitaires
            </h1>

            <p class="text-xl text-gray-600 leading-relaxed">
              Un système moderne et sécurisé pour la gestion des dignitaires de la République Gabonaise. 
              Postulez en ligne, suivez votre dossier et accédez à votre espace personnel.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
              <NuxtLink to="/candidature" class="group px-8 py-4 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white text-lg font-semibold rounded-xl shadow-xl shadow-gabon-green-600/30 hover:shadow-2xl hover:shadow-gabon-green-600/40 transition-all duration-300 flex items-center justify-center gap-2">
                Commencer ma candidature
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
              </NuxtLink>
              
              <NuxtLink to="/candidature/login" class="px-8 py-4 bg-white hover:bg-gray-50 text-gray-700 text-lg font-semibold rounded-xl shadow-lg border-2 border-gray-200 hover:border-gabon-green-600 transition-all duration-300 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Se connecter
              </NuxtLink>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-3 gap-6 pt-8">
              <div class="text-center">
                <div class="text-3xl font-bold text-gabon-green-600">{{ stats?.totalDignitaires ?? '—' }}</div>
                <div class="text-sm text-gray-600 mt-1">Dignitaires</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-gabon-blue-600">{{ stats?.totalPaysCouverts ?? '—' }}</div>
                <div class="text-sm text-gray-600 mt-1">Pays couverts</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-gabon-yellow-600">{{ stats?.delaiMoyenJours ? `${stats.delaiMoyenJours}j` : '—' }}</div>
                <div class="text-sm text-gray-600 mt-1">Délai moyen de traitement</div>
              </div>
            </div>
          </div>

          <!-- Image/Illustration -->
          <div class="relative">
            <div class="relative rounded-3xl overflow-hidden shadow-2xl">
              <div class="aspect-square bg-gradient-to-br from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 p-1">
                <div class="w-full h-full bg-white rounded-3xl flex items-center justify-center">
                  <svg class="w-64 h-64 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                  </svg>
                </div>
              </div>
            </div>
            <!-- Floating cards -->
            <div class="absolute -top-6 -right-6 bg-white rounded-2xl shadow-xl p-4 border border-gray-200 animate-float">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gabon-green-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-gabon-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-bold text-gray-900">Vérifié</div>
                  <div class="text-xs text-gray-600">Sécurisé</div>
                </div>
              </div>
            </div>
            <div class="absolute -bottom-6 -left-6 bg-white rounded-2xl shadow-xl p-4 border border-gray-200 animate-float-delayed">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gabon-blue-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-gabon-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-bold text-gray-900">Rapide</div>
                  <div class="text-xs text-gray-600">En ligne</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Bandeau de confiance -->
    <section class="py-10 px-4 bg-white border-y border-gray-100">
      <div class="max-w-7xl mx-auto">
        <p class="text-center text-xs font-semibold uppercase tracking-widest text-gray-400 mb-6">Une plateforme officielle et reconnue</p>
        <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
          <div class="flex items-center gap-2 text-gray-500">
            <Landmark class="w-5 h-5 text-gabon-green-600" :stroke-width="1.75" />
            <span class="font-semibold text-sm">Présidence de la République</span>
          </div>
          <div class="flex items-center gap-2 text-gray-500">
            <Landmark class="w-5 h-5 text-gabon-blue-600" :stroke-width="1.75" />
            <span class="font-semibold text-sm">Primature</span>
          </div>
          <div class="flex items-center gap-2 text-gray-500">
            <Landmark class="w-5 h-5 text-gabon-yellow-600" :stroke-width="1.75" />
            <span class="font-semibold text-sm">Ministère de la Fonction Publique</span>
          </div>
          <div class="flex items-center gap-2 text-gray-500">
            <ShieldCheck class="w-5 h-5 text-gabon-green-600" :stroke-width="1.75" />
            <span class="font-semibold text-sm">Données hébergées et sécurisées</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Section Fonctionnalités -->
    <section id="fonctionnalites" class="py-24 px-4 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Fonctionnalités Principales</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">Une plateforme complète pour simplifier la gestion des dignitaires</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="group relative bg-gradient-to-br from-white to-gabon-green-50 rounded-2xl p-8 shadow-lg hover:shadow-2xl border border-gray-200 hover:border-gabon-green-600 transition-all duration-300 cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gabon-green-500/5 rounded-bl-full"></div>
            <div class="relative">
              <div class="w-14 h-14 bg-gradient-to-br from-gabon-green-600 to-gabon-green-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Candidature en ligne</h3>
              <p class="text-gray-600 leading-relaxed">Postulez directement en ligne avec un formulaire simple et intuitif. Suivez l'état de votre candidature en temps réel.</p>
            </div>
          </div>

          <!-- Feature 2 -->
          <div class="group relative bg-gradient-to-br from-white to-gabon-blue-50 rounded-2xl p-8 shadow-lg hover:shadow-2xl border border-gray-200 hover:border-gabon-blue-600 transition-all duration-300 cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gabon-blue-500/5 rounded-bl-full"></div>
            <div class="relative">
              <div class="w-14 h-14 bg-gradient-to-br from-gabon-blue-600 to-gabon-blue-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Documents sécurisés</h3>
              <p class="text-gray-600 leading-relaxed">Téléchargez vos documents (CV, diplômes, attestations) en toute sécurité. Stockage crypté et confidentiel.</p>
            </div>
          </div>

          <!-- Feature 3 -->
          <div class="group relative bg-gradient-to-br from-white to-gabon-yellow-50 rounded-2xl p-8 shadow-lg hover:shadow-2xl border border-gray-200 hover:border-gabon-yellow-600 transition-all duration-300 cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gabon-yellow-500/5 rounded-bl-full"></div>
            <div class="relative">
              <div class="w-14 h-14 bg-gradient-to-br from-gabon-yellow-600 to-gabon-yellow-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Validation rapide</h3>
              <p class="text-gray-600 leading-relaxed">Les administrateurs peuvent valider ou refuser les candidatures rapidement avec un système de notifications.</p>
            </div>
          </div>

          <!-- Feature 4 -->
          <div class="group relative bg-gradient-to-br from-white to-purple-50 rounded-2xl p-8 shadow-lg hover:shadow-2xl border border-gray-200 hover:border-purple-600 transition-all duration-300 cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/5 rounded-bl-full"></div>
            <div class="relative">
              <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Espace personnel</h3>
              <p class="text-gray-600 leading-relaxed">Accédez à votre dashboard personnel pour consulter vos informations et suivre votre statut.</p>
            </div>
          </div>

          <!-- Feature 5 -->
          <div class="group relative bg-gradient-to-br from-white to-red-50 rounded-2xl p-8 shadow-lg hover:shadow-2xl border border-gray-200 hover:border-red-600 transition-all duration-300 cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-500/5 rounded-bl-full"></div>
            <div class="relative">
              <div class="w-14 h-14 bg-gradient-to-br from-red-600 to-red-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Sécurité maximale</h3>
              <p class="text-gray-600 leading-relaxed">Authentification sécurisée, données cryptées et conformité aux normes de sécurité les plus strictes.</p>
            </div>
          </div>

          <!-- Feature 6 -->
          <div class="group relative bg-gradient-to-br from-white to-indigo-50 rounded-2xl p-8 shadow-lg hover:shadow-2xl border border-gray-200 hover:border-indigo-600 transition-all duration-300 cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-bl-full"></div>
            <div class="relative">
              <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Suivi et statistiques</h3>
              <p class="text-gray-600 leading-relaxed">Tableaux de bord complets avec statistiques détaillées pour les administrateurs.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section Documents à préparer -->
    <section id="documents" class="py-24 px-4 bg-gradient-to-br from-slate-50 to-gabon-blue-50">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Documents à préparer</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">Préparez ces pièces à l'avance pour compléter votre dossier plus rapidement</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 text-center">
            <IdCard class="w-9 h-9 mx-auto mb-3 text-gabon-green-600" :stroke-width="1.5" />
            <h3 class="font-bold text-gray-900 mb-1">Pièce d'identité</h3>
            <p class="text-sm text-gray-600">NIP, matricule et photo d'identité</p>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 text-center">
            <FileText class="w-9 h-9 mx-auto mb-3 text-gabon-blue-600" :stroke-width="1.5" />
            <h3 class="font-bold text-gray-900 mb-1">CV</h3>
            <p class="text-sm text-gray-600">Curriculum vitae à jour</p>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 text-center">
            <GraduationCap class="w-9 h-9 mx-auto mb-3 text-gabon-yellow-600" :stroke-width="1.5" />
            <h3 class="font-bold text-gray-900 mb-1">Diplômes</h3>
            <p class="text-sm text-gray-600">Diplômes obtenus avec leurs justificatifs</p>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 text-center">
            <ShieldCheck class="w-9 h-9 mx-auto mb-3 text-red-600" :stroke-width="1.5" />
            <h3 class="font-bold text-gray-900 mb-1">Casier judiciaire</h3>
            <p class="text-sm text-gray-600">Extrait de casier judiciaire</p>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 text-center">
            <HeartPulse class="w-9 h-9 mx-auto mb-3 text-purple-600" :stroke-width="1.5" />
            <h3 class="font-bold text-gray-900 mb-1">Certificat médical</h3>
            <p class="text-sm text-gray-600">Certificat médical récent</p>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 text-center">
            <Stamp class="w-9 h-9 mx-auto mb-3 text-indigo-600" :stroke-width="1.5" />
            <h3 class="font-bold text-gray-900 mb-1">Passeport</h3>
            <p class="text-sm text-gray-600">Si vous en possédez un</p>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 text-center">
            <Mail class="w-9 h-9 mx-auto mb-3 text-gabon-green-600" :stroke-width="1.5" />
            <h3 class="font-bold text-gray-900 mb-1">Lettre de motivation</h3>
            <p class="text-sm text-gray-600">Facultative mais recommandée</p>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 text-center">
            <FileCheck2 class="w-9 h-9 mx-auto mb-3 text-gabon-blue-600" :stroke-width="1.5" />
            <h3 class="font-bold text-gray-900 mb-1">Attestations</h3>
            <p class="text-sm text-gray-600">Expériences professionnelles justifiées</p>
          </div>
        </div>

        <p class="text-center text-gray-500 mt-10 text-sm">
          Rassurez-vous : l'inscription reste courte. Vous pourrez compléter vos diplômes, langues, expériences et documents complémentaires directement depuis votre espace personnel, à votre rythme.
        </p>
      </div>
    </section>

    <!-- Section Processus -->
    <section id="processus" class="py-24 px-4 bg-gradient-to-br from-slate-50 to-gabon-green-50">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Comment ça marche ?</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">Un processus simple en 4 étapes</p>
        </div>

        <div class="relative">
          <!-- Ligne de connexion (desktop) -->
          <div class="hidden lg:block absolute top-24 left-0 right-0 h-1 bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-600 to-gabon-blue-600 mx-auto" style="width: 75%;"></div>

          <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 relative">
            <!-- Étape 1 -->
            <div class="relative">
              <div class="bg-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 border-2 border-gray-200 hover:border-gabon-green-600 h-full">
                <div class="flex flex-col items-center text-center">
                  <div class="w-20 h-20 bg-gradient-to-br from-gabon-green-600 to-gabon-green-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg relative z-10">
                    <span class="text-3xl font-bold text-white">1</span>
                  </div>
                  <h3 class="text-xl font-bold text-gray-900 mb-4">Inscription</h3>
                  <p class="text-gray-600 leading-relaxed">Créez votre compte en quelques minutes avec vos informations personnelles</p>
                </div>
              </div>
            </div>

            <!-- Étape 2 -->
            <div class="relative">
              <div class="bg-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 border-2 border-gray-200 hover:border-gabon-yellow-600 h-full">
                <div class="flex flex-col items-center text-center">
                  <div class="w-20 h-20 bg-gradient-to-br from-gabon-yellow-600 to-gabon-yellow-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg relative z-10">
                    <span class="text-3xl font-bold text-white">2</span>
                  </div>
                  <h3 class="text-xl font-bold text-gray-900 mb-4">Documents</h3>
                  <p class="text-gray-600 leading-relaxed">Téléchargez vos documents (CV, diplômes, attestations) en toute sécurité</p>
                </div>
              </div>
            </div>

            <!-- Étape 3 -->
            <div class="relative">
              <div class="bg-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 border-2 border-gray-200 hover:border-gabon-blue-600 h-full">
                <div class="flex flex-col items-center text-center">
                  <div class="w-20 h-20 bg-gradient-to-br from-gabon-blue-600 to-gabon-blue-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg relative z-10">
                    <span class="text-3xl font-bold text-white">3</span>
                  </div>
                  <h3 class="text-xl font-bold text-gray-900 mb-4">Validation</h3>
                  <p class="text-gray-600 leading-relaxed">Nos administrateurs examinent votre dossier et vous notifient par email</p>
                </div>
              </div>
            </div>

            <!-- Étape 4 -->
            <div class="relative">
              <div class="bg-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 border-2 border-gray-200 hover:border-purple-600 h-full">
                <div class="flex flex-col items-center text-center">
                  <div class="w-20 h-20 bg-gradient-to-br from-purple-600 to-purple-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg relative z-10">
                    <span class="text-3xl font-bold text-white">4</span>
                  </div>
                  <h3 class="text-xl font-bold text-gray-900 mb-4">Confirmation</h3>
                  <p class="text-gray-600 leading-relaxed">Accédez à votre espace personnel et consultez vos informations</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- CTA -->
        <div class="text-center mt-16">
          <NuxtLink to="/candidature" class="inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white text-lg font-bold rounded-2xl shadow-2xl shadow-gabon-green-600/40 hover:shadow-gabon-green-600/60 hover:-translate-y-1 transition-all duration-300">
            <span>Commencer maintenant</span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Section Témoignages -->
    <section id="temoignages" class="py-24 px-4 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Ils sont passés par là</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">Ce que disent les dignitaires déjà validés sur la plateforme</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div v-for="temoignage in temoignages" :key="temoignage.nom" class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-8 shadow-lg border border-gray-200 relative">
            <Quote class="w-8 h-8 text-gabon-green-200 absolute top-6 right-6" :stroke-width="1.5" />
            <p class="text-gray-700 leading-relaxed mb-6 relative z-10">{{ temoignage.citation }}</p>
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 flex items-center justify-center text-white font-bold">
                {{ temoignage.initiales }}
              </div>
              <div>
                <p class="font-bold text-gray-900">{{ temoignage.nom }}</p>
                <p class="text-sm text-gray-500">{{ temoignage.poste }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section FAQ -->
    <section id="faq" class="py-24 px-4 bg-gradient-to-br from-slate-50 to-gabon-green-50">
      <div class="max-w-3xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Questions fréquentes</h2>
          <p class="text-xl text-gray-600">Tout ce qu'il faut savoir avant de vous lancer</p>
        </div>

        <div class="space-y-4">
          <div v-for="(item, index) in faqItems" :key="index" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <button @click="toggleFaq(index)" class="w-full flex items-center justify-between gap-4 p-6 text-left">
              <span class="font-bold text-gray-900">{{ item.question }}</span>
              <ChevronDown class="w-5 h-5 text-gray-500 flex-shrink-0 transition-transform duration-300" :class="{ 'rotate-180': openFaq === index }" :stroke-width="2" />
            </button>
            <Transition
              enter-active-class="transition-all duration-300 ease-out"
              enter-from-class="max-h-0 opacity-0"
              enter-to-class="max-h-40 opacity-100"
              leave-active-class="transition-all duration-200 ease-in"
              leave-from-class="max-h-40 opacity-100"
              leave-to-class="max-h-0 opacity-0"
            >
              <div v-if="openFaq === index" class="px-6 pb-6 text-gray-600 leading-relaxed overflow-hidden">
                {{ item.reponse }}
              </div>
            </Transition>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white py-12 px-4">
      <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-3 gap-8 mb-8">
          <!-- Logo et description -->
          <div>
            <div class="flex items-center gap-3 mb-4">
              <div class="bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 rounded-xl p-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <span class="font-bold text-xl">Gestion Dignitaires</span>
            </div>
            <p class="text-gray-400 leading-relaxed">Plateforme officielle de gestion des dignitaires de la République Gabonaise</p>
          </div>

          <!-- Liens rapides -->
          <div>
            <h3 class="font-bold text-lg mb-4">Liens rapides</h3>
            <ul class="space-y-2">
              <li><a href="#accueil" class="text-gray-400 hover:text-gabon-green-400 transition-colors">Accueil</a></li>
              <li><a href="#fonctionnalites" class="text-gray-400 hover:text-gabon-green-400 transition-colors">Fonctionnalités</a></li>
              <li><a href="#processus" class="text-gray-400 hover:text-gabon-green-400 transition-colors">Processus</a></li>
              <li><a href="#temoignages" class="text-gray-400 hover:text-gabon-green-400 transition-colors">Témoignages</a></li>
              <li><a href="#faq" class="text-gray-400 hover:text-gabon-green-400 transition-colors">FAQ</a></li>
              <li><NuxtLink to="/candidature" class="text-gray-400 hover:text-gabon-green-400 transition-colors">Candidature</NuxtLink></li>
            </ul>
          </div>

          <!-- Contact -->
          <div>
            <h3 class="font-bold text-lg mb-4">Contact</h3>
            <ul class="space-y-2 text-gray-400">
              <li class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                contact@dignitaires.ga
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                +241 01 23 45 67
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Libreville, Gabon
              </li>
            </ul>
          </div>
        </div>

        <div class="border-t border-gray-700 pt-8 flex flex-col md:flex-row justify-between items-center">
          <p class="text-gray-400 text-sm mb-4 md:mb-0">© 2026 République Gabonaise. Tous droits réservés.</p>
          <div class="flex gap-4">
            <span class="w-2 h-2 bg-gabon-green-500 rounded-full"></span>
            <span class="w-2 h-2 bg-gabon-yellow-500 rounded-full"></span>
            <span class="w-2 h-2 bg-gabon-blue-500 rounded-full"></span>
          </div>
        </div>
      </div>

      <!-- Bouton retour en haut -->
      <Transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-4"
      >
        <button
          v-if="showScrollTop"
          @click="scrollToTop"
          class="fixed bottom-8 right-8 z-40 w-12 h-12 bg-gradient-to-br from-gabon-green-600 to-gabon-blue-600 hover:shadow-xl text-white rounded-full shadow-lg flex items-center justify-center transition-shadow duration-300"
          aria-label="Retour en haut de la page"
        >
          <ArrowUp class="w-5 h-5" :stroke-width="2.5" />
        </button>
      </Transition>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
  IdCard, FileText, GraduationCap, ShieldCheck, HeartPulse, Stamp, Mail, FileCheck2,
  Landmark, Quote, ChevronDown, ArrowUp
} from 'lucide-vue-next'

const showMobileMenu = ref(false)

// Statistiques réelles pour la section hero
const config = useRuntimeConfig()
const { data: stats } = await useAsyncData('public-stats', async () => {
  try {
    return await $fetch(`${config.public.apiBase}/public/stats`)
  } catch (error) {
    console.error('Erreur chargement statistiques publiques:', error)
    return null
  }
})

// Témoignages (contenu illustratif)
const temoignages = [
  {
    nom: 'A. Ndong',
    poste: 'Conseiller, Ministère des Affaires Étrangères',
    initiales: 'AN',
    citation: "La candidature en ligne m'a fait gagner un temps précieux. J'ai pu suivre l'avancement de mon dossier à chaque étape, sans avoir à me déplacer."
  },
  {
    nom: 'S. Mba',
    poste: 'Directrice, Ministère de l\'Éducation',
    initiales: 'SM',
    citation: "Le dépôt des documents et diplômes est simple et rassurant. J'ai reçu une réponse rapide de l'administration après validation de mon dossier."
  },
  {
    nom: 'J-P. Obiang',
    poste: 'Ambassadeur',
    initiales: 'JO',
    citation: "Une plateforme claire et professionnelle, à l'image de l'institution qu'elle représente. L'espace personnel permet de tout garder sous la main."
  }
]

// FAQ
const faqItems = [
  {
    question: 'Qui peut candidater sur cette plateforme ?',
    reponse: "Toute personne remplissant les conditions d'éligibilité fixées par l'administration peut déposer une candidature. Le dossier sera examiné par nos services avant toute confirmation."
  },
  {
    question: 'Combien de temps prend le traitement de mon dossier ?',
    reponse: "Le délai moyen de traitement est affiché en temps réel en haut de cette page. Il varie selon le nombre de dossiers en cours d'examen."
  },
  {
    question: 'Puis-je compléter mon dossier après l\'inscription ?',
    reponse: "Oui. L'inscription initiale reste volontairement courte. Vous pourrez ensuite ajouter vos diplômes, langues, expériences et documents complémentaires depuis votre espace personnel, à votre rythme, tant que votre dossier est en attente."
  },
  {
    question: 'Comment suis-je informé de la décision ?',
    reponse: "Vous serez notifié directement depuis votre espace personnel, où le statut de votre candidature (en attente, validée ou refusée) est visible à tout moment."
  },
  {
    question: 'Mes documents sont-ils en sécurité ?',
    reponse: "Oui, l'ensemble de vos données et documents est hébergé de manière sécurisée et n'est accessible qu'aux administrateurs habilités à traiter votre dossier."
  }
]
const openFaq = ref(null)
const toggleFaq = (index) => {
  openFaq.value = openFaq.value === index ? null : index
}

// Bouton retour en haut de page
const showScrollTop = ref(false)
const handleScroll = () => {
  showScrollTop.value = window.scrollY > 400
}
const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})
onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

// SEO
useHead({
  title: 'Gestion Dignitaires - République Gabonaise',
  meta: [
    { name: 'description', content: 'Plateforme officielle de gestion des dignitaires de la République Gabonaise. Postulez en ligne et suivez votre candidature.' }
  ]
})
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
}

@keyframes float-delayed {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.animate-float-delayed {
  animation: float-delayed 4s ease-in-out infinite;
  animation-delay: 0.5s;
}
</style>
