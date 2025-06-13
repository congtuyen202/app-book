<template>
    <div class="font-main w-full relative min-h-screen bg-background flex flex-col">
        <AppHeader />
        <main class="pt-16 flex-grow">
            <slot />
        </main>
        <AppFooter />
        <!-- Chat Popup and Modal removed -->
    </div>
</template>

<script setup lang="ts">
// AppHeader, AppFooter, NuxtLink are auto-imported by Nuxt (or globally registered)
// Lucide icons are expected to be auto-imported via nuxt-lucide-icons module
// All chat-related script logic has been moved to pages/chat/index.vue
</script>

<style>
/* Chat-specific styles (animations, typing indicator, custom scrollbar) removed. */
/* Global styles (like .font-main) should remain or be in a global CSS file. */
.font-main {
    background-color: hsl(var(--background));
    color: hsl(var(--foreground));
}
</style>