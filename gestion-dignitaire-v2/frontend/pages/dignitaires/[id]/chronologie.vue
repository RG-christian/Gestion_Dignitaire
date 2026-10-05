<template>
  <DashboardLayout>
    <main class="min-h-screen bg-gray-50 px-4 py-6 md:px-8">
      <div class="mx-auto max-w-7xl">
        <NuxtLink :to="`/dignitaires/${route.params.id}`" class="mb-5 inline-flex items-center text-sm font-semibold text-blue-700 hover:text-blue-900">
          ← Retour à la fiche du dignitaire
        </NuxtLink>

        <div v-if="loading" class="rounded-2xl bg-white p-12 text-center shadow-sm">
          <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-green-600 border-t-transparent"></div>
          <p class="mt-4 text-gray-500">Chargement de la chronologie…</p>
        </div>

        <div v-else-if="error" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-red-700">
          {{ error }}
        </div>

        <template v-else-if="data">
          <header class="mb-7 rounded-2xl bg-gradient-to-r from-green-700 via-yellow-500 to-blue-700 p-7 text-white shadow-lg">
            <p class="text-sm font-medium text-white/80">Parcours consolidé</p>
            <h1 class="mt-1 text-3xl font-bold">{{ data.dignitaire.nom_complet }}</h1>
            <p class="mt-2">Matricule : {{ data.dignitaire.matricule || 'Non renseigné' }}</p>
          </header>

          <div class="mb-6 flex flex-wrap gap-2" role="tablist" aria-label="Types de chronologie">
            <button v-for="tab in tabs" :key="tab.key" type="button" class="rounded-full px-5 py-2.5 text-sm font-semibold transition" :class="activeTab === tab.key ? tab.activeClass : 'bg-white text-gray-600 shadow-sm hover:bg-gray-100'" @click="activeTab = tab.key">
              {{ tab.label }} ({{ tab.count }})
            </button>
          </div>

          <section v-if="activeTab === 'familiale'" class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold text-gray-900">Chronologie familiale</h2>
            <p class="mb-6 mt-1 text-sm text-gray-500">Naissances, unions et fins d’union conservées dans le temps</p>
            <ChronologyTimeline v-if="data.familiale.length" :items="familyItems" color="green" />
            <p v-else class="rounded-xl bg-gray-50 p-8 text-center text-gray-500">Aucun événement familial enregistré.</p>
          </section>

          <section v-if="activeTab === 'academique'" class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold text-gray-900">Chronologie académique</h2>
            <p class="mb-6 mt-1 text-sm text-gray-500">Diplômes triés par année et regroupés par niveau</p>
            <div v-if="academicGroups.length" class="space-y-7">
              <div v-for="group in academicGroups" :key="group.niveau">
                <h3 class="mb-3 border-b border-yellow-200 pb-2 font-bold text-yellow-800">{{ group.niveau }}</h3>
                <ChronologyTimeline :items="group.items" color="yellow" />
              </div>
            </div>
            <p v-else class="rounded-xl bg-gray-50 p-8 text-center text-gray-500">Aucun diplôme enregistré.</p>
          </section>

          <section v-if="activeTab === 'professionnelle'" class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold text-gray-900">Chronologie professionnelle</h2>
            <p class="mb-6 mt-1 text-sm text-gray-500">Expériences les plus récentes en premier ; les postes sans date de fin sont signalés en cours</p>
            <ChronologyTimeline v-if="data.professionnelle.length" :items="professionalItems" color="blue" />
            <p v-else class="rounded-xl bg-gray-50 p-8 text-center text-gray-500">Aucune expérience professionnelle enregistrée.</p>
          </section>
        </template>
      </div>
    </main>
  </DashboardLayout>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })

type TimelineItem = { id: string | number; date: string | null; title: string; description?: string | null; badge?: string | null }
type Chronologie = {
  dignitaire: { id: number; nom_complet: string; matricule: string | null }
  familiale: Array<{ id: string; type: string; date: string | null; titre: string; description: string | null; statut: string | null }>
  academique: { elements: Array<{ id: number; annee: string | null; intitule: string; niveau: string; etablissement: string | null; domaine: string | null }> }
  professionnelle: Array<{ id: number; date_debut: string | null; date_fin: string | null; en_cours: boolean; intitule: string; structure: string | null }>
}

const route = useRoute()
const config = useRuntimeConfig()
const authStore = useAuthStore()
const loading = ref(true)
const error = ref('')
const data = ref<Chronologie | null>(null)
const activeTab = ref<'familiale' | 'academique' | 'professionnelle'>('familiale')

const formatDate = (value: string | null) => value
  ? new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(`${value}T00:00:00`))
  : 'Date non renseignée'

const tabs = computed(() => [
  { key: 'familiale' as const, label: 'Familiale', count: data.value?.familiale.length ?? 0, activeClass: 'bg-green-700 text-white' },
  { key: 'academique' as const, label: 'Académique', count: data.value?.academique.elements.length ?? 0, activeClass: 'bg-yellow-500 text-white' },
  { key: 'professionnelle' as const, label: 'Professionnelle', count: data.value?.professionnelle.length ?? 0, activeClass: 'bg-blue-700 text-white' },
])

const familyItems = computed<TimelineItem[]>(() => (data.value?.familiale ?? []).map(item => ({
  id: item.id,
  date: item.date,
  title: item.titre,
  description: item.description,
  badge: item.type === 'fin_union' ? item.statut : null,
})))

const academicGroups = computed(() => {
  const groups = new Map<string, TimelineItem[]>()
  for (const item of data.value?.academique.elements ?? []) {
    const items = groups.get(item.niveau) ?? []
    items.push({ id: item.id, date: item.annee, title: item.intitule, description: [item.etablissement, item.domaine].filter(Boolean).join(' • ') })
    groups.set(item.niveau, items)
  }
  return Array.from(groups, ([niveau, items]) => ({ niveau, items }))
})

const professionalItems = computed<TimelineItem[]>(() => (data.value?.professionnelle ?? []).map(item => ({
  id: item.id,
  date: item.date_debut,
  title: item.intitule,
  description: `${item.structure || 'Structure non renseignée'} • ${formatDate(item.date_debut)} — ${item.en_cours ? 'Aujourd’hui' : formatDate(item.date_fin)}`,
  badge: item.en_cours ? 'En cours' : 'Terminée',
})))

onMounted(async () => {
  try {
    data.value = await $fetch<Chronologie>(`${config.public.apiBase}/dignitaires/${route.params.id}/chronologie`, {
      headers: { Authorization: `Bearer ${authStore.token}` },
    })
  } catch (exception: any) {
    error.value = exception?.data?.message || 'Impossible de charger la chronologie.'
  } finally {
    loading.value = false
  }
})
</script>
