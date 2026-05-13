export const usePagination = (items: Ref<any[]>, itemsPerPage = 10) => {
  const currentPage = ref(1)

  const totalPages = computed(() => Math.ceil(items.value.length / itemsPerPage))
  const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
  const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, items.value.length))
  
  const paginatedItems = computed(() => {
    return items.value.slice(startIndex.value, endIndex.value)
  })

  const resetPage = () => {
    currentPage.value = 1
  }

  return {
    currentPage,
    totalPages,
    startIndex,
    endIndex,
    paginatedItems,
    resetPage
  }
}
