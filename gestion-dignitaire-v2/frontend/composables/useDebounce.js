/**
 * Composable pour debounce (attendre avant d'exécuter)
 * Utile pour les recherches en temps réel
 */
export const useDebounce = () => {
  let timeout = null

  const debounce = (func, delay = 500) => {
    return (...args) => {
      if (timeout) clearTimeout(timeout)
      timeout = setTimeout(() => {
        func(...args)
      }, delay)
    }
  }

  return { debounce }
}
