<template>
  <div class="space-y-2 w-full">
    <label 
      v-if="label" 
      :for="inputId" 
      class="block text-sm font-medium text-gray-700"
    >
      {{ label }}
    </label>
    <div class="relative">
      <input
        :id="inputId"
        v-model="localValue"
        type="search"
        :placeholder="placeholder"
        :disabled="disabled"
        class="flex h-11 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 pl-10 pr-10 text-sm text-gray-900 shadow-sm transition-all placeholder:text-gray-400 focus:border-gabon-green-500 focus:outline-none focus:ring-3 focus:ring-gabon-green-500/20 disabled:cursor-not-allowed disabled:opacity-50 [&::-webkit-search-cancel-button]:appearance-none [&::-webkit-search-decoration]:appearance-none"
        @input="handleInput"
        @keyup.enter="handleSubmit"
      >
      
      <!-- Icône de recherche à gauche -->
      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center justify-center pl-3 text-gray-400">
        <svg 
          class="w-4 h-4" 
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            stroke-width="2" 
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
      </div>

      <!-- Bouton de soumission à droite (optionnel) -->
      <button
        v-if="showSubmitButton"
        type="button"
        class="absolute inset-y-0 right-0 flex h-full w-10 items-center justify-center rounded-r-lg text-gray-400 transition-colors hover:text-gabon-green-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-gabon-green-500 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50"
        :disabled="disabled"
        @click="handleSubmit"
        :aria-label="submitLabel"
      >
        <svg 
          class="w-4 h-4" 
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            stroke-width="2" 
            d="M14 5l7 7m0 0l-7 7m7-7H3"
          />
        </svg>
      </button>

      <!-- Bouton clear (si la valeur n'est pas vide) -->
      <button
        v-else-if="localValue && showClearButton"
        type="button"
        class="absolute inset-y-0 right-0 flex h-full w-10 items-center justify-center rounded-r-lg text-gray-400 transition-colors hover:text-red-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-gabon-green-500"
        @click="handleClear"
        aria-label="Effacer"
      >
        <svg 
          class="w-4 h-4" 
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            stroke-width="2" 
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Rechercher...'
  },
  label: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  showSubmitButton: {
    type: Boolean,
    default: false
  },
  showClearButton: {
    type: Boolean,
    default: true
  },
  submitLabel: {
    type: String,
    default: 'Rechercher'
  }
})

const emit = defineEmits(['update:modelValue', 'submit', 'clear'])

// Générer un ID unique pour l'input
const inputId = `search-input-${Math.random().toString(36).substring(2, 9)}`

const localValue = ref(props.modelValue)

// Synchroniser avec le v-model parent
watch(() => props.modelValue, (newValue) => {
  localValue.value = newValue
})

watch(localValue, (newValue) => {
  emit('update:modelValue', newValue)
})

function handleInput(event) {
  const target = event.target
  localValue.value = target.value
}

function handleSubmit() {
  emit('submit', localValue.value)
}

function handleClear() {
  localValue.value = ''
  emit('clear')
}
</script>
