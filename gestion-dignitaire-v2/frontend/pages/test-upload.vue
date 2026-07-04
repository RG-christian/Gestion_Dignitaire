<template>
  <div class="p-8">
    <h1 class="text-2xl mb-4">Test Upload Simple</h1>
    
    <div class="mb-4">
      <input type="file" @change="handleFileSelect" accept="image/*" class="border p-2">
    </div>
    
    <button @click="testDirectUpload" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">
      Test Direct (test-upload.php)
    </button>
    
    <button @click="testLaravelUpload" class="bg-green-500 text-white px-4 py-2 rounded">
      Test Laravel API
    </button>
    
    <div v-if="result" class="mt-4 p-4 border rounded">
      <h3 class="font-bold mb-2">Résultat:</h3>
      <pre class="bg-gray-100 p-2 rounded overflow-auto">{{ result }}</pre>
    </div>
  </div>
</template>

<script setup lang="ts">
const fileSelected = ref<File | null>(null)
const result = ref<any>(null)

function handleFileSelect(event: Event) {
  const target = event.target as HTMLInputElement
  fileSelected.value = target.files?.[0] || null
}

async function testDirectUpload() {
  if (!fileSelected.value) {
    alert('Sélectionnez un fichier')
    return
  }
  
  try {
    const formData = new FormData()
    formData.append('photo', fileSelected.value)
    
    const response = await fetch('http://127.0.0.1:8000/test-upload.php', {
      method: 'POST',
      body: formData
    })
    
    result.value = await response.json()
  } catch (error) {
    result.value = { error: error.message }
  }
}

async function testLaravelUpload() {
  if (!fileSelected.value) {
    alert('Sélectionnez un fichier')
    return
  }
  
  try {
    const formData = new FormData()
    formData.append('photo', fileSelected.value)
    
    const authStore = useAuthStore()
    
    const response = await fetch('http://127.0.0.1:8000/api/profile/photo', {
      method: 'POST',
      body: formData,
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })
    
    result.value = {
      status: response.status,
      data: await response.json()
    }
  } catch (error) {
    result.value = { error: error.message }
  }
}
</script>
