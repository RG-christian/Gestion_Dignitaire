<template>
  <div class="bg-white rounded-xl shadow border p-6 hover:shadow-lg transition">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <div class="text-gray-500 text-sm mb-1">{{ title }}</div>
        <div class="text-3xl font-bold text-gray-900">{{ value }}</div>
      </div>
      <div class="ml-4">
        <span :class="`inline-flex bg-${color}-100 p-4 rounded-full`">
          <svg class="w-8 h-8" :class="`text-${color}-600`" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" :d="iconPath" />
          </svg>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  title: string
  value: number | string
  icon: string
  color: string
}>()

const iconPath = computed(() => {
  const icons: Record<string, string> = {
    users: 'M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5m6-6a4 4 0 10-8 0 4 4 0 008 0z',
    briefcase: 'M6 7V6a2 2 0 012-2h8a2 2 0 012 2v1M6 7h12M6 7v12a2 2 0 002 2h8a2 2 0 002-2V7',
    medal: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
    map: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
  }
  return icons[props.icon] || icons.users
})
</script>
