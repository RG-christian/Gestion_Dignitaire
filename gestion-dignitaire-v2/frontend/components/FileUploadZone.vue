<template>
  <div>
    <label v-if="label" class="text-xs font-semibold text-gray-600 block mb-1">{{ label }}</label>

    <div
      v-if="!modelValue"
      @click="openPicker"
      @dragover.prevent="dragOver = true"
      @dragleave.prevent="dragOver = false"
      @drop.prevent="onDrop"
      class="border-2 border-dashed rounded-xl px-4 py-6 text-center cursor-pointer transition-colors"
      :class="dragOver ? 'border-gabon-green-500 bg-gabon-green-50' : 'border-gray-300 hover:border-gabon-green-400 hover:bg-gray-50'"
    >
      <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
      </svg>
      <p class="text-sm text-gray-600">
        <span class="font-semibold text-gabon-green-700">Cliquez pour choisir un fichier</span> ou glissez-déposez
      </p>
      <p v-if="hint" class="text-xs text-gray-400 mt-1">{{ hint }}</p>
    </div>

    <div v-else class="flex items-center justify-between gap-3 border border-gray-200 bg-gray-50 rounded-xl px-4 py-3">
      <div class="flex items-center gap-3 min-w-0">
        <div class="w-9 h-9 bg-gabon-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-gabon-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
        <div class="min-w-0">
          <p class="text-sm font-semibold text-gray-900 truncate">{{ modelValue.name }}</p>
          <p class="text-xs text-gray-500">{{ formatSize(modelValue.size) }}</p>
        </div>
      </div>
      <button type="button" @click="clear" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors flex-shrink-0" title="Retirer le fichier">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>

    <input
      ref="fileInput"
      type="file"
      :accept="accept"
      class="hidden"
      @change="onFileChange"
    >
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  modelValue: File | null
  accept?: string
  label?: string
  hint?: string
  maxSizeMb?: number
}>()

const emit = defineEmits<{
  'update:modelValue': [value: File | null]
}>()

const fileInput = ref<HTMLInputElement | null>(null)
const dragOver = ref(false)
const error = ref('')

function formatSize(bytes: number): string {
  if (bytes < 1024) return `${bytes} o`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} Ko`
  return `${(bytes / (1024 * 1024)).toFixed(1)} Mo`
}

function openPicker() {
  fileInput.value?.click()
}

function applyFile(file: File | undefined) {
  error.value = ''
  if (!file) return

  const maxBytes = (props.maxSizeMb ?? 10) * 1024 * 1024
  if (file.size > maxBytes) {
    error.value = `Le fichier dépasse ${props.maxSizeMb ?? 10} Mo.`
    return
  }

  emit('update:modelValue', file)
}

function onFileChange(event: Event) {
  applyFile((event.target as HTMLInputElement).files?.[0])
}

function onDrop(event: DragEvent) {
  dragOver.value = false
  applyFile(event.dataTransfer?.files?.[0])
}

function clear() {
  error.value = ''
  emit('update:modelValue', null)
  if (fileInput.value) fileInput.value.value = ''
}
</script>
