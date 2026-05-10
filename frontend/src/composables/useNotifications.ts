import { ref, onMounted, onUnmounted } from 'vue'
import api from '@/services/api'

interface Notification {
  id: string
  type: string
  data: {
    task_id?: number
    task_title?: string
    project_id?: number
    message: string
  }
  read_at: string | null
  created_at: string
}

export function useNotifications() {
  const notifications = ref<Notification[]>([])
  const unreadCount = ref(0)
  let pollInterval: ReturnType<typeof setInterval> | null = null

  async function fetchNotifications(): Promise<void> {
    const { data } = await api.get<Notification[]>('/notifications')
    notifications.value = data
    unreadCount.value = data.filter((n) => !n.read_at).length
  }

  async function markAsRead(id: string): Promise<void> {
    await api.patch(`/notifications/${id}/read`)
    const notification = notifications.value.find((n) => n.id === id)
    if (notification) {
      notification.read_at = new Date().toISOString()
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
  }

  async function markAllAsRead(): Promise<void> {
    await api.post('/notifications/read-all')
    notifications.value.forEach((n) => {
      n.read_at = n.read_at ?? new Date().toISOString()
    })
    unreadCount.value = 0
  }

  onMounted(() => {
    fetchNotifications()
    // Poll every 30 seconds as fallback when WebSocket is unavailable
    pollInterval = setInterval(fetchNotifications, 30_000)
  })

  onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval)
  })

  return { notifications, unreadCount, markAsRead, markAllAsRead }
}
