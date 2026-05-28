/**
 * Hook de debounce pour optimiser les requêtes AJAX
 * Retarde l'exécution d'une fonction jusqu'à ce que l'utilisateur arrête de taper
 */
export function useDebounce() {
  const debounce = <T extends (...args: any[]) => any>(
    func: T,
    delay: number = 500
  ): ((...args: Parameters<T>) => void) => {
    let timeoutId: ReturnType<typeof setTimeout> | null = null

    return (...args: Parameters<T>) => {
      if (timeoutId) {
        clearTimeout(timeoutId)
      }

      timeoutId = setTimeout(() => {
        func(...args)
      }, delay)
    }
  }

  return { debounce }
}
