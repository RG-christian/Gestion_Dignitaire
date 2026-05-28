<template>
  <div v-if="total > 0" class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm text-gray-700 px-4 pb-4">
    <div class="text-gray-600">
      Affichage de <span class="font-semibold text-gray-900">{{ startIndex + 1 }}</span> à <span class="font-semibold text-gray-900">{{ endIndex }}</span> sur <span class="font-semibold text-gray-900">{{ total }}</span> entrées
    </div>
    <div class="flex flex-wrap gap-1 mt-2 sm:mt-0">
      <!-- Bouton Précédent -->
      <button
        v-if="currentPage > 1"
        @click="$emit('update:currentPage', currentPage - 1)"
        class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 transition"
      >
        Précédent
      </button>
      
      <!-- Première page -->
      <button
        v-if="showFirstPage"
        @click="$emit('update:currentPage', 1)"
        :class="[
          'px-3 py-1 rounded border transition',
          currentPage === 1 ? 'bg-gabon-green-600 text-white border-gabon-green-600' : 'border-gray-300 hover:bg-gray-100'
        ]"
      >
        1
      </button>
      
      <!-- Points de suspension gauche -->
      <span v-if="showLeftDots" class="px-2 py-1 text-gray-500">...</span>
      
      <!-- Pages visibles -->
      <button
        v-for="page in visiblePages"
        :key="page"
        @click="$emit('update:currentPage', page)"
        :class="[
          'px-3 py-1 rounded border transition',
          currentPage === page ? 'bg-gabon-green-600 text-white border-gabon-green-600' : 'border-gray-300 hover:bg-gray-100'
        ]"
      >
        {{ page }}
      </button>
      
      <!-- Points de suspension droite -->
      <span v-if="showRightDots" class="px-2 py-1 text-gray-500">...</span>
      
      <!-- Dernière page -->
      <button
        v-if="showLastPage"
        @click="$emit('update:currentPage', totalPages)"
        :class="[
          'px-3 py-1 rounded border transition',
          currentPage === totalPages ? 'bg-gabon-green-600 text-white border-gabon-green-600' : 'border-gray-300 hover:bg-gray-100'
        ]"
      >
        {{ totalPages }}
      </button>
      
      <!-- Bouton Suivant -->
      <button
        v-if="currentPage < totalPages"
        @click="$emit('update:currentPage', currentPage + 1)"
        class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 transition"
      >
        Suivant
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  totalPages: {
    type: Number,
    required: true
  },
  startIndex: {
    type: Number,
    required: true
  },
  endIndex: {
    type: Number,
    required: true
  },
  total: {
    type: Number,
    required: true
  }
})

defineEmits(['update:currentPage'])

// Nombre de pages à afficher autour de la page actuelle
const maxVisiblePages = 5

// Calculer les pages visibles
const visiblePages = computed(() => {
  const pages = []
  const { currentPage, totalPages } = props
  
  // Si peu de pages, afficher toutes (sans la première ni la dernière qui sont gérées séparément)
  if (totalPages <= maxVisiblePages + 2) {
    for (let i = 2; i < totalPages; i++) {
      pages.push(i)
    }
    return pages
  }
  
  // Calculer la plage de pages à afficher
  let startPage = Math.max(2, currentPage - Math.floor(maxVisiblePages / 2))
  let endPage = Math.min(totalPages - 1, startPage + maxVisiblePages - 1)
  
  // Ajuster si on est proche du début
  if (endPage - startPage < maxVisiblePages - 1) {
    startPage = Math.max(2, endPage - maxVisiblePages + 1)
  }
  
  // Ne pas inclure la première et dernière page (affichées séparément)
  for (let i = startPage; i <= endPage; i++) {
    if (i !== 1 && i !== totalPages) {
      pages.push(i)
    }
  }
  
  return pages
})

// Afficher la première page séparément
const showFirstPage = computed(() => {
  return props.totalPages > 1
})

// Afficher la dernière page séparément
const showLastPage = computed(() => {
  return props.totalPages > 1 && !visiblePages.value.includes(props.totalPages)
})

// Afficher les points de suspension à gauche
const showLeftDots = computed(() => {
  if (props.totalPages <= maxVisiblePages + 2) return false
  return visiblePages.value.length > 0 && visiblePages.value[0] > 2
})

// Afficher les points de suspension à droite
const showRightDots = computed(() => {
  if (props.totalPages <= maxVisiblePages + 2) return false
  return visiblePages.value.length > 0 && visiblePages.value[visiblePages.value.length - 1] < props.totalPages - 1
})
</script>
