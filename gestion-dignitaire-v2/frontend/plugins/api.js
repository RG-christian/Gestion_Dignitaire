export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const api = {
    async request(endpoint, options = {}) {
      const url = `${apiBase}${endpoint}`
      
      try {
        const response = await $fetch(url, {
          ...options,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
          }
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
