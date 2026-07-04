const ROLES_ACCES_COMPLET = ['Administrateur', 'Super Administrateur']

export const usePermissions = () => {
  const authStore = useAuthStore()

  const roleName = computed(() => authStore.user?.role_name)

  const aAccesComplet = computed(() => ROLES_ACCES_COMPLET.includes(roleName.value))

  const estSuperAdmin = computed(() => roleName.value === 'Super Administrateur')

  function niveauSousfonction(sousfonctionName: string): string | null {
    const sf = (authStore.user?.sousfonctions || []).find(
      (s: any) => s.sousfonction_name === sousfonctionName
    )
    return sf?.pivot?.niveau || null
  }

  function peutLire(sousfonctionName: string): boolean {
    if (aAccesComplet.value) return true
    return niveauSousfonction(sousfonctionName) !== null
  }

  function peutEcrire(sousfonctionName: string): boolean {
    if (aAccesComplet.value) return true
    return niveauSousfonction(sousfonctionName) === 'ecriture'
  }

  function peutSupprimer(): boolean {
    return aAccesComplet.value
  }

  return {
    roleName,
    aAccesComplet,
    estSuperAdmin,
    peutLire,
    peutEcrire,
    peutSupprimer
  }
}
