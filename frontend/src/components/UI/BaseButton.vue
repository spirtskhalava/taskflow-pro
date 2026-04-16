<script setup lang="ts">
withDefaults(
  defineProps<{
    type?: 'button' | 'submit' | 'reset'
    loading?: boolean
    disabled?: boolean
    size?: 'sm' | 'md' | 'lg'
    variant?: 'primary' | 'secondary' | 'danger'
  }>(),
  {
    type: 'button',
    loading: false,
    disabled: false,
    size: 'md',
    variant: 'primary',
  }
)
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    class="inline-flex items-center justify-center font-medium rounded-xl transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
    :class="{
      'px-4 py-2 text-sm': size === 'md',
      'px-3 py-1.5 text-xs': size === 'sm',
      'px-5 py-2.5 text-base': size === 'lg',
      'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 shadow-sm': variant === 'primary',
      'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-indigo-500': variant === 'secondary',
      'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500': variant === 'danger',
    }"
  >
    <svg
      v-if="loading"
      class="animate-spin -ml-0.5 mr-2 h-4 w-4"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
    </svg>
    <slot />
  </button>
</template>
