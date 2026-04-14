<script setup lang="ts">
import { useRoute } from 'vue-router'

defineEmits<{ menuClick: [] }>()

const route = useRoute()

const titles: Record<string, string> = {
  dashboard: 'Dashboard',
  projects: 'Projects',
  'project-detail': 'Project',
  'task-detail': 'Task',
}

const pageTitle = () => titles[route.name as string] ?? ''
</script>

<template>
  <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 flex-shrink-0">
    <div class="flex items-center gap-4">
      <button
        class="lg:hidden p-2 text-gray-500 hover:text-gray-900 rounded-lg hover:bg-gray-100"
        @click="$emit('menuClick')"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <h2 class="font-semibold text-gray-900">{{ pageTitle() }}</h2>
    </div>

    <div class="flex items-center gap-2">
      <!-- Notification bell placeholder -->
      <button class="p-2 text-gray-400 hover:text-gray-900 rounded-lg hover:bg-gray-100 relative">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
      </button>
    </div>
  </header>
</template>
