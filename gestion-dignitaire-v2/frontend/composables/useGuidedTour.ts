export interface TourStep {
  // Sélecteur CSS de l'élément ciblé, ex: '[data-tour="add-button"]'
  target: string
  title: string
  content: string
}

// État singleton module-scope (même pattern que useReferentiels.ts) : un
// seul <GuidedTourOverlay/> monté dans DashboardLayout.vue lit cet état,
// quelle que soit la page qui a démarré la visite.
const steps = ref<TourStep[]>([])
const currentIndex = ref(0)
const active = ref(false)
const tourId = ref<string | null>(null)

export function useGuidedTour() {
  const onboarding = useOnboarding()

  function start(newSteps: TourStep[], id: string, opts: { force?: boolean } = {}) {
    if (!newSteps || newSteps.length === 0) return
    if (!opts.force && onboarding.hasSeenTour(id)) return
    steps.value = newSteps
    tourId.value = id
    currentIndex.value = 0
    active.value = true
  }

  function finish() {
    if (tourId.value) onboarding.markTourSeen(tourId.value)
    active.value = false
    steps.value = []
    currentIndex.value = 0
    tourId.value = null
  }

  function next() {
    if (currentIndex.value < steps.value.length - 1) {
      currentIndex.value++
    } else {
      finish()
    }
  }

  function prev() {
    if (currentIndex.value > 0) currentIndex.value--
  }

  function skip() {
    finish()
  }

  const currentStep = computed(() => steps.value[currentIndex.value] || null)

  return {
    steps,
    currentIndex,
    active,
    currentStep,
    start,
    next,
    prev,
    skip
  }
}
