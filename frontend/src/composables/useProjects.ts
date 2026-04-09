import { useProjectStore } from '@/stores/project'
import { storeToRefs } from 'pinia'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

export function useProjects() {
  const projectStore = useProjectStore()
  const { projects, currentProject, isLoading, pagination } = storeToRefs(projectStore)
  const router = useRouter()
  const formErrors = ref<Record<string, string[]>>({})

  async function createProject(payload: { name: string; description?: string; color?: string; deadline?: string }): Promise<void> {
    formErrors.value = {}
    try {
      const project = await projectStore.createProject(payload)
      await router.push(`/projects/${project.id}`)
    } catch (error: any) {
      if (error.response?.status === 422) {
        formErrors.value = error.response.data.errors ?? {}
      }
    }
  }

  async function deleteProject(id: number): Promise<void> {
    await projectStore.deleteProject(id)
    await router.push('/projects')
  }

  return {
    projects,
    currentProject,
    isLoading,
    pagination,
    formErrors,
    fetchProjects: projectStore.fetchProjects,
    fetchProject: projectStore.fetchProject,
    createProject,
    updateProject: projectStore.updateProject,
    deleteProject,
    archiveProject: projectStore.archiveProject,
    inviteMember: projectStore.inviteMember,
    removeMember: projectStore.removeMember,
  }
}
