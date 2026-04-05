import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import type { Task, TaskStatus } from '@/types'

export const useTaskStore = defineStore('task', () => {
  const tasks = ref<Task[]>([])
  const currentTask = ref<Task | null>(null)
  const isLoading = ref(false)

  const tasksByStatus = computed(() => {
    const groups: Record<TaskStatus, Task[]> = {
      todo: [],
      in_progress: [],
      in_review: [],
      done: [],
    }
    for (const task of tasks.value) {
      groups[task.status].push(task)
    }
    return groups
  })

  async function fetchTasks(projectId: number, params: Record<string, unknown> = {}): Promise<void> {
    isLoading.value = true
    try {
      const { data } = await api.get<Task[]>(`/projects/${projectId}/tasks`, { params })
      tasks.value = data
    } finally {
      isLoading.value = false
    }
  }

  async function fetchTask(taskId: number): Promise<void> {
    const { data } = await api.get<Task>(`/tasks/${taskId}`)
    currentTask.value = data
  }

  async function createTask(projectId: number, payload: Partial<Task>): Promise<Task> {
    const { data } = await api.post<Task>(`/projects/${projectId}/tasks`, payload)
    tasks.value.push(data)
    return data
  }

  async function updateTask(taskId: number, payload: Partial<Task>): Promise<Task> {
    const { data } = await api.put<Task>(`/tasks/${taskId}`, payload)
    updateInList(data)
    if (currentTask.value?.id === taskId) {
      currentTask.value = data
    }
    return data
  }

  async function updateStatus(taskId: number, status: TaskStatus): Promise<Task> {
    const { data } = await api.patch<Task>(`/tasks/${taskId}/status`, { status })
    updateInList(data)
    return data
  }

  async function deleteTask(taskId: number): Promise<void> {
    await api.delete(`/tasks/${taskId}`)
    tasks.value = tasks.value.filter((t) => t.id !== taskId)
    if (currentTask.value?.id === taskId) {
      currentTask.value = null
    }
  }

  async function addComment(taskId: number, body: string): Promise<void> {
    const { data } = await api.post(`/tasks/${taskId}/comments`, { body })
    if (currentTask.value?.id === taskId) {
      currentTask.value = {
        ...currentTask.value,
        comments: [data, ...(currentTask.value.comments ?? [])],
      }
    }
  }

  function updateInList(task: Task): void {
    const index = tasks.value.findIndex((t) => t.id === task.id)
    if (index !== -1) {
      tasks.value[index] = task
    }
  }

  function moveTaskOptimistic(taskId: number, newStatus: TaskStatus): void {
    const task = tasks.value.find((t) => t.id === taskId)
    if (task) {
      task.status = newStatus
    }
  }

  return {
    tasks,
    currentTask,
    isLoading,
    tasksByStatus,
    fetchTasks,
    fetchTask,
    createTask,
    updateTask,
    updateStatus,
    deleteTask,
    addComment,
    moveTaskOptimistic,
  }
})
