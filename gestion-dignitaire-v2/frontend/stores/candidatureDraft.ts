import { defineStore } from 'pinia'

/**
 * Pont en mémoire entre la soumission du formulaire de candidature et la
 * page de vérification OTP qui suit : les fichiers (File objects) ne
 * peuvent pas être sérialisés en localStorage, donc on les garde ici le
 * temps de la navigation SPA (candidature/index.vue -> verify-otp.vue).
 */
export const useCandidatureDraftStore = defineStore('candidatureDraft', {
  state: () => ({
    pendingFiles: [] as Array<{ file: File; name: string; size: number; type: string }>
  }),

  actions: {
    setPendingFiles(files: Array<{ file: File; name: string; size: number; type: string }>) {
      this.pendingFiles = files
    },
    clear() {
      this.pendingFiles = []
    }
  }
})
