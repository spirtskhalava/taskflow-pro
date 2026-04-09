import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { ref } from 'vue'

export function useAuth() {
  const authStore = useAuthStore()
  const router = useRouter()
  const errors = ref<Record<string, string[]>>({})
  const isLoading = ref(false)

  async function login(email: string, password: string): Promise<void> {
    isLoading.value = true
    errors.value = {}
    try {
      await authStore.login(email, password)
      const redirect = router.currentRoute.value.query.redirect as string
      await router.push(redirect ?? '/dashboard')
    } catch (error: any) {
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors ?? {}
      }
    } finally {
      isLoading.value = false
    }
  }

  async function register(name: string, email: string, password: string, passwordConfirmation: string): Promise<void> {
    isLoading.value = true
    errors.value = {}
    try {
      await authStore.register(name, email, password, passwordConfirmation)
      await router.push('/dashboard')
    } catch (error: any) {
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors ?? {}
      }
    } finally {
      isLoading.value = false
    }
  }

  async function logout(): Promise<void> {
    await authStore.logout()
    await router.push('/login')
  }

  return {
    user: authStore.user,
    isAuthenticated: authStore.isAuthenticated,
    isLoading,
    errors,
    login,
    register,
    logout,
  }
}
