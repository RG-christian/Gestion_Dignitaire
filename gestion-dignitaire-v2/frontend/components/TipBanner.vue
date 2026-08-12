<template>
  <div
    v-if="!dismissed"
    class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4"
  >
    <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center flex-shrink-0">
      <i :class="['fas', icon || 'fa-lightbulb', 'text-sm']"></i>
    </div>
    <div class="flex-1 min-w-0">
      <p class="font-semibold text-blue-900 text-sm">{{ title }}</p>
      <p class="text-blue-800 text-sm mt-0.5 leading-relaxed"><slot /></p>
    </div>
    <button @click="dismiss" class="text-blue-400 hover:text-blue-600 transition flex-shrink-0" aria-label="Fermer">
      <i class="fas fa-times"></i>
    </button>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  id: string
  title: string
  icon?: string
}>()

const onboarding = useOnboarding()
const dismissed = computed(() => onboarding.isTipDismissed(props.id))

function dismiss() {
  onboarding.dismissTip(props.id)
}
</script>
