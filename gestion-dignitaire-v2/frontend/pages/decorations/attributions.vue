<template>
  <DashboardLayout>
    <header class="bg-gradient-to-r from-gabon-green-600 via-gabon-yellow-500 to-gabon-blue-600 p-6 text-white shadow-lg">
      <h1 class="text-3xl font-bold">Attributions de décorations</h1>
      <p class="mt-1 text-sm">Associer une décoration, un dignitaire, son poste et l'attestation PDF sécurisée.</p>
    </header>
    <section class="p-6">
      <div class="mb-5 flex justify-between gap-3">
        <NuxtLink to="/decorations" class="rounded-lg border px-4 py-2 font-semibold text-gray-700">Retour au référentiel</NuxtLink>
        <button @click="openForm()" class="rounded-lg bg-gabon-green-700 px-5 py-2 font-semibold text-white">Ajouter une attribution</button>
      </div>
      <div class="overflow-x-auto rounded-xl bg-white shadow">
        <table class="min-w-full divide-y">
          <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600"><tr><th class="p-4">Dignitaire</th><th class="p-4">Décoration</th><th class="p-4">Date</th><th class="p-4">Poste</th><th class="p-4">Attestation</th><th class="p-4">Actions</th></tr></thead>
          <tbody class="divide-y">
            <tr v-for="item in attributions" :key="item.id">
              <td class="p-4 font-semibold">{{ item.dignitaire_nom }}</td><td class="p-4">{{ item.decoration_nom }}</td><td class="p-4">{{ formatDate(item.date_attribution) }}</td><td class="p-4">{{ item.poste_intitule || 'Non renseigné' }}</td>
              <td class="p-4"><button v-if="item.attestation_path" @click="download(item)" class="font-semibold text-red-700"><i class="fas fa-file-pdf mr-2"></i>Télécharger</button><span v-else-if="item.attestation_legacy" class="text-amber-700" :title="item.attestation_legacy">Référence historique</span><span v-else class="text-gray-400">Aucune</span></td>
              <td class="p-4"><div class="flex gap-2"><button @click="openForm(item)" class="rounded bg-blue-50 px-3 py-2 font-semibold text-blue-700">Modifier</button><button @click="remove(item)" class="rounded bg-red-50 px-3 py-2 font-semibold text-red-700">Archiver</button></div></td>
            </tr>
            <tr v-if="!loading && !attributions.length"><td colspan="6" class="p-10 text-center text-gray-500">Aucune attribution</td></tr>
          </tbody>
        </table>
      </div>
    </section>
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeForm">
      <form @submit.prevent="save" class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <h2 class="mb-5 text-xl font-bold">{{ selected ? 'Modifier' : 'Ajouter' }} une attribution</h2>
        <div class="space-y-4">
          <div><label class="mb-1 block font-semibold">Dignitaire *</label><select v-model="form.dignitaire_id" required class="w-full rounded-lg border p-3"><option value="">Choisir</option><option v-for="d in options.dignitaires" :key="d.id" :value="d.id">{{ d.prenom }} {{ d.nom }}</option></select></div>
          <div><label class="mb-1 block font-semibold">Décoration *</label><select v-model="form.decoration_id" required class="w-full rounded-lg border p-3"><option value="">Choisir</option><option v-for="d in options.decorations" :key="d.id" :value="d.id">{{ d.nom }}</option></select></div>
          <div><label class="mb-1 block font-semibold">Date d'attribution *</label><input v-model="form.date_attribution" required type="date" class="w-full rounded-lg border p-3"></div>
          <div><label class="mb-1 block font-semibold">Poste occupé</label><select v-model="form.poste_id" class="w-full rounded-lg border p-3"><option value="">Aucun</option><option v-for="p in filteredPostes" :key="p.id" :value="p.id">{{ p.intitule }}</option></select><p class="mt-1 text-xs text-gray-500">Seuls les postes du dignitaire sélectionné sont proposés.</p></div>
          <div><label class="mb-1 block font-semibold">Attestation PDF</label><input type="file" accept="application/pdf,.pdf" @change="pickFile" class="w-full rounded-lg border p-3"><p class="mt-1 text-xs text-gray-500">PDF uniquement, 10 Mo maximum. Le fichier est conservé dans le stockage privé.</p><button v-if="selected?.attestation_path" type="button" @click="download(selected)" class="mt-2 font-semibold text-red-700">Télécharger l'attestation actuelle</button></div>
        </div>
        <div class="mt-6 flex gap-3"><button type="button" @click="closeForm" class="flex-1 rounded-lg bg-gray-100 p-3 font-semibold">Annuler</button><button type="submit" class="flex-1 rounded-lg bg-gabon-green-700 p-3 font-semibold text-white">Enregistrer</button></div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
