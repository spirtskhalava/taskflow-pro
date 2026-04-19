<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/services/api'
import type { DashboardStats, Project, Task } from '@/types'
import { useAuthStore } from '@/stores/auth'
import { format } from 'date-fns'

const authStore = useAuthStore()

const stats = ref<DashboardStats | null>(null)
const recentProjects = ref<Project[]>([])
const myTasks = ref<Task[]>([])
const isLoading = ref(true)

const priorityColors: Record<string, string> = {
  urgent: 'bg-red-100 text-red-700',
  high: 'bg-orange-100 text-orange-700',
  medium: 'bg-yellow-100 text-yellow-700',
  low: 'bg-green-100 text-green-700',
}

const statusColors: Record<string, string> = {
  todo: 'bg-gray-100 text-gray-600',
  in_progress: 'bg-blue-100 text-blue-700',
  in_review: 'bg-purple-100 text-purple-700',
  done: 'bg-green-100 text-green-700',
}

onMounted(async () => {
  try {
    const { data } = await api.get('/dashboard')
    stats.value = data.stats
    recentProjects.value = data.recent_projects
    myTasks.value = data.my_tasks
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">
        Good morning, {{ authStore.user?.name?.split(' ')[0] }} 👋
      </h1>
      <p class="text-gray-500 mt-1">Here's what's happening across your projects.</p>
    </div>

    <!-- Stats -->
    <div v-if="stats" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Total Tasks</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.total_tasks }}</p>
      </div>
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">My Tasks</p>
        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ stats.my_tasks }}</p>
      </div>
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Overdue</p>
        <p class="text-3xl font-bold text-red-500 mt-1">{{ stats.overdue }}</p>
      </div>
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Completed</p>
        <p class="text-3xl font-bold text-green-500 mt-1">{{ stats.completed }}</p>
      </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
      <!-- Recent Projects -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
          <h2 class="font-semibold text-gray-900">Recent Projects</h2>
          <RouterLink to="/projects" class="text-sm text-indigo-600 hover:text-indigo-700">View all →</RouterLink>
        </div>
        <div class="space-y-3">
          <RouterLink
            v-for="project in recentProjects"
            :key="project.id"
            :to="`/projects/${project.id}`"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition group"
          >
            <div class="w-3 h-3 rounded-full flex-shrink-0" :style="{ backgroundColor: project.color }"></div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-900 truncate group-hover:text-indigo-600 transition">{{ project.name }}</p>
              <p class="text-xs text-gray-400 mt-0.5">
                {{ project.tasks_count }} tasks · {{ project.completion_percentage }}% done
              </p>
            </div>
            <div class="w-16 bg-gray-100 rounded-full h-1.5 flex-shrink-0">
              <div
                class="bg-indigo-500 h-1.5 rounded-full"
                :style="{ width: `${project.completion_percentage}%` }"
              ></div>
            </div>
          </RouterLink>
          <div v-if="!recentProjects.length" class="text-center py-8 text-gray-400 text-sm">
            No projects yet. <RouterLink to="/projects" class="text-indigo-600">Create one →</RouterLink>
          </div>
        </div>
      </div>

      <!-- My Tasks -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-semibold text-gray-900 mb-5">My Active Tasks</h2>
        <div class="space-y-2">
          <div
            v-for="task in myTasks"
            :key="task.id"
            class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition cursor-pointer"
            @click="$router.push(`/tasks/${task.id}`)"
          >
            <div class="mt-0.5">
              <span :class="[priorityColors[task.priority], 'inline-block w-2 h-2 rounded-full mt-1.5']"></span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ task.title }}</p>
              <p class="text-xs text-gray-400 mt-0.5">
                {{ task.project?.name }}
                <span v-if="task.due_date"> · Due {{ format(new Date(task.due_date), 'MMM d') }}</span>
              </p>
            </div>
            <span :class="[statusColors[task.status], 'text-xs px-2 py-0.5 rounded-full font-medium capitalize']">
              {{ task.status.replace('_', ' ') }}
            </span>
          </div>
          <div v-if="!myTasks.length" class="text-center py-8 text-gray-400 text-sm">
            You have no active tasks. 🎉
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
