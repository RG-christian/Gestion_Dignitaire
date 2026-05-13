<template>
  <div v-if="total > 0" class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm text-gray-700 px-4 pb-4">
    <div>
      Affichage de {{ startIndex + 1 }} à {{ endIndex }} sur {{ total }} entrées
    </div>
    <div class="flex flex-wrap gap-1 mt-2 sm:mt-0">
      <button
        v-if="currentPage > 1"
        @click="$emit('update:currentPage', currentPage - 1)"
        class="px-3 py-1 rounded border hover:bg-gray-100"
      >
        Précédent
      </button>
      <button
        v-for="page in totalPages"
        :key="page"
        @click="$emit('update:currentPage', page)"
        :class="[
          'px-3 py-1 rounded border',
          currentPage === page ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'
        ]"
      >
        {{ page }}
      </button>
      <button
        v-if="currentPage < totalPages"
        @click="$emit('update:currentPage', currentPage + 1)"
        class="px-3 py-1 rounded border hover:bg-gray-100"
      >
        Suivant
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
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
</script>
