import type { TourStep } from './useGuidedTour'

export interface HelpSection {
  heading: string
  body: string
}

export interface HelpContent {
  title: string
  intro?: string
  sections: HelpSection[]
  tourId?: string
  tourSteps?: TourStep[]
}

// État singleton module-scope, lu par le <HelpPanel/> unique monté dans
// DashboardLayout.vue. Chaque page appelle setContent() dans son onMounted.
const isOpen = ref(false)
const content = ref<HelpContent | null>(null)

export function useHelpPanel() {
  function setContent(newContent: HelpContent) {
    content.value = newContent
  }

  function open() {
    isOpen.value = true
  }

  function close() {
    isOpen.value = false
  }

  function toggle() {
    isOpen.value = !isOpen.value
  }

  return { isOpen, content, setContent, open, close, toggle }
}
