<script setup lang="ts">
import { onMounted } from 'vue'
import { useProjects } from '@/composables/useProjects'
import { useTasks } from '@/composables/useTasks'
import KanbanBoard from '@/components/Tasks/KanbanBoard.vue'
import BaseButton from '@/components/UI/BaseButton.vue'

const props = defineProps<{ id: number }>()

const { currentProject, fetchProject } = useProjects()
const { tasks, isLoading, fetchTasks } = useTasks(props.id)

onMounted(async () => {
  await Promise.all([
    fetchProject(props.id),
    fetchTasks(props.id),
  ])
})
</script>

<template>
  <div class="flex flex-col h-full">
    <div v-if="currentProject" class="px-6 py-4 border-b border-gray-200 bg-white flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-4 h-4 rounded-full" :style="{ backgroundColor: currentProject.color }"></div>
        <div>
          <h1 class="text-lg font-bold text-gray-900">{{ currentProject.name }}</h1>
          <p class="text-xs text-gray-400">
            {{ currentProject.members.length }} members ·
            {{ currentProject.completion_percentage }}% complete
          </p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <div class="flex -space-x-2">
          <img
            v-for="member in currentProject.members.slice(0, 5)"
            :key="member.id"
            :src="member.avatar_url"
            :alt="member.name"
            :title="member.name"
            class="w-8 h-8 rounded-full border-2 border-white"
          />
        </div>
        <BaseButton size="sm">
          <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Task
        </BaseButton>
      </div>
    </div>

    <div class="flex-1 overflow-auto">
      <KanbanBoard v-if="!isLoading" :project-id="id" />
      <div v-else class="flex items-center justify-center h-64">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      </div>
    </div>
  </div>
</template>
