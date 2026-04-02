export type TaskStatus = 'todo' | 'in_progress' | 'in_review' | 'done'
export type TaskPriority = 'low' | 'medium' | 'high' | 'urgent'
export type ProjectRole = 'owner' | 'admin' | 'member'

export interface User {
  id: number
  name: string
  email: string
  avatar_url: string
  timezone?: string
  role?: ProjectRole
  created_at: string
}

export interface Project {
  id: number
  name: string
  description?: string
  status: string
  color: string
  deadline?: string
  is_archived: boolean
  completion_percentage: number
  tasks_count?: number
  completed_tasks_count?: number
  owner: User
  members: User[]
  tasks?: Task[]
  created_at: string
  updated_at: string
}

export interface Task {
  id: number
  project_id: number
  title: string
  description?: string
  status: TaskStatus
  priority: TaskPriority
  position: number
  due_date?: string
  completed_at?: string
  estimated_hours?: number
  comments_count?: number
  assignee?: User
  reporter?: User
  project?: Project
  comments?: Comment[]
  attachments?: Attachment[]
  created_at: string
  updated_at: string
}

export interface Comment {
  id: number
  body: string
  user: User
  created_at: string
  updated_at: string
}

export interface Attachment {
  id: number
  name: string
  url: string
  mime_type: string
  size: number
  created_at: string
}

export interface DashboardStats {
  total_tasks: number
  my_tasks: number
  overdue: number
  completed: number
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}
