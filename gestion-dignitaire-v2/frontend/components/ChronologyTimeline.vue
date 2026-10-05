<template>
  <ol class="relative ml-3 border-l-2" :class="borderClass">
    <li v-for="item in items" :key="item.id" class="relative mb-7 ml-6 last:mb-0">
      <span class="absolute -left-[31px] top-1.5 h-3.5 w-3.5 rounded-full border-2 border-white ring-2" :class="dotClass"></span>
      <div class="flex flex-wrap items-center gap-2">
        <time class="text-sm font-semibold" :class="textClass">{{ item.date || 'Date non renseignée' }}</time>
        <span v-if="item.badge" class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold capitalize text-gray-700">{{ item.badge }}</span>
      </div>
      <h3 class="mt-1 font-bold text-gray-900">{{ item.title }}</h3>
      <p v-if="item.description" class="mt-1 text-sm text-gray-600">{{ item.description }}</p>
    </li>
  </ol>
</template>

<script setup lang="ts">
const props = defineProps<{
  items: Array<{ id: string | number; date: string | null; title: string; description?: string | null; badge?: string | null }>
  color: 'green' | 'yellow' | 'blue'
}>()

const borderClass = computed(() => ({ green: 'border-green-200', yellow: 'border-yellow-200', blue: 'border-blue-200' })[props.color])
const dotClass = computed(() => ({ green: 'bg-green-600 ring-green-100', yellow: 'bg-yellow-500 ring-yellow-100', blue: 'bg-blue-700 ring-blue-100' })[props.color])
const textClass = computed(() => ({ green: 'text-green-700', yellow: 'text-yellow-700', blue: 'text-blue-700' })[props.color])
</script>
