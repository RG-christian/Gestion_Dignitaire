<template>
  <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center group relative hover:shadow-xl transition">
    <img 
      :src="`/uploads/photos/${dignitaire.photo || 'default.png'}`" 
      :alt="dignitaire.nom_complet"
      class="w-24 h-24 rounded-full object-cover border-4 border-green-200 shadow mb-3"
      @error="handleImageError"
    />
    <h4 class="text-lg font-semibold text-center mb-1">{{ dignitaire.nom_complet }}</h4>
    <p class="text-sm text-gray-600 mb-2">{{ dignitaire.matricule }}</p>
    <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">
      {{ dignitaire.genre }}
    </span>
    
    <!-- Actions au hover -->
    <div class="absolute top-3 right-3 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition">
      <NuxtLink 
        :to="`/dignitaires/${dignitaire.id}`"
        class="bg-sky-100 hover:bg-sky-200 text-sky-800 p-2 rounded-full shadow"
        title="Voir"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
      </NuxtLink>
      <button 
        @click="$emit('edit', dignitaire)"
        class="bg-blue-100 hover:bg-blue-200 text-blue-800 p-2 rounded-full shadow"
        title="Modifier"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
      </button>
      <button 
        @click="$emit('delete', dignitaire.id)"
        class="bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-full shadow"
        title="Supprimer"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  dignitaire: any
}>()

defineEmits(['edit', 'delete'])

function handleImageError(event: Event) {
  (event.target as HTMLImageElement).src = '/uploads/photos/default.png'
}
</script>
