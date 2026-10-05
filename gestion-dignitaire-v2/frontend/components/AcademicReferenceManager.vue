<template>
  <section class="mx-auto max-w-6xl px-4 py-8">
    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
      <div>
        <NuxtLink to="/diplomes" class="text-sm font-semibold text-blue-700 hover:text-blue-900">← Retour aux diplômes</NuxtLink>
        <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ title }}</h1>
        <p class="mt-1 text-gray-500">{{ description }}</p>
      </div>
      <button v-if="canWrite" type="button" class="rounded-lg bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800" @click="openForm()">
        Ajouter
      </button>
    </div>

    <div class="mb-6 rounded-xl bg-white p-4 shadow-sm">
      <input v-model="search" type="search" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100" :placeholder="`Rechercher un ${singular.toLowerCase()}…`">
    </div>

    <div v-if="loading" class="rounded-xl bg-white p-12 text-center text-gray-500">Chargement…</div>
    <div v-else class="overflow-hidden rounded-xl bg-white shadow-sm">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
          <tr><th class="px-5 py-3">Nom</th><th v-if="kind === 'domaines'" class="px-5 py-3">Description</th><template v-else><th class="px-5 py-3">Type</th><th class="px-5 py-3">Ville</th></template><th class="px-5 py-3 text-right">Actions</th></tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-gray-50">
            <td class="px-5 py-4 font-semibold text-gray-900">{{ item.nom }}</td>
            <td v-if="kind === 'domaines'" class="px-5 py-4 text-sm text-gray-600">{{ item.description || '—' }}</td>
            <template v-else><td class="px-5 py-4 text-sm text-gray-600">{{ item.type || '—' }}</td><td class="px-5 py-4 text-sm text-gray-600">{{ item.ville?.nom || '—' }}</td></template>
            <td class="px-5 py-4 text-right">
              <button v-if="canWrite" class="mr-3 text-sm font-semibold text-blue-700 hover:text-blue-900" @click="openForm(item)">Modifier</button>
              <button v-if="canDelete" class="text-sm font-semibold text-red-700 hover:text-red-900" @click="remove(item)">Supprimer</button>
            </td>
          </tr>
          <tr v-if="!filteredItems.length"><td :colspan="kind === 'domaines' ? 3 : 4" class="px-5 py-10 text-center text-gray-500">Aucun résultat.</td></tr>
        </tbody>
      </table>
    </div>

    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showForm = false">
      <form class="w-full max-w-lg rounded-2xl bg-white shadow-2xl" @submit.prevent="save">
        <div class="border-b px-6 py-4"><h2 class="text-xl font-bold text-gray-900">{{ selectedId ? `Modifier le ${singular.toLowerCase()}` : `Ajouter un ${singular.toLowerCase()}` }}</h2></div>
        <div class="space-y-4 p-6">
          <label class="block text-sm font-semibold text-gray-700">Nom<input v-model.trim="form.nom" required :maxlength="kind === 'domaines' ? 100 : 150" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2.5"></label>
          <label v-if="kind === 'domaines'" class="block text-sm font-semibold text-gray-700">Description<textarea v-model.trim="form.description" rows="4" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2.5"></textarea></label>
          <template v-else>
            <label class="block text-sm font-semibold text-gray-700">Type<input v-model.trim="form.type" maxlength="50" placeholder="Université, école, institut…" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2.5"></label>
            <label class="block text-sm font-semibold text-gray-700">Ville<select v-model="form.ville_id" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2.5"><option value="">Non renseignée</option><option v-for="ville in villes" :key="ville.id" :value="ville.id">{{ ville.nom }}</option></select></label>
          </template>
          <p v-if="formError" class="rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ formError }}</p>
        </div>
        <div class="flex justify-end gap-3 border-t px-6 py-4"><button type="button" class="rounded-lg border px-4 py-2" @click="showForm = false">Annuler</button><button type="submit" :disabled="saving" class="rounded-lg bg-green-700 px-5 py-2 font-semibold text-white disabled:opacity-50">{{ saving ? 'Enregistrement…' : 'Enregistrer' }}</button></div>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
const props = defineProps<{ kind: 'domaines' | 'etablissements' }>()
const config = useRuntimeConfig()
const authStore = useAuthStore()
const permissions = usePermissions()
const { $swal } = useNuxtApp()
const items = ref<any[]>([])
const villes = ref<any[]>([])
const loading = ref(true)
const saving = ref(false)
const search = ref('')
const showForm = ref(false)
const selectedId = ref<number | null>(null)
const formError = ref('')
const form = reactive({ nom: '', description: '', type: '', ville_id: '' as string | number })

const singular = computed(() => props.kind === 'domaines' ? 'Domaine' : 'Établissement')
const title = computed(() => props.kind === 'domaines' ? 'Domaines académiques' : 'Établissements')
const description = computed(() => props.kind === 'domaines' ? 'Classement des diplômes par secteur de connaissance.' : 'Organismes ayant délivré les diplômes des dignitaires.')
const canWrite = computed(() => permissions.peutEcrire('Diplôme'))
const canDelete = computed(() => permissions.peutEcrire('Diplôme') && permissions.peutSupprimer())
const filteredItems = computed(() => {
  const term = search.value.trim().toLocaleLowerCase('fr')
  if (!term) return items.value
  return items.value.filter(item => [item.nom, item.description, item.type, item.ville?.nom].some(value => value?.toLocaleLowerCase('fr').includes(term)))
})

const headers = () => ({ Authorization: `Bearer ${authStore.token}`, Accept: 'application/json' })

async function load() {
  loading.value = true
  try {
    const calls: Promise<any>[] = [$fetch(`${config.public.apiBase}/${props.kind}`, { headers: headers() })]
    if (props.kind === 'etablissements') calls.push($fetch(`${config.public.apiBase}/villes`, { headers: headers() }))
    const [references, cityList = []] = await Promise.all(calls)
    items.value = references as any[]
    villes.value = cityList as any[]
  } finally {
    loading.value = false
  }
}

function openForm(item?: any) {
  selectedId.value = item?.id ?? null
  form.nom = item?.nom ?? ''
  form.description = item?.description ?? ''
  form.type = item?.type ?? ''
  form.ville_id = item?.ville_id ?? ''
  formError.value = ''
  showForm.value = true
}

async function save() {
  saving.value = true
  formError.value = ''
  try {
    const body = props.kind === 'domaines'
      ? { nom: form.nom, description: form.description || null }
      : { nom: form.nom, type: form.type || null, ville_id: form.ville_id || null }
    await $fetch(`${config.public.apiBase}/${props.kind}${selectedId.value ? `/${selectedId.value}` : ''}`, {
      method: selectedId.value ? 'PUT' : 'POST', headers: headers(), body,
    })
    showForm.value = false
    await load()
  } catch (error: any) {
    formError.value = error?.data?.message || 'Enregistrement impossible.'
  } finally {
    saving.value = false
  }
}

async function remove(item: any) {
  const result = await $swal.fire({ title: `Supprimer « ${item.nom} » ?`, text: 'La suppression sera refusée si ce référentiel est utilisé par un diplôme.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Supprimer', cancelButtonText: 'Annuler' })
  if (!result.isConfirmed) return
  try {
    await $fetch(`${config.public.apiBase}/${props.kind}/${item.id}`, { method: 'DELETE', headers: headers() })
    await load()
  } catch (error: any) {
    await $swal.fire({ icon: 'error', title: 'Suppression impossible', text: error?.data?.message || 'Ce référentiel ne peut pas être supprimé.' })
  }
}

onMounted(load)
</script>
