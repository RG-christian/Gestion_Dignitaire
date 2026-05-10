<template>
  <DashboardLayout>
    <header class="bg-white shadow-sm p-6">
      <h2 class="text-3xl font-bold text-gray-800">Gestion des Expériences</h2>
      <p class="text-gray-600 mt-1">Liste des expériences professionnelles</p>
    </header>

    <div class="p-6">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <p class="text-gray-500">Page en cours de développement...</p>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})
</script>
