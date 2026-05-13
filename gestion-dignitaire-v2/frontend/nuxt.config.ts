// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: false }, // Désactiver en production
  
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@vueuse/nuxt'
  ],

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api'
    }
  },

  app: {
    head: {
      title: 'Gestion des Dignitaires',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Système de gestion des dignitaires - République Gabonaise' }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'stylesheet', href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css' }
      ]
    }
  },

  css: ['~/assets/css/main.css'],

  // Optimisations de build
  vite: {
    build: {
      cssCodeSplit: true,
      rollupOptions: {
        output: {
          manualChunks: {
            'vendor': ['vue', 'pinia'],
          }
        }
      }
    }
  },

  // Optimisations de rendu
  experimental: {
    payloadExtraction: false,
    renderJsonPayloads: true,
    typedPages: false
  },

  compatibilityDate: '2024-04-03'
})
