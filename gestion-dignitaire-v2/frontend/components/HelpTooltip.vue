<template>
  <span ref="rootEl" class="relative inline-flex">
    <button
      type="button"
      @click.stop="toggle"
      class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition text-[10px] font-bold align-middle"
      :aria-label="label || 'Aide'"
    >
      <i class="fas fa-info text-[9px]"></i>
    </button>
    <div
      v-if="isOpen"
      class="absolute z-50 left-1/2 -translate-x-1/2 top-full mt-2 w-64 bg-gray-800 text-white text-xs rounded-lg shadow-xl p-3 leading-relaxed"
    >
      <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-800 rotate-45"></div>
      <slot />
    </div>
  </span>
</template>

<script setup lang="ts">
defineProps<{
  label?: string
}>()

const isOpen = ref(false)
const rootEl = ref<HTMLElement | null>(null)

function toggle() {
  isOpen.value = !isOpen.value
}

onClickOutside(rootEl, () => {
  isOpen.value = false
})
</script>