definePageMeta({ middleware: 'auth' })
const config = useRuntimeConfig()
const authStore = useAuthStore()
const fileDownload = useFileDownload()
const attributions = ref([])
const options = reactive({ dignitaires: [], decorations: [], postes: [] })
const loading = ref(true)
const showForm = ref(false)
const selected = ref(null)
const attestation = ref(null)
const form = reactive({ dignitaire_id: '', decoration_id: '', date_attribution: '', poste_id: '' })
const headers = () => ({ Authorization: `Bearer ${authStore.token}` })
const filteredPostes = computed(() => options.postes.filter(p => String(p.dignitaire_id) === String(form.dignitaire_id)))
function formatDate(value) { return value ? new Date(value).toLocaleDateString('fr-FR') : 'N/A' }
async function load() { loading.value = true; try { const [rows, refs] = await Promise.all([$fetch(`${config.public.apiBase}/decoration-attributions`, { headers: headers() }), $fetch(`${config.public.apiBase}/decoration-attributions/initial-data`, { headers: headers() })]); attributions.value = rows; Object.assign(options, refs) } finally { loading.value = false } }
function openForm(item = null) { selected.value = item; attestation.value = null; Object.assign(form, { dignitaire_id: item?.dignitaire_id || '', decoration_id: item?.decoration_id || '', date_attribution: item?.date_attribution || '', poste_id: item?.poste_id || '' }); showForm.value = true }
function closeForm() { showForm.value = false; selected.value = null; attestation.value = null }
function pickFile(event) { const file = event.target.files?.[0] || null; if (file && (file.type !== 'application/pdf' || file.size > 10 * 1024 * 1024)) { event.target.value = ''; attestation.value = null; useNuxtApp().$swal.fire({ icon: 'error', title: 'Fichier invalide', text: 'Sélectionnez un PDF de 10 Mo maximum.' }); return } attestation.value = file }
async function save() { const body = new FormData(); for (const [key, value] of Object.entries(form)) if (value) body.append(key, String(value)); if (attestation.value) body.append('attestation', attestation.value); if (selected.value) body.append('_method', 'PUT'); await $fetch(`${config.public.apiBase}/decoration-attributions${selected.value ? '/' + selected.value.id : ''}`, { method: 'POST', body, headers: headers() }); closeForm(); await load(); useNuxtApp().$swal.fire({ icon: 'success', title: 'Attribution enregistrée', timer: 1500, showConfirmButton: false }) }
async function download(item) { await fileDownload.download(`/decoration-attributions/${item.id}/attestation`, {}, item.attestation_nom_original || `attestation-${item.id}.pdf`) }
async function remove(item) { const result = await useNuxtApp().$swal.fire({ title: 'Archiver cette attribution ?', text: 'Le document sera conservé et l\'attribution pourra être restaurée.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Archiver', cancelButtonText: 'Annuler' }); if (!result.isConfirmed) return; await $fetch(`${config.public.apiBase}/decoration-attributions/${item.id}`, { method: 'DELETE', headers: headers() }); await load() }
onMounted(load)
</script>
