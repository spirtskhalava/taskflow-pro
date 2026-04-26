<script setup lang="ts">
import type { Task } from '@/types'
import { useRouter } from 'vue-router'
import { format, isPast } from 'date-fns'

const props = defineProps<{ task: Task }>()
const router = useRouter()

const priorityConfig: Record<string, { label: string; class: string }> = {
  urgent: { label: 'Urgent', class: 'text-red-600 bg-red-50' },
  high: { label: 'High', class: 'text-orange-600 bg-orange-50' },
  medium: { label: 'Medium', class: 'text-yellow-600 bg-yellow-50' },
  low: { label: 'Low', class: 'text-green-600 bg-green-50' },
}

const isOverdue = props.task.due_date
  ? isPast(new Date(props.task.due_date)) && props.task.status !== 'done'
  : false
</script>

<template>
  <div
    class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 cursor-grab active:cursor-grabbing hover:shadow-md transition group"
    @click="router.push(`/tasks/${task.id}`)"
  >
    <p class="text-sm font-medium text-gray-800 leading-snug group-hover:text-indigo-600 transition">
      {{ task.title }}
    </p>

    <div class="flex items-center gap-1.5 mt-2 flex-wrap">
      <span
        :class="[priorityConfig[task.priority].class, 'text-xs px-1.5 py-0.5 rounded font-medium']"
      >
        {{ priorityConfig[task.priority].label }}
      </span>

      <span
        v-if="task.due_date"
        :class="[isOverdue ? 'text-red-500' : 'text-gray-400', 'text-xs flex items-center gap-0.5']"
      >
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        {{ format(new Date(task.due_date), 'MMM d') }}
      </span>
    </div>

    <div class="flex items-center justify-between mt-3">
      <div v-if="task.comments_count" class="flex items-center gap-1 text-xs text-gray-400">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        {{ task.comments_count }}
      </div>
      <div v-else class="flex-1"></div>

      <img
        v-if="task.assignee"
        :src="task.assignee.avatar_url"
        :alt="task.assignee.name"
        :title="task.assignee.name"
        class="w-6 h-6 rounded-full"
      />
    </div>
  </div>
</template>
