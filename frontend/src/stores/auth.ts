import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import type { User } from '@/types'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))

  const isAuthenticated = computed(() => !!token.value)

  async function login(email: string, password: string): Promise<void> {
    const { data } = await api.post('/auth/login', { email, password })
    setAuth(data.user, data.token)
  }

  async function register(name: string, email: string, password: string, passwordConfirmation: string): Promise<void> {
    const { data } = await api.post('/auth/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    })
    setAuth(data.user, data.token)
  }

  async function logout(): Promise<void> {
    try {
      await api.post('/auth/logout')
    } finally {
      clearAuth()
    }
  }

  async function fetchMe(): Promise<void> {
    if (!token.value) return
    try {
      const { data } = await api.get('/auth/me')
      user.value = data
    } catch {
      clearAuth()
    }
  }

  async function updateProfile(payload: Partial<User>): Promise<void> {
    const { data } = await api.put('/auth/profile', payload)
    user.value = data
  }

  function setAuth(userData: User, authToken: string): void {
    user.value = userData
    token.value = authToken
    localStorage.setItem('auth_token', authToken)
  }

  function clearAuth(): void {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchMe,
    updateProfile,
  }
})
