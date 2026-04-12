<script setup lang="ts">
import { ref } from 'vue'
import { useAuth } from '@/composables/useAuth'
import BaseButton from '@/components/UI/BaseButton.vue'

const { login, isLoading, errors } = useAuth()

const form = ref({ email: '', password: '' })

async function handleSubmit() {
  await login(form.value.email, form.value.password)
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-2xl mb-4 shadow-lg">
          <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">TaskFlow Pro</h1>
        <p class="text-gray-500 mt-1">Sign in to your workspace</p>
      </div>

      <div class="bg-white rounded-2xl shadow-xl p-8">
        <form @submit.prevent="handleSubmit" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
            <input
              v-model="form.email"
              type="email"
              required
              autocomplete="email"
              placeholder="you@company.com"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
              :class="{ 'border-red-400 focus:ring-red-400': errors.email }"
            />
            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input
              v-model="form.password"
              type="password"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            />
          </div>

          <BaseButton type="submit" :loading="isLoading" class="w-full">
            Sign in
          </BaseButton>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
          Don't have an account?
          <RouterLink to="/register" class="text-indigo-600 font-medium hover:text-indigo-700">Sign up</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
