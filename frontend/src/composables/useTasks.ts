import { useTaskStore } from '@/stores/task'
import { storeToRefs } from 'pinia'
import type { TaskStatus } from '@/types'
import { ref } from 'vue'

export function useTasks(projectId?: number) {
  const taskStore = useTaskStore()
  const { tasks, currentTask, isLoading, tasksByStatus } = storeToRefs(taskStore)
  const formErrors = ref<Record<string, string[]>>({})

  async function createTask(payload: Record<string, unknown>): Promise<void> {
    if (!projectId) return
    formErrors.value = {}
    try {
      await taskStore.createTask(projectId, payload)
    } catch (error: any) {
      if (error.response?.status === 422) {
        formErrors.value = error.response.data.errors ?? {}
      }
    }
  }

  async function moveTask(taskId: number, newStatus: TaskStatus): Promise<void> {
    // Optimistic update first for instant UI response
    taskStore.moveTaskOptimistic(taskId, newStatus)
    try {
      await taskStore.updateStatus(taskId, newStatus)
    } catch {
      // Rollback: re-fetch tasks on failure
      if (projectId) await taskStore.fetchTasks(projectId)
    }
  }

  return {
    tasks,
    currentTask,
    isLoading,
    tasksByStatus,
    formErrors,
    fetchTasks: taskStore.fetchTasks,
    fetchTask: taskStore.fetchTask,
    createTask,
    updateTask: taskStore.updateTask,
    deleteTask: taskStore.deleteTask,
    moveTask,
    addComment: taskStore.addComment,
  }
}
