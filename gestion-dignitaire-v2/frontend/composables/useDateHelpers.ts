export const useDateHelpers = () => {
  /**
   * Date minimale utilisable pour un champ "date de fin" : le lendemain de
   * la date de début, pour empêcher nativement (via l'attribut `min` du
   * calendrier) de choisir une date de fin antérieure ou identique à la
   * date de début.
   */
  function minDateFin(dateDebut?: string | null): string | undefined {
    if (!dateDebut) return undefined
    const d = new Date(dateDebut)
    if (Number.isNaN(d.getTime())) return undefined
    d.setDate(d.getDate() + 1)
    return d.toISOString().slice(0, 10)
  }

  return { minDateFin }
}
