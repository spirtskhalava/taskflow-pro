import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'
import type { Project, PaginatedResponse } from '@/types'

export const useProjectStore = defineStore('project', () => {
  const projects = ref<Project[]>([])
  const currentProject = ref<Project | null>(null)
  const isLoading = ref(false)
  const pagination = ref({ currentPage: 1, lastPage: 1, total: 0 })

  async function fetchProjects(params: Record<string, unknown> = {}): Promise<void> {
    isLoading.value = true
    try {
      const { data } = await api.get<PaginatedResponse<Project>>('/projects', { params })
      projects.value = data.data
      pagination.value = {
        currentPage: data.current_page,
        lastPage: data.last_page,
        total: data.total,
      }
    } finally {
      isLoading.value = false
    }
  }

  async function fetchProject(id: number): Promise<void> {
    isLoading.value = true
    try {
      const { data } = await api.get<Project>(`/projects/${id}`)
      currentProject.value = data
    } finally {
      isLoading.value = false
    }
  }

  async function createProject(payload: Partial<Project>): Promise<Project> {
    const { data } = await api.post<Project>('/projects', payload)
    projects.value.unshift(data)
    return data
  }

  async function updateProject(id: number, payload: Partial<Project>): Promise<Project> {
    const { data } = await api.put<Project>(`/projects/${id}`, payload)
    updateInList(data)
    if (currentProject.value?.id === id) {
      currentProject.value = data
    }
    return data
  }

  async function deleteProject(id: number): Promise<void> {
    await api.delete(`/projects/${id}`)
    projects.value = projects.value.filter((p) => p.id !== id)
    if (currentProject.value?.id === id) {
      currentProject.value = null
    }
  }

  async function archiveProject(id: number): Promise<void> {
    const { data } = await api.patch<Project>(`/projects/${id}/archive`)
    updateInList(data)
  }

  async function inviteMember(projectId: number, email: string, role: string): Promise<void> {
    await api.post(`/projects/${projectId}/members/invite`, { email, role })
    await fetchProject(projectId)
  }

  async function removeMember(projectId: number, memberId: number): Promise<void> {
    await api.delete(`/projects/${projectId}/members/${memberId}`)
    if (currentProject.value?.id === projectId) {
      currentProject.value = {
        ...currentProject.value,
        members: currentProject.value.members.filter((m) => m.id !== memberId),
      }
    }
  }

  function updateInList(project: Project): void {
    const index = projects.value.findIndex((p) => p.id === project.id)
    if (index !== -1) {
      projects.value[index] = project
    }
  }

  return {
    projects,
    currentProject,
    isLoading,
    pagination,
    fetchProjects,
    fetchProject,
    createProject,
    updateProject,
    deleteProject,
    archiveProject,
    inviteMember,
    removeMember,
  }
})
