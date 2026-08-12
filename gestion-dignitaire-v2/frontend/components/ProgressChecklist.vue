<template>
  <div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-gray-800">{{ title }}</h3>
      <span class="text-xs font-semibold text-gray-400">{{ doneCount }}/{{ items.length }}</span>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-2 mb-5 overflow-hidden">
      <div class="h-full bg-gradient-to-r from-gabon-green-500 to-gabon-green-600 transition-all duration-500" :style="{ width: percent + '%' }"></div>
    </div>
    <ul class="space-y-3">
      <li v-for="item in items" :key="item.id" class="flex items-start gap-3">
        <span
          class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 text-[10px]"
          :class="item.done ? 'bg-gabon-green-600 text-white' : 'bg-gray-200 text-gray-400'"
        >
          <i v-if="item.done" class="fas fa-check"></i>
        </span>
        <div class="min-w-0">
          <NuxtLink
            v-if="item.to && !item.done"
            :to="item.to"
            class="text-sm font-medium text-gray-700 hover:text-blue-600 transition"
          >
            {{ item.label }}
          </NuxtLink>
          <span v-else class="text-sm font-medium" :class="item.done ? 'text-gray-400 line-through' : 'text-gray-700'">
            {{ item.label }}
          </span>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  title: string
  items: { id: string; label: string; done: boolean; to?: string }[]
}>()

const doneCount = computed(() => props.items.filter(i => i.done).length)
const percent = computed(() => props.items.length ? Math.round((doneCount.value / props.items.length) * 100) : 0)
</script>
