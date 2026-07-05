export const useFileDownload = () => {
  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  async function download(path: string, params: Record<string, any> = {}, fallbackName = 'export') {
    const qs = new URLSearchParams(
      Object.entries(params)
        .filter(([, v]) => v !== '' && v !== null && v !== undefined)
        .map(([k, v]) => [k, String(v)])
    ).toString()

    const response = await fetch(`${config.public.apiBase}${path}${qs ? '?' + qs : ''}`, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })

    if (!response.ok) {
      throw new Error(`Échec de l'export (${response.status})`)
    }

    const blob = await response.blob()
    const disposition = response.headers.get('Content-Disposition') || ''
    const match = disposition.match(/filename="?([^"]+)"?/)

    const a = document.createElement('a')
    a.href = URL.createObjectURL(blob)
    a.download = match?.[1] || fallbackName
    document.body.appendChild(a)
    a.click()
    a.remove()
    URL.revokeObjectURL(a.href)
  }

  return { download }
}
