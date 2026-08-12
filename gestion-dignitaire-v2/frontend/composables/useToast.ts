// Feedback léger (toast) qui complète les $swal.fire de confirmation déjà
// utilisés partout dans l'app — ne les remplace pas, sert pour les succès
// rapides (enregistrement, suppression) qui n'ont pas besoin de bloquer.
//
// $swal (plugins/sweetalert.client.ts) est un plugin client-only : il est
// `undefined` côté serveur. useToast() est appelé dans le setup() de
// plusieurs pages (donc aussi en SSR) — on ne construit le mixin qu'au
// moment de l'appel réel (success/error/info), jamais à l'initialisation.
export function useToast() {
  function fire(options: Record<string, any>) {
    if (!process.client) return
    const { $swal } = useNuxtApp()
    if (!$swal) return
    ;($swal as any).mixin({
      toast: true,
      position: 'top-end',
      timer: 2500,
      timerProgressBar: true,
      showConfirmButton: false
    }).fire(options)
  }

  function success(message: string) {
    fire({ icon: 'success', title: message })
  }

  function error(message: string) {
    fire({ icon: 'error', title: message })
  }

  function info(message: string) {
    fire({ icon: 'info', title: message })
  }

  return { success, error, info }
}
