export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const api = {
    async request(endpoint, options = {}) {
      const url = `${apiBase}${endpoint}`
      
      try {
        // Ne pas définir Content-Type si le body est un FormData (le navigateur le fait automatiquement)
        const isFormData = options.body instanceof FormData
        
        const headers = {
          'Accept': 'application/json',
          ...options.headers
        }
        
        // Ajouter Content-Type uniquement si ce n'est pas un FormData
        if (!isFormData && !options.headers?.['Content-Type']) {
          headers['Content-Type'] = 'application/json'
        }
        
        const response = await $fetch(url, {
          ...options,
          headers
        })
        return response
      } catch (error) {
        console.error('API Error:', error)
        throw error
      }
    },

    get(endpoint, options = {}) {
      return this.request(endpoint, { ...options, method: 'GET' })
    },

    post(endpoint, data, options = {}) {
      return this.request(endpoint, { 
        ...options, 
        method: 'POST',
        body: data
      })
    },

    put(endpoint, data, options = {}) {
      return this.request(endpoint, { 
        ...options, 
        method: 'PUT',
        body: data
      })
    },

    delete(endpoint, options = {}) {
      return this.request(endpoint, { ...options, method: 'DELETE' })
    }
  }

  return {
    provide: {
      api
    }
  }
})
