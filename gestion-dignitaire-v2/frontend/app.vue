<template>
  <div>
    <NuxtPage />
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()

// Charger l'authentification au démarrage
onMounted(() => {
  if (process.client && !authStore.token) {
    const token = localStorage.getItem('auth_token')
    const user = localStorage.getItem('user')
    const tokenExpiry = localStorage.getItem('token_expiry')
    
    if (token && user) {
      authStore.token = token
      authStore.user = JSON.parse(user)
      authStore.tokenExpiry = tokenExpiry
    }
  }
})
</script>
