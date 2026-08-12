<template>
  <Teleport to="body">
    <div v-if="active && currentStep" class="fixed inset-0 z-[80]">
      <!-- Fond assombri avec découpe (box-shadow) autour de l'élément ciblé -->
      <div class="absolute transition-all duration-200 rounded-lg pointer-events-none" :style="cutoutStyle"></div>
      <!-- Bloque les clics sur le reste de la page tant que la visite est active -->
      <div class="absolute inset-0"></div>

      <div class="absolute bg-white rounded-xl shadow-2xl p-4 w-72 z-[81]" :style="bubbleStyle">
        <p class="text-xs font-semibold text-blue-600 mb-1">
          Étape {{ currentIndex + 1 }} / {{ steps.length }}
        </p>
        <h3 class="font-bold text-gray-900 text-sm mb-1">{{ currentStep.title }}</h3>
        <p class="text-gray-600 text-sm leading-relaxed">{{ currentStep.content }}</p>
        <div class="flex items-center justify-between mt-4">
          <button @click="skip" class="text-xs text-gray-400 hover:text-gray-600 transition">Passer</button>
          <div class="flex items-center gap-2">
            <button
              v-if="currentIndex > 0"
              @click="prev"
              class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition"
            >
              Précédent
            </button>
            <button
              @click="next"
              class="px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition"
            >
              {{ currentIndex === steps.length - 1 ? 'Terminer' : 'Suivant' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
const { active, steps, currentIndex, currentStep, next, prev, skip } = useGuidedTour()

const PAD = 8
const rect = ref<DOMRect | null>(null)

function updateRect() {
  if (!currentStep.value) {
    rect.value = null
    return
  }
  const el = document.querySelector(currentStep.value.target)
  if (el) {
    el.scrollIntoView({ block: 'center', behavior: 'smooth' })
    rect.value = el.getBoundingClientRect()
  } else {
    rect.value = null
  }
}

watch([currentStep, active], async () => {
  await nextTick()
  updateRect()
}, { immediate: true })

function onScrollOrResize() {
  if (active.value) updateRect()
}

function onKeydown(e: KeyboardEvent) {
  if (!active.value) return
  if (e.key === 'Escape') skip()
  if (e.key === 'ArrowRight') next()
  if (e.key === 'ArrowLeft') prev()
}

onMounted(() => {
  window.addEventListener('scroll', onScrollOrResize, true)
  window.addEventListener('resize', onScrollOrResize)
  window.addEventListener('keydown', onKeydown)
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScrollOrResize, true)
  window.removeEventListener('resize', onScrollOrResize)
  window.removeEventListener('keydown', onKeydown)
})

const cutoutStyle = computed(() => {
  if (!rect.value) {
    return {
      top: '-9999px',
      left: '-9999px',
      width: '0px',
      height: '0px',
      boxShadow: '0 0 0 9999px rgba(15, 23, 42, 0.6)'
    }
  }
  const r = rect.value
  return {
    top: `${r.top - PAD}px`,
    left: `${r.left - PAD}px`,
    width: `${r.width + PAD * 2}px`,
    height: `${r.height + PAD * 2}px`,
    boxShadow: '0 0 0 9999px rgba(15, 23, 42, 0.6)'
  }
})

const bubbleStyle = computed(() => {
  if (!rect.value) {
    return { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' }
  }
  const r = rect.value
  const spaceBelow = window.innerHeight - r.bottom
  const top = spaceBelow > 220 ? r.bottom + PAD + 8 : Math.max(r.top - 220, 16)
  let left = r.left
  const maxLeft = window.innerWidth - 300
  if (left > maxLeft) left = Math.max(maxLeft, 16)
  return { top: `${top}px`, left: `${left}px` }
})
</script>
