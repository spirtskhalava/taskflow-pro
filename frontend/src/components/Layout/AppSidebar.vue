<script setup lang="ts">
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useAuth } from '@/composables/useAuth'

defineProps<{ open: boolean }>()
defineEmits<{ close: [] }>()

const route = useRoute()
const authStore = useAuthStore()
const { logout } = useAuth()

const navLinks = [
  {
    name: 'Dashboard',
    to: '/dashboard',
    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />`,
  },
  {
    name: 'Projects',
    to: '/projects',
    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />`,
  },
]

function isActive(to: string): boolean {
  return route.path.startsWith(to)
}
</script>

<template>
  <!-- Mobile overlay -->
  <div
    v-if="open"
    class="fixed inset-0 bg-black/40 z-20 lg:hidden"
    @click="$emit('close')"
  ></div>

  <!-- Sidebar -->
  <aside
    class="fixed top-0 left-0 h-full w-64 bg-white border-r border-gray-200 z-30 flex flex-col transition-transform duration-200 lg:static lg:translate-x-0"
    :class="open ? 'translate-x-0' : '-translate-x-full'"
  >
    <!-- Logo -->
    <div class="h-16 flex items-center px-5 border-b border-gray-100 flex-shrink-0">
      <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center mr-3">
        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
      </div>
      <span class="font-bold text-gray-900">TaskFlow Pro</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      <RouterLink
        v-for="link in navLinks"
        :key="link.to"
        :to="link.to"
        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition"
        :class="isActive(link.to)
          ? 'bg-indigo-50 text-indigo-700'
          : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
        @click="$emit('close')"
      >
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-html="link.icon"></svg>
        {{ link.name }}
      </RouterLink>
    </nav>

    <!-- User -->
    <div class="p-3 border-t border-gray-100 flex-shrink-0">
      <div class="flex items-center gap-3 px-2 py-2">
        <img
          v-if="authStore.user"
          :src="authStore.user.avatar_url"
          :alt="authStore.user.name"
          class="w-8 h-8 rounded-full flex-shrink-0"
        />
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 truncate">{{ authStore.user?.name }}</p>
          <p class="text-xs text-gray-400 truncate">{{ authStore.user?.email }}</p>
        </div>
        <button @click="logout" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition" title="Logout">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>
  </aside>
</template>
