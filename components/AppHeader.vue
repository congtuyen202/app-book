<template>
  <header class="bg-background border-b border-border text-foreground fixed top-0 left-0 right-0 z-50 h-16">
    <div class="container mx-auto h-full flex items-center justify-between px-4">
      <!-- Logo -->
      <NuxtLink to="/" class="text-2xl font-bold text-foreground hover:opacity-80 transition-opacity">
        MyAppLogo
      </NuxtLink>

      <!-- Desktop Navigation & Actions -->
      <nav class="hidden md:flex items-center space-x-4">
        <NuxtLink to="/" class="text-muted-foreground hover:text-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">Home</NuxtLink>
        <NuxtLink to="/books" class="text-muted-foreground hover:text-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">Books</NuxtLink>
        <NuxtLink to="/blog" class="text-muted-foreground hover:text-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">Blog</NuxtLink>
        <NuxtLink to="/about" class="text-muted-foreground hover:text-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">About</NuxtLink>
        <NuxtLink to="/chat" class="text-muted-foreground hover:text-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">Chat Support</NuxtLink>

        <template v-if="isLoggedIn">
          <NuxtLink to="/profile" class="text-muted-foreground hover:text-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">Profile</NuxtLink>
          <NuxtLink to="/settings" class="text-muted-foreground hover:text-primary transition-colors px-3 py-2 rounded-md text-sm font-medium">Settings</NuxtLink>
          <Button variant="ghost" size="sm" @click="handleLogout" class="text-muted-foreground hover:text-primary hover:bg-accent px-3 py-2 text-sm font-medium">Logout</Button>
        </template>
        <template v-else>
          <NuxtLink to="/auth/login">
            <Button variant="secondary" size="sm" class="px-4 py-1.5 h-auto">Login</Button>
          </NuxtLink>
          <!-- Register button removed from non-logged-in state -->
        </template>

        <!-- Theme Switcher Button (Desktop) -->
        <Button variant="ghost" size="icon" @click="toggleTheme" aria-label="Toggle theme" class="text-muted-foreground hover:text-primary hover:bg-accent">
          <IconSun v-if="currentTheme === 'light'" class="h-5 w-5" />
          <IconMoon v-else class="h-5 w-5" /> <!-- Only two states now -->
        </Button>
      </nav>

      <!-- Mobile Menu Button -->
      <div class="md:hidden">
        <Button variant="ghost" size="icon" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Open menu" class="text-muted-foreground hover:text-primary hover:bg-accent">
          <IconMenu class="h-6 w-6" />
        </Button>
      </div>
    </div>

    <!-- Mobile Menu Overlay/Dropdown -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-1"
    >
      <div v-if="mobileMenuOpen" class="md:hidden absolute top-16 left-0 right-0 bg-background border-t border-border shadow-lg z-40">
        <nav class="container mx-auto flex flex-col px-4 py-2 space-y-1">
          <NuxtLink to="/" class="text-muted-foreground hover:bg-accent hover:text-primary transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Home</NuxtLink>
          <NuxtLink to="/books" class="text-muted-foreground hover:bg-accent hover:text-primary transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Books</NuxtLink>
          <NuxtLink to="/blog" class="text-muted-foreground hover:bg-accent hover:text-primary transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Blog</NuxtLink>
          <NuxtLink to="/about" class="text-muted-foreground hover:bg-accent hover:text-primary transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">About</NuxtLink>
          <NuxtLink to="/chat" class="text-muted-foreground hover:bg-accent hover:text-primary transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Chat Support</NuxtLink>

          <template v-if="isLoggedIn">
            <NuxtLink to="/profile" class="text-muted-foreground hover:bg-accent hover:text-primary transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Profile</NuxtLink>
            <NuxtLink to="/settings" class="text-muted-foreground hover:bg-accent hover:text-primary transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Settings</NuxtLink>
            <Button variant="ghost" class="w-full justify-start px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-primary" @click="handleLogoutAndCloseMenu">Logout</Button>
          </template>
          <template v-else>
            <NuxtLink to="/auth/login" class="text-muted-foreground hover:bg-accent hover:text-primary transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Login</NuxtLink>
            <!-- Register button removed from mobile menu non-logged-in state -->
          </template>

          <div class="border-t border-border pt-2 mt-2">
             <Button variant="ghost" class="w-full justify-start px-3 py-2 text-base font-medium flex items-center gap-2 text-muted-foreground hover:bg-accent hover:text-primary" @click="toggleThemeAndCloseMenu" aria-label="Toggle theme">
                <IconSun v-if="currentTheme === 'light'" class="h-5 w-5" />
                <IconMoon v-else class="h-5 w-5" />
                <span>Change to {{ currentTheme === 'light' ? 'Dark' : 'Light' }} Mode</span>
            </Button>
          </div>
        </nav>
      </div>
    </Transition>
  </header>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useReaderSettings } from '~/composables/useReaderSettings';
import { Button } from '@/components/ui/button';
// NuxtLink and navigateTo are auto-imported
// Icon components (IconSun, IconMoon, IconMenu) are expected to be auto-imported by nuxt-lucide-icons. IconLaptop removed.

const { settings: readerSettings } = useReaderSettings();

const mobileMenuOpen = ref(false);

// Mock authentication state
const isLoggedIn = ref(false); // Set to true to see authenticated state, or integrate with actual auth state

const handleLogout = () => {
  isLoggedIn.value = false;
  // In a real app, call auth service and navigate
  alert('Logged out (mock action)');
  if (typeof window !== 'undefined') navigateTo('/'); // Navigate to home after logout
};

const closeMobileMenu = () => {
  mobileMenuOpen.value = false;
};

const handleLogoutAndCloseMenu = () => {
  handleLogout();
  closeMobileMenu();
};

const currentTheme = computed(() => readerSettings.value.theme);

const toggleTheme = () => {
  readerSettings.value.theme = readerSettings.value.theme === 'light' ? 'dark' : 'light';
};

const toggleThemeAndCloseMenu = () => {
  toggleTheme();
  closeMobileMenu();
};
</script>
