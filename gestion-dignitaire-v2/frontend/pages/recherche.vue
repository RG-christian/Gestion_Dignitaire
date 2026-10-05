<template>
  <DashboardLayout>
    <div class="mx-auto max-w-7xl p-6">
      <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Recherche multicritère</h1>
        <p class="mt-1 text-sm text-gray-600">Les critères renseignés sont combinés : un dignitaire doit tous les respecter.</p>
      </header>

      <form class="mb-6 rounded-xl bg-white p-5 shadow" @submit.prevent="search(1)">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
          <label class="text-sm">Nom, prénom, matricule ou NIP<input v-model="filters.q" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Texte libre"></label>
          <label class="text-sm">Poste<select v-model="filters.poste" class="mt-1 w-full rounded-lg border px-3 py-2"><option value="">Tous</option><option v-for="v in options.postes" :key="v" :value="v">{{ v }}</option></select></label>
          <label class="text-sm">Pays d'affectation<select v-model="filters.pays_affectation_id" class="mt-1 w-full rounded-lg border px-3 py-2"><option value="">Tous</option><option v-for="v in options.pays" :key="v.id" :value="v.id">{{ v.nom }}</option></select></label>
          <label class="text-sm">Langue<select v-model="filters.langue_id" class="mt-1 w-full rounded-lg border px-3 py-2"><option value="">Toutes</option><option v-for="v in options.langues" :key="v.id" :value="v.id">{{ v.nom }}</option></select></label>
          <label class="text-sm">Domaine<select v-model="filters.domaine_id" class="mt-1 w-full rounded-lg border px-3 py-2"><option value="">Tous</option><option v-for="v in options.domaines" :key="v.id" :value="v.id">{{ v.nom }}</option></select></label>
          <label class="text-sm">Niveau académique<select v-model="filters.niveau_academique" class="mt-1 w-full rounded-lg border px-3 py-2"><option value="">Tous</option><option v-for="v in options.niveaux_academiques" :key="v" :value="v">{{ v }}</option></select></label>
          <label class="text-sm">Structure professionnelle<select v-model="filters.structure_id" class="mt-1 w-full rounded-lg border px-3 py-2"><option value="">Toutes</option><option v-for="v in options.structures" :key="v.id" :value="v.id">{{ v.nom }}</option></select></label>
          <label class="text-sm">Mandat<select v-model="filters.mandat_actif" class="mt-1 w-full rounded-lg border px-3 py-2"><option value="">Tous</option><option value="1">En cours</option><option value="0">Sans mandat en cours</option></select></label>
          <label class="text-sm">Statut militaire<select v-model="filters.est_militaire" class="mt-1 w-full rounded-lg border px-3 py-2"><option value="">Tous</option><option value="1">Militaire</option><option value="0">Non militaire</option></select></label>
        </div>
        <div class="mt-5 flex gap-3">
          <button :disabled="loading" class="rounded-lg bg-blue-600 px-5 py-2 font-semibold text-white disabled:opacity-50">{{ loading ? 'Recherche…' : 'Rechercher' }}</button>
          <button type="button" class="rounded-lg border px-5 py-2 text-gray-700" @click="reset">Réinitialiser</button>
        </div>
      </form>

      <div class="overflow-hidden rounded-xl bg-white shadow">
        <div class="border-b px-5 py-4 font-semibold">{{ pagination.total }} résultat(s)</div>
        <div v-if="error" class="p-6 text-red-600">{{ error }}</div>
        <div v-else-if="!results.length" class="p-10 text-center text-gray-500">Aucun dignitaire ne correspond aux critères.</div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full text-sm"><thead class="bg-gray-50"><tr><th class="p-3 text-left">Dignitaire</th><th class="p-3 text-left">Matricule</th><th class="p-3 text-left">Statut</th><th class="p-3 text-left">Poste récent</th><th class="p-3"></th></tr></thead>
            <tbody><tr v-for="item in results" :key="item.id" class="border-t"><td class="p-3 font-semibold">{{ item.prenom }} {{ item.nom }}</td><td class="p-3">{{ item.matricule }}</td><td class="p-3">{{ item.statut }}</td><td class="p-3">{{ item.postes?.[0]?.intitule || '—' }}</td><td class="p-3 text-right"><NuxtLink :to="`/dignitaires/${item.id}`" class="font-semibold text-blue-600">Voir</NuxtLink></td></tr></tbody>
          </table>
        </div>
        <div v-if="pagination.last_page > 1" class="flex items-center justify-between border-t p-4"><button :disabled="pagination.current_page <= 1" @click="search(pagination.current_page - 1)">Précédent</button><span>Page {{ pagination.current_page }} / {{ pagination.last_page }}</span><button :disabled="pagination.current_page >= pagination.last_page" @click="search(pagination.current_page + 1)">Suivant</button></div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })
const config = useRuntimeConfig()
const authStore = useAuthStore()
const options = ref<any>({ postes: [], pays: [], langues: [], domaines: [], niveaux_academiques: [], structures: [] })
const results = ref<any[]>([])
const loading = ref(false)
const error = ref('')
const pagination = reactive({ total: 0, current_page: 1, last_page: 1 })
const emptyFilters = () => ({ q: '', poste: '', pays_affectation_id: '', langue_id: '', domaine_id: '', niveau_academique: '', structure_id: '', mandat_actif: '', est_militaire: '' })
const filters = reactive(emptyFilters())
const headers = computed(() => ({ Authorization: `Bearer ${authStore.token}` }))

async function loadOptions() {
  options.value = await $fetch(`${config.public.apiBase}/search/advanced/options`, { headers: headers.value })
}
async function search(page = 1) {
  loading.value = true
  error.value = ''
  try {
    const params = Object.fromEntries(Object.entries(filters).filter(([, value]) => value !== ''))
    const response: any = await $fetch(`${config.public.apiBase}/search/advanced`, { params: { ...params, page, per_page: 20 }, headers: headers.value })
    results.value = response.results.data
    pagination.total = response.results.total
    pagination.current_page = response.results.current_page
    pagination.last_page = response.results.last_page
  } catch (e) {
    console.error(e)
    error.value = 'La recherche n’a pas pu être exécutée.'
  } finally { loading.value = false }
}
function reset() { Object.assign(filters, emptyFilters()); search(1) }
onMounted(async () => { await loadOptions(); await search(1) })
useHead({ title: 'Recherche multicritère - Gestion Dignitaires' })
</script>
