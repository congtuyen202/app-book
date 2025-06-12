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
// Imports for Button, Card, Input, and chat-related refs/functions removed.
// AppHeader, AppFooter, NuxtLink are auto-imported by Nuxt (or globally registered)
// Lucide icons are expected to be auto-imported via nuxt-lucide-icons module
</script>

<style>
/* Chat-specific styles (animations, typing indicator, custom scrollbar) removed. */
/* Global styles (like .font-main if it's truly global) should remain or be in a global CSS file. */

/* Ensure the main layout div itself uses theme background if not already handled by app.vue */
/* This was already here, ensuring it's correctly applied */
.font-main {
    background-color: hsl(var(--background));
    color: hsl(var(--foreground));
}
</style>