<template>
  <Teleport to="body">
    <Transition name="help-fade">
      <div v-if="isOpen" class="fixed inset-0 bg-black/40 z-[60]" @click="close"></div>
    </Transition>
    <Transition name="help-slide">
      <aside
        v-if="isOpen"
        class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white shadow-2xl z-[70] flex flex-col"
      >
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-gabon-blue-600 to-gabon-green-600 flex-shrink-0">
          <h2 class="text-white font-bold text-lg flex items-center gap-2">
            <i class="fas fa-circle-question"></i>
            {{ content?.title || 'Aide' }}
          </h2>
          <button @click="close" class="text-white/80 hover:text-white transition">
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-5">
          <p v-if="content?.intro" class="text-gray-600 text-sm leading-relaxed">{{ content.intro }}</p>

          <div v-for="(section, i) in content?.sections || []" :key="i">
            <h3 class="font-semibold text-gray-800 text-sm mb-1">{{ section.heading }}</h3>
            <p class="text-gray-600 text-sm leading-relaxed">{{ section.body }}</p>
          </div>

          <p v-if="!content || content.sections.length === 0" class="text-gray-400 text-sm text-center py-8">
            Aucune aide disponible pour cette page.
          </p>
        </div>

        <div v-if="content?.tourId && content?.tourSteps?.length" class="p-5 border-t border-gray-200 flex-shrink-0">
          <button
            @click="replayTour"
            class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition flex items-center justify-center gap-2"
          >
            <i class="fas fa-route"></i>
            Revoir la visite guidée
          </button>
        </div>
      </aside>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
const { isOpen, content, close } = useHelpPanel()
const tour = useGuidedTour()

function replayTour() {
  if (content.value?.tourSteps && content.value?.tourId) {
    const steps = content.value.tourSteps
    const id = content.value.tourId
    close()
    tour.start(steps, id, { force: true })
  }
}
</script>

<style scoped>
.help-fade-enter-active,
.help-fade-leave-active {
  transition: opacity 0.2s ease;
}
.help-fade-enter-from,
.help-fade-leave-to {
  opacity: 0;
}
.help-slide-enter-active,
.help-slide-leave-active {
  transition: transform 0.25s ease;
}
.help-slide-enter-from,
.help-slide-leave-to {
  transform: translateX(100%);
}
</style>
