import { ref } from 'vue'
import { defineStore } from 'pinia'
import api from '../lib/api'

export const useClipStore = defineStore('clips', () => {
  const clips = ref([])
  const loading = ref(false)
  const saving = ref(false)
  const deletingId = ref(null)
  const error = ref('')

  async function fetchClips() {
    loading.value = true
    error.value = ''

    try {
      const { data } = await api.get('/clips')
      clips.value = data.data
    } catch (requestError) {
      error.value = extractErrorMessage(requestError, 'Failed to fetch clips.')
    } finally {
      loading.value = false
    }
  }

  async function createClip(payload) {
    saving.value = true
    error.value = ''

    try {
      const { data } = await api.post('/clips', payload)
      clips.value = [data.data, ...clips.value]
      return true
    } catch (requestError) {
      error.value = extractErrorMessage(requestError, 'Failed to create clip.')
      return false
    } finally {
      saving.value = false
    }
  }

  async function updateClip(id, payload) {
    saving.value = true
    error.value = ''

    try {
      const { data } = await api.put(`/clips/${id}`, payload)
      clips.value = clips.value.map((clip) => (clip.id === id ? data.data : clip))
      return true
    } catch (requestError) {
      error.value = extractErrorMessage(requestError, 'Failed to update clip.')
      return false
    } finally {
      saving.value = false
    }
  }

  async function deleteClip(id) {
    deletingId.value = id
    error.value = ''

    try {
      await api.delete(`/clips/${id}`)
      clips.value = clips.value.filter((clip) => clip.id !== id)
      return true
    } catch (requestError) {
      error.value = extractErrorMessage(requestError, 'Failed to delete clip.')
      return false
    } finally {
      deletingId.value = null
    }
  }

  function reset() {
    clips.value = []
    loading.value = false
    saving.value = false
    deletingId.value = null
    error.value = ''
  }

  function extractErrorMessage(requestError, fallback) {
    const responseData = requestError.response?.data
    const firstValidationError = Object.values(responseData?.errors ?? {})[0]?.[0]

    if (typeof firstValidationError === 'string') {
      return firstValidationError
    }

    return responseData?.message ?? fallback
  }

  return {
    clips,
    loading,
    saving,
    deletingId,
    error,
    fetchClips,
    createClip,
    updateClip,
    deleteClip,
    reset,
  }
})
