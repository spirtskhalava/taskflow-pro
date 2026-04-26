<script setup lang="ts">
import { computed } from 'vue'
import { useTasks } from '@/composables/useTasks'
import TaskCard from './TaskCard.vue'
import type { TaskStatus } from '@/types'

const props = defineProps<{ projectId: number }>()

const { tasksByStatus, moveTask } = useTasks(props.projectId)

const columns: { key: TaskStatus; label: string; color: string }[] = [
  { key: 'todo', label: 'To Do', color: 'bg-gray-200' },
  { key: 'in_progress', label: 'In Progress', color: 'bg-blue-200' },
  { key: 'in_review', label: 'In Review', color: 'bg-purple-200' },
  { key: 'done', label: 'Done', color: 'bg-green-200' },
]

let draggingTaskId: number | null = null

function onDragStart(taskId: number) {
  draggingTaskId = taskId
}

function onDrop(status: TaskStatus) {
  if (draggingTaskId === null) return
  moveTask(draggingTaskId, status)
  draggingTaskId = null
}

function onDragOver(event: DragEvent) {
  event.preventDefault()
}
</script>

<template>
  <div class="flex gap-4 p-6 h-full overflow-x-auto">
    <div
      v-for="col in columns"
      :key="col.key"
      class="flex-shrink-0 w-72 flex flex-col"
      @drop="onDrop(col.key)"
      @dragover="onDragOver"
    >
      <!-- Column header -->
      <div class="flex items-center gap-2 mb-3">
        <span :class="[col.color, 'w-2.5 h-2.5 rounded-full']"></span>
        <span class="font-semibold text-sm text-gray-700">{{ col.label }}</span>
        <span class="ml-auto text-xs text-gray-400 font-medium bg-gray-100 rounded-full px-2 py-0.5">
          {{ tasksByStatus[col.key].length }}
        </span>
      </div>

      <!-- Tasks -->
      <div class="flex-1 space-y-2 min-h-[8rem] rounded-xl p-2 bg-gray-50/60">
        <TaskCard
          v-for="task in tasksByStatus[col.key]"
          :key="task.id"
          :task="task"
          draggable="true"
          @dragstart="onDragStart(task.id)"
        />

        <div
          v-if="!tasksByStatus[col.key].length"
          class="flex items-center justify-center h-24 text-sm text-gray-300 border-2 border-dashed border-gray-200 rounded-xl"
        >
          Drop tasks here
        </div>
      </div>
    </div>
  </div>
</template>
