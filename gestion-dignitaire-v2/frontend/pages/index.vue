<template>
  <div></div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()

if (process.client && !authStore.token) {
  const token = localStorage.getItem('auth_token')
  if (token) {
    await authStore.loadFromStorage()
  }
}

if (authStore.isAuthenticated) {
  await navigateTo('/dashboard')
} else {
  await navigateTo('/accueil')
}
</script>
