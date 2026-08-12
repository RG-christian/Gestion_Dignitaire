// Polling léger réutilisable (admin et candidat) : pas d'infra temps réel
// (WebSocket/broadcasting) dans ce projet, donc on vérifie périodiquement
// que la session est toujours valide, pour détecter une éviction même sur
// un onglet resté inactif (cf. plan session concurrente).
export function useSessionWatcher() {
  let timer: ReturnType<typeof setInterval> | null = null

  function start(intervalMs: number, tick: () => void) {
    stop()
    timer = setInterval(tick, intervalMs)
  }

  function stop() {
    if (timer) clearInterval(timer)
    timer = null
  }

  return { start, stop }
}
