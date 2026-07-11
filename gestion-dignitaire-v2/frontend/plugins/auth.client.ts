export default defineNuxtPlugin(() => {
  const authStore = useAuthStore()

  // Restaure la session depuis localStorage au démarrage, puis revalide
  // auprès de l'API (authStore.loadFromStorage -> fetchUser) pour éviter
  // d'afficher un menu basé sur des fonctions/sous-fonctions périmées
  // (ex: droits modifiés côté admin depuis la dernière connexion).
  // Pas d'await : on ne bloque pas le rendu initial, le store se met à
  // jour dès que la réponse arrive.
  if (process.client) {
    authStore.loadFromStorage()
  }
})
