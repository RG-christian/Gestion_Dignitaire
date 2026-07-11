<template>
  <div class="relative">
    <input
      v-model="searchText"
      type="text"
      class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
      placeholder="Rechercher ou saisir un nouvel établissement..."
      @focus="showDropdown = true"
      @input="showDropdown = true"
      @blur="handleBlur"
    >
    <div
      v-if="showDropdown && suggestions.length > 0"
      class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-y-auto"
    >
      <button
        v-for="etab in suggestions"
        :key="etab.id"
        type="button"
        class="w-full text-left px-4 py-2 hover:bg-green-50 text-sm"
        @mousedown.prevent="selectEtablissement(etab)"
      >
        {{ etab.nom }}
      </button>
    </div>
    <p v-if="searchText && !isResolved" class="text-xs text-gabon-green-700 mt-1">
      Un nouvel établissement « {{ searchText }} » sera créé à l'enregistrement.
    </p>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  modelValue: number | null
  initialLabel?: string | null
}>()

const emit = defineEmits<{
  'update:modelValue': [value: number | null]
}>()

const api = useApi()
const referentiels = useReferentiels()

const allEtablissements = ref<any[]>([])
const searchText = ref(props.initialLabel || '')
const resolvedName = ref(props.initialLabel || '')
const showDropdown = ref(false)

const isResolved = computed(() => {
  if (!searchText.value) return true
  return searchText.value.trim().toLowerCase() === resolvedName.value.trim().toLowerCase()
})

const suggestions = computed(() => {
  const q = searchText.value.trim().toLowerCase()
  if (!q) return allEtablissements.value.slice(0, 10)
  return allEtablissements.value
    .filter(e => e.nom.toLowerCase().includes(q))
    .slice(0, 10)
})

function selectEtablissement(etab: any) {
  searchText.value = etab.nom
  resolvedName.value = etab.nom
  showDropdown.value = false
  emit('update:modelValue', etab.id)
}

async function handleBlur() {
  setTimeout(async () => {
    showDropdown.value = false

    const text = searchText.value.trim()
    if (!text) {
      resolvedName.value = ''
      emit('update:modelValue', null)
      return
    }

    if (isResolved.value) return

    try {
      const result: any = await api.createEtablissement(text)
      resolvedName.value = result.nom
      searchText.value = result.nom
      emit('update:modelValue', result.id)
      if (!allEtablissements.value.some(e => e.id === result.id)) {
        allEtablissements.value.push(result)
      }
    } catch (error) {
      console.error('Erreur résolution établissement:', error)
    }
  }, 200)
}

watch(() => props.initialLabel, (label) => {
  if (label && label !== searchText.value) {
    searchText.value = label
    resolvedName.value = label
  }
})

onMounted(async () => {
  allEtablissements.value = await referentiels.getEtablissements()
})
</script>
