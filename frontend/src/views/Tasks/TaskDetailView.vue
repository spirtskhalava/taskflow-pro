<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useTasks } from '@/composables/useTasks'
import { useAuthStore } from '@/stores/auth'
import { format } from 'date-fns'
import type { TaskStatus } from '@/types'

const props = defineProps<{ id: number }>()

const { currentTask, fetchTask, addComment, updateStatus } = useTasks()
const authStore = useAuthStore()
const newComment = ref('')

const statusOptions: { value: TaskStatus; label: string }[] = [
  { value: 'todo', label: 'To Do' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'in_review', label: 'In Review' },
  { value: 'done', label: 'Done' },
]

async function handleComment() {
  if (!newComment.value.trim()) return
  await addComment(props.id, newComment.value)
  newComment.value = ''
}

async function handleStatusChange(status: string) {
  await updateStatus(props.id, status as TaskStatus)
}

onMounted(() => fetchTask(props.id))
</script>

<template>
  <div v-if="currentTask" class="max-w-4xl mx-auto p-6">
    <div class="grid lg:grid-cols-3 gap-6">
      <!-- Main content -->
      <div class="lg:col-span-2 space-y-5">
        <div>
          <h1 class="text-xl font-bold text-gray-900">{{ currentTask.title }}</h1>
          <p v-if="currentTask.description" class="text-gray-600 mt-3 leading-relaxed whitespace-pre-wrap">
            {{ currentTask.description }}
          </p>
          <p v-else class="text-gray-400 mt-3 italic text-sm">No description provided.</p>
        </div>

        <!-- Comments -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
          <h2 class="font-semibold text-gray-800 mb-4">
            Comments <span class="text-gray-400 font-normal">({{ currentTask.comments?.length ?? 0 }})</span>
          </h2>

          <div class="flex gap-3 mb-5">
            <img :src="authStore.user?.avatar_url" :alt="authStore.user?.name" class="w-8 h-8 rounded-full flex-shrink-0 mt-0.5" />
            <div class="flex-1">
              <textarea
                v-model="newComment"
                rows="3"
                placeholder="Write a comment..."
                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-400"
              ></textarea>
              <button
                @click="handleComment"
                :disabled="!newComment.trim()"
                class="mt-2 px-4 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-40 transition"
              >
                Comment
              </button>
            </div>
          </div>

          <div class="space-y-4">
            <div v-for="comment in currentTask.comments" :key="comment.id" class="flex gap-3">
              <img :src="comment.user.avatar_url" :alt="comment.user.name" class="w-7 h-7 rounded-full flex-shrink-0 mt-0.5" />
              <div class="flex-1">
                <div class="flex items-baseline gap-2">
                  <span class="text-sm font-medium text-gray-800">{{ comment.user.name }}</span>
                  <span class="text-xs text-gray-400">{{ format(new Date(comment.created_at), 'MMM d, HH:mm') }}</span>
                </div>
                <p class="text-sm text-gray-600 mt-0.5 whitespace-pre-wrap">{{ comment.body }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 space-y-4">
          <div>
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Status</label>
            <select
              :value="currentTask.status"
              @change="handleStatusChange(($event.target as HTMLSelectElement).value)"
              class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >
              <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
          </div>

          <div v-if="currentTask.assignee">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Assignee</label>
            <div class="flex items-center gap-2">
              <img :src="currentTask.assignee.avatar_url" :alt="currentTask.assignee.name" class="w-7 h-7 rounded-full" />
              <span class="text-sm text-gray-700">{{ currentTask.assignee.name }}</span>
            </div>
          </div>

          <div v-if="currentTask.due_date">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Due Date</label>
            <p class="text-sm text-gray-700">{{ format(new Date(currentTask.due_date), 'MMMM d, yyyy') }}</p>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Priority</label>
            <span class="inline-block text-xs px-2 py-0.5 rounded-full font-medium capitalize bg-orange-50 text-orange-600">
              {{ currentTask.priority }}
            </span>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Created</label>
            <p class="text-sm text-gray-500">{{ format(new Date(currentTask.created_at), 'MMM d, yyyy') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="flex items-center justify-center h-64">
    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
  </div>
</template>
