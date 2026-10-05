<template>
  <DashboardLayout>
    <div style="zoom: 0.8">
      <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 p-6 mb-6 shadow-lg">
        <h1 class="text-3xl font-bold text-white">Gestion des procès-verbaux</h1>
        <p class="mt-1 text-sm text-white/90">Créer, consulter et archiver les PV associés aux nominations</p>
      </header>

      <section class="px-4 pb-10">
        <TipBanner id="pvs-intro" title="Traçabilité des nominations" icon="fa-file-circle-check">
          Un procès-verbal lié à une nomination ne peut pas être supprimé. Il doit être archivé afin de préserver l'historique.
        </TipBanner>

        <div class="mb-6 flex flex-col gap-4 rounded-xl bg-white p-4 shadow-lg md:flex-row md:items-center">
          <SearchInput v-model="filters.search" class="flex-1" placeholder="Rechercher par numéro ou description..." @update:modelValue="debouncedLoad" />
          <select v-model="filters.statut" class="rounded-lg border border-gray-300 px-4 py-3" @change="loadPvs">
            <option value="">Tous les statuts</option>
            <option value="actif">Actifs</option>
            <option value="archive">Archivés</option>
          </select>
          <button v-if="permissions.peutEcrire('Procès-verbal')" class="rounded-lg bg-gabon-green-700 px-5 py-3 font-semibold text-white hover:bg-gabon-green-800" @click="openForm()">
            Ajouter un PV
          </button>
        </div>

        <div v-if="loading" class="py-20 text-center text-gray-500">Chargement des procès-verbaux...</div>
        <div v-else class="overflow-hidden rounded-xl bg-white shadow-lg">
          <div v-if="!pvs.length" class="p-12 text-center text-gray-500">Aucun procès-verbal trouvé.</div>
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase text-gray-700">Numéro</th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase text-gray-700">Date</th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase text-gray-700">Description</th>
                  <th class="px-6 py-4 text-center text-xs font-bold uppercase text-gray-700">Nominations</th>
                  <th class="px-6 py-4 text-center text-xs font-bold uppercase text-gray-700">Statut</th>
                  <th class="px-6 py-4 text-center text-xs font-bold uppercase text-gray-700">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="pv in pvs" :key="pv.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 font-semibold text-gray-900">{{ pv.numero }}</td>
                  <td class="px-6 py-4 text-gray-700">{{ formatDate(pv.date) }}</td>
                  <td class="max-w-md px-6 py-4 text-gray-700">{{ pv.description || 'Aucune description' }}</td>
                  <td class="px-6 py-4 text-center">{{ pv.nominations_count }}</td>
                  <td class="px-6 py-4 text-center">
                    <span :class="pv.statut === 'archive' ? 'bg-gray-200 text-gray-700' : 'bg-green-100 text-green-700'" class="rounded-full px-3 py-1 text-xs font-semibold">
                      {{ pv.statut === 'archive' ? 'Archivé' : 'Actif' }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex justify-center gap-2">
                      <button class="rounded bg-blue-100 px-3 py-2 text-blue-700" @click="showDetails(pv.id)">Consulter</button>
                      <button v-if="permissions.peutEcrire('Procès-verbal')" class="rounded bg-yellow-100 px-3 py-2 text-yellow-800" @click="openForm(pv)">Modifier</button>
                      <button v-if="permissions.peutEcrire('Procès-verbal') && pv.statut !== 'archive'" class="rounded bg-gray-200 px-3 py-2 text-gray-700" @click="changeArchive(pv, true)">Archiver</button>
                      <button v-if="permissions.peutEcrire('Procès-verbal') && pv.statut === 'archive'" class="rounded bg-green-100 px-3 py-2 text-green-700" @click="changeArchive(pv, false)">Restaurer</button>
                      <button v-if="permissions.peutSupprimer() && !pv.nominations_count" class="rounded bg-red-100 px-3 py-2 text-red-700" @click="deletePv(pv)">Supprimer</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-2xl rounded-xl bg-white shadow-2xl">
          <div class="flex items-center justify-between rounded-t-xl bg-gabon-green-700 px-6 py-4 text-white">
            <h2 class="text-xl font-bold">{{ selected ? 'Modifier le procès-verbal' : 'Ajouter un procès-verbal' }}</h2>
            <button class="text-2xl" @click="closeForm">×</button>
          </div>
          <form class="space-y-4 p-6" @submit.prevent="savePv">
            <div>
              <label class="mb-2 block text-sm font-semibold">Numéro *</label>
              <input v-model="form.numero" required maxlength="50" class="w-full rounded-lg border px-4 py-3" placeholder="Ex : PV-2026-001">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold">Date *</label>
              <input v-model="form.date" required type="date" class="w-full rounded-lg border px-4 py-3">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold">Description</label>
              <textarea v-model="form.description" rows="4" maxlength="2000" class="w-full rounded-lg border px-4 py-3" />
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold">Document PDF</label>
              <input type="file" accept="application/pdf,.pdf" class="w-full rounded-lg border px-4 py-3" @change="selectFile">
              <p class="mt-1 text-xs text-gray-500">PDF uniquement, 10 Mo maximum.</p>
            </div>
            <div class="flex justify-end gap-3 border-t pt-4">
              <button type="button" class="rounded-lg border px-5 py-2" @click="closeForm">Annuler</button>
              <button :disabled="saving" class="rounded-lg bg-gabon-green-700 px-5 py-2 font-semibold text-white disabled:opacity-50">
                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <div v-if="detail" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-xl bg-white shadow-2xl">
          <div class="flex items-center justify-between rounded-t-xl bg-gabon-blue-700 px-6 py-4 text-white">
            <h2 class="text-xl font-bold">Procès-verbal {{ detail.numero }}</h2>
            <button class="text-2xl" @click="detail = null">×</button>
          </div>
          <div class="space-y-5 p-6">
            <div class="grid gap-4 md:grid-cols-2">
              <div><span class="font-semibold">Date :</span> {{ formatDate(detail.date) }}</div>
              <div><span class="font-semibold">Statut :</span> {{ detail.statut === 'archive' ? 'Archivé' : 'Actif' }}</div>
            </div>
            <p>{{ detail.description || 'Aucune description.' }}</p>
            <button v-if="detail.fichier_path" class="rounded-lg bg-blue-700 px-4 py-2 text-white" @click="downloadPv(detail)">Télécharger le PDF</button>
            <div>
              <h3 class="mb-2 font-bold">Nominations liées ({{ detail.nominations_count }})</h3>
              <div v-if="!detail.nominations?.length" class="text-gray-500">Aucune nomination liée.</div>
              <ul v-else class="divide-y rounded-lg border">
                <li v-for="nomination in detail.nominations" :key="nomination.id" class="p-3">
                  {{ nomination.dignitaire?.prenom }} {{ nomination.dignitaire?.nom }} — {{ nomination.fonction || nomination.poste?.intitule || 'Fonction non précisée' }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'

const config = useRuntimeConfig()
const authStore = useAuthStore()
const permissions = usePermissions()
const fileDownload = useFileDownload()
const { debounce } = useDebounce()
const { $swal } = useNuxtApp()

const pvs = ref<any[]>([])
const loading = ref(true)
const saving = ref(false)
const showForm = ref(false)
const selected = ref<any | null>(null)
const detail = ref<any | null>(null)
const selectedFile = ref<File | null>(null)
const filters = reactive({ search: '', statut: '' })
const form = reactive({ numero: '', date: '', description: '' })

const headers = () => ({ Authorization: `Bearer ${authStore.token}` })

async function loadPvs() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.set('search', filters.search)
    if (filters.statut) params.set('statut', filters.statut)
    pvs.value = await $fetch(`${config.public.apiBase}/pvs?${params}`, { headers: headers() }) as any[]
  } finally {
    loading.value = false
  }
}

