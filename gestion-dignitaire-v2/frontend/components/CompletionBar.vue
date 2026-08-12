<template>
  <div>
    <div class="flex items-center justify-between mb-1.5">
      <span class="text-sm font-medium text-gray-600">{{ label }}</span>
      <span class="text-sm font-bold" :class="colorClass">{{ percent }}%</span>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
      <div class="h-full rounded-full transition-all duration-500" :class="barColorClass" :style="{ width: percent + '%' }"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  percent: number
  label: string
}>()

const colorClass = computed(() => {
  if (props.percent >= 80) return 'text-green-600'
  if (props.percent >= 50) return 'text-yellow-600'
  return 'text-red-500'
})

const barColorClass = computed(() => {
  if (props.percent >= 80) return 'bg-green-500'
  if (props.percent >= 50) return 'bg-yellow-500'
  return 'bg-red-400'
})
</script>
