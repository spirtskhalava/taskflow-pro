<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center">
      <p class="text-7xl font-bold text-indigo-200">404</p>
      <h1 class="text-2xl font-bold text-gray-800 mt-4">Page not found</h1>
      <p class="text-gray-400 mt-2">The page you're looking for doesn't exist.</p>
      <RouterLink to="/dashboard" class="mt-6 inline-block px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition">
        Go to Dashboard
      </RouterLink>
    </div>
  </div>
</template>