const debouncedLoad = debounce(loadPvs, 400)

function openForm(pv: any | null = null) {
  selected.value = pv
  form.numero = pv?.numero || ''
  form.date = pv?.date?.slice(0, 10) || ''
  form.description = pv?.description || ''
  selectedFile.value = null
  showForm.value = true
}

function closeForm() {
  showForm.value = false
  selected.value = null
  selectedFile.value = null
}

function selectFile(event: Event) {
  selectedFile.value = (event.target as HTMLInputElement).files?.[0] || null
}

async function savePv() {
  saving.value = true
  try {
    const body = new FormData()
    body.append('numero', form.numero)
    body.append('date', form.date)
    if (form.description) body.append('description', form.description)
    if (selectedFile.value) body.append('fichier', selectedFile.value)
    if (selected.value) body.append('_method', 'PUT')

    await $fetch(`${config.public.apiBase}/pvs${selected.value ? `/${selected.value.id}` : ''}`, {
      method: 'POST', body, headers: headers()
    })
    closeForm()
    await loadPvs()
    await $swal.fire({ icon: 'success', title: 'Procès-verbal enregistré', timer: 1500, showConfirmButton: false })
  } catch (error: any) {
    await $swal.fire({ icon: 'error', title: 'Enregistrement impossible', text: error?.data?.message || 'Vérifiez les informations saisies.' })
  } finally {
    saving.value = false
  }
}

async function showDetails(id: number) {
  detail.value = await $fetch(`${config.public.apiBase}/pvs/${id}`, { headers: headers() })
}

async function changeArchive(pv: any, archive: boolean) {
  await $fetch(`${config.public.apiBase}/pvs/${pv.id}/${archive ? 'archiver' : 'restaurer'}`, { method: 'POST', headers: headers() })
  await loadPvs()
}

async function deletePv(pv: any) {
  const result = await $swal.fire({ icon: 'warning', title: `Supprimer ${pv.numero} ?`, text: 'Cette opération est réservée aux PV sans nomination.', showCancelButton: true, confirmButtonText: 'Supprimer', cancelButtonText: 'Annuler' })
  if (!result.isConfirmed) return
  await $fetch(`${config.public.apiBase}/pvs/${pv.id}`, { method: 'DELETE', headers: headers() })
  await loadPvs()
}

async function downloadPv(pv: any) {
  await fileDownload.download(`/pvs/${pv.id}/download`, {}, `PV-${pv.numero}.pdf`)
}

function formatDate(value: string | null) {
  if (!value) return 'N/A'
  return new Date(`${value.slice(0, 10)}T00:00:00`).toLocaleDateString('fr-FR')
}

onMounted(loadPvs)
</script>
