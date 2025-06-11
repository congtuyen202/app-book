<template>
  <header class="bg-primary text-primary-foreground shadow-md fixed top-0 left-0 right-0 z-50 h-16 flex items-center px-4">
    <div class="container mx-auto flex items-center justify-between">
      <NuxtLink to="/" class="text-xl font-bold">MyApp</NuxtLink>
      <nav class="space-x-4">
        <NuxtLink to="/" class="hover:text-secondary">Home</NuxtLink>
        <NuxtLink to="/about" class="hover:text-secondary">About</NuxtLink>
      </nav>
    </div>
  </header>
</template>
<script setup lang="ts">
// Add any script setup if needed
</script>
