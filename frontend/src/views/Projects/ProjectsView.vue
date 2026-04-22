<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useProjects } from '@/composables/useProjects'
import BaseButton from '@/components/UI/BaseButton.vue'
import BaseModal from '@/components/UI/BaseModal.vue'

const { projects, isLoading, fetchProjects, createProject, formErrors } = useProjects()

const showCreateModal = ref(false)
const newProject = ref({ name: '', description: '', color: '#6366f1', deadline: '' })

const colors = ['#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6', '#06b6d4']

async function handleCreate() {
  await createProject(newProject.value)
  if (!Object.keys(formErrors.value).length) {
    showCreateModal.value = false
    newProject.value = { name: '', description: '', color: '#6366f1', deadline: '' }
  }
}

onMounted(() => fetchProjects())
</script>

<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
        <p class="text-gray-500 text-sm mt-1">{{ projects.length }} active projects</p>
      </div>
      <BaseButton @click="showCreateModal = true">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Project
      </BaseButton>
    </div>

    <div v-if="isLoading" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="bg-white rounded-2xl p-5 shadow-sm animate-pulse h-40"></div>
    </div>

    <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <RouterLink
        v-for="project in projects"
        :key="project.id"
        :to="`/projects/${project.id}`"
        class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-100 transition group block"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center" :style="{ backgroundColor: project.color + '20' }">
            <div class="w-4 h-4 rounded-full" :style="{ backgroundColor: project.color }"></div>
          </div>
          <span v-if="project.deadline" class="text-xs text-gray-400">
            Due {{ new Date(project.deadline).toLocaleDateString('en', { month: 'short', day: 'numeric' }) }}
          </span>
        </div>

        <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition truncate">
          {{ project.name }}
        </h3>
        <p class="text-sm text-gray-400 mt-1 line-clamp-2">{{ project.description || 'No description' }}</p>

        <div class="mt-4">
          <div class="flex items-center justify-between text-xs text-gray-400 mb-1.5">
            <span>{{ project.completed_tasks_count ?? 0 }}/{{ project.tasks_count ?? 0 }} tasks</span>
            <span>{{ project.completion_percentage }}%</span>
          </div>
          <div class="w-full bg-gray-100 rounded-full h-1.5">
            <div
              class="h-1.5 rounded-full transition-all"
              :style="{ width: `${project.completion_percentage}%`, backgroundColor: project.color }"
            ></div>
          </div>
        </div>

        <div class="mt-4 flex items-center">
          <div class="flex -space-x-2">
            <img
              v-for="member in project.members.slice(0, 4)"
              :key="member.id"
              :src="member.avatar_url"
              :alt="member.name"
              :title="member.name"
              class="w-7 h-7 rounded-full border-2 border-white"
            />
          </div>
          <span v-if="project.members.length > 4" class="text-xs text-gray-400 ml-2">
            +{{ project.members.length - 4 }} more
          </span>
        </div>
      </RouterLink>
    </div>

    <div v-if="!isLoading && !projects.length" class="text-center py-20">
      <div class="text-5xl mb-4">📋</div>
      <h3 class="text-lg font-semibold text-gray-700">No projects yet</h3>
      <p class="text-gray-400 mt-1">Create your first project to get started.</p>
      <BaseButton class="mt-4" @click="showCreateModal = true">Create Project</BaseButton>
    </div>

    <!-- Create modal -->
    <BaseModal v-model="showCreateModal" title="New Project">
      <form @submit.prevent="handleCreate" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Project name *</label>
          <input
            v-model="newProject.name"
            type="text"
            required
            placeholder="e.g. Marketing Website"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            :class="{ 'border-red-400': formErrors.name }"
          />
          <p v-if="formErrors.name" class="text-red-500 text-xs mt-1">{{ formErrors.name[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea
            v-model="newProject.description"
            rows="3"
            placeholder="Optional project description..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"
          ></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
          <div class="flex gap-2 flex-wrap">
            <button
              v-for="color in colors"
              :key="color"
              type="button"
              class="w-7 h-7 rounded-full border-2 transition"
              :style="{ backgroundColor: color }"
              :class="newProject.color === color ? 'border-gray-900 scale-110' : 'border-transparent'"
              @click="newProject.color = color"
            ></button>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
          <input
            v-model="newProject.deadline"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
          />
        </div>

        <div class="flex justify-end gap-3 pt-2">
          <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">
            Cancel
          </button>
          <BaseButton type="submit">Create Project</BaseButton>
        </div>
      </form>
    </BaseModal>
  </div>
</template>
