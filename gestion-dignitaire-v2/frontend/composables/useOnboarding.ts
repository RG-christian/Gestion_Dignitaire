import { useStorage } from '@vueuse/core'

// État de progression pédagogique (bannières fermées, visites vues, étapes
// cochées), scindé par utilisateur puisque plusieurs comptes peuvent se
// connecter sur le même poste. Stocké dans une seule clé localStorage
// (comme useReferentiels.ts, état module-scope) pour éviter les soucis de
// clé réactive avec useStorage.
interface OnboardingState {
  dismissedTips: string[]
  seenTours: string[]
  doneSteps: string[]
}

function emptyState(): OnboardingState {
  return { dismissedTips: [], seenTours: [], doneSteps: [] }
}

const store = useStorage<Record<string, OnboardingState>>('onboarding-state', {})

export function useOnboarding() {
  const authStore = useAuthStore()
  const userId = computed(() => String(authStore.user?.id || 'anonyme'))

  function currentState(): OnboardingState {
    if (!store.value[userId.value]) {
      store.value[userId.value] = emptyState()
    }
    return store.value[userId.value]
  }

  function isTipDismissed(key: string): boolean {
    return currentState().dismissedTips.includes(key)
  }

  function dismissTip(key: string) {
    const s = currentState()
    if (!s.dismissedTips.includes(key)) s.dismissedTips.push(key)
  }

  function hasSeenTour(tourId: string): boolean {
    return currentState().seenTours.includes(tourId)
  }

  function markTourSeen(tourId: string) {
    const s = currentState()
    if (!s.seenTours.includes(tourId)) s.seenTours.push(tourId)
  }

  function isStepDone(stepId: string): boolean {
    return currentState().doneSteps.includes(stepId)
  }

  function markStepDone(stepId: string) {
    const s = currentState()
    if (!s.doneSteps.includes(stepId)) s.doneSteps.push(stepId)
  }

  return {
    isTipDismissed,
    dismissTip,
    hasSeenTour,
    markTourSeen,
    isStepDone,
    markStepDone
  }
}
