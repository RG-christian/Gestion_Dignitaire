<template>
  <DashboardLayout>
    <header class="bg-gradient-to-r from-gray-700 to-gray-900 p-6 text-white shadow-lg"><h1 class="text-3xl font-bold">Archives métier</h1><p class="mt-1">Consulter et restaurer les enregistrements archivés.</p></header>
    <section class="p-6">
      <div v-if="loading" class="py-12 text-center text-gray-500">Chargement des archives...</div>
      <div v-else class="space-y-6">
        <div v-for="group in groups" :key="group.key" class="rounded-xl bg-white p-5 shadow">
          <h2 class="mb-4 text-lg font-bold">{{ group.label }} ({{ group.rows.length }})</h2>
          <div v-if="group.rows.length" class="divide-y"><div v-for="row in group.rows" :key="row.id" class="flex items-center justify-between gap-4 py-3"><div><p class="font-semibold">{{ row.libelle }}</p><p class="text-xs text-gray-500">Archivé le {{ formatDate(row.deleted_at) }}</p></div><button @click="restore(group.key, row.id)" class="rounded-lg bg-green-50 px-4 py-2 font-semibold text-green-700 hover:bg-green-100">Restaurer</button></div></div>
          <p v-else class="text-sm text-gray-500">Aucune archive.</p>
        </div>
      </div>
    </section>
  </DashboardLayout>
</template>

<script setup>
definePageMeta({ middleware: 'auth' })
const config = useRuntimeConfig()
const authStore = useAuthStore()
const archives = ref({})
const loading = ref(true)
const labels = { dignitaires: 'Dignitaires', nominations: 'Nominations', postes: 'Postes', affectations: 'Affectations', conjoints: 'Conjoints', 'decoration-attributions': 'Attributions de décorations' }
const groups = computed(() => Object.entries(labels).map(([key, label]) => ({ key, label, rows: archives.value[key] || [] })))
const headers = () => ({ Authorization: `Bearer ${authStore.token}` })
function formatDate(value) { return value ? new Date(value).toLocaleString('fr-FR') : 'N/A' }
async function load() { loading.value = true; try { archives.value = await $fetch(`${config.public.apiBase}/admin/archives`, { headers: headers() }) } finally { loading.value = false } }
async function restore(type, id) { await $fetch(`${config.public.apiBase}/${type}/${id}/restaurer`, { method: 'POST', headers: headers() }); await load(); useNuxtApp().$swal.fire({ icon: 'success', title: 'Enregistrement restauré', timer: 1500, showConfirmButton: false }) }
onMounted(load)
</script>
