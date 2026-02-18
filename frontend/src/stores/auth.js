import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import api from '../lib/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('clips_token'))
  const loading = ref(false)
  const error = ref('')

  const isAuthenticated = computed(() => Boolean(token.value))

  function setSession(nextToken, nextUser) {
    token.value = nextToken
    user.value = nextUser
    localStorage.setItem('clips_token', nextToken)
  }

  function clearSession() {
    token.value = null
    user.value = null
    localStorage.removeItem('clips_token')
  }

  async function register(payload) {
    loading.value = true
    error.value = ''

    try {
      const { data } = await api.post('/auth/register', payload)
      setSession(data.token, data.user)
      return true
    } catch (requestError) {
      error.value = extractErrorMessage(requestError, 'Registration failed.')
      return false
    } finally {
      loading.value = false
    }
  }

  async function login(payload) {
    loading.value = true
    error.value = ''

    try {
      const { data } = await api.post('/auth/login', payload)
      setSession(data.token, data.user)
      return true
    } catch (requestError) {
      error.value = extractErrorMessage(requestError, 'Login failed.')
      return false
    } finally {
      loading.value = false
    }
  }

  async function fetchCurrentUser() {
    if (!token.value) {
      return false
    }

    loading.value = true
    error.value = ''

    try {
      const { data } = await api.get('/user')
      user.value = data
      return true
    } catch {
      clearSession()
      return false
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true
    error.value = ''

    try {
      await api.post('/auth/logout')
    } catch {
    } finally {
      clearSession()
      loading.value = false
    }
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
    user,
    token,
    loading,
    error,
    isAuthenticated,
    register,
    login,
    fetchCurrentUser,
    logout,
    clearSession,
  }
})
