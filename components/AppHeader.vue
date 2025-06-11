<template>
  <header class="bg-primary text-primary-foreground shadow-md fixed top-0 left-0 right-0 z-50 h-16">
    <div class="container mx-auto h-full flex items-center justify-between px-4">
      <!-- Logo -->
      <NuxtLink to="/" class="text-2xl font-bold hover:opacity-80 transition-opacity">
        MyAppLogo
      </NuxtLink>

      <!-- Desktop Navigation & Actions -->
      <nav class="hidden md:flex items-center space-x-4">
        <NuxtLink to="/" class="hover:text-secondary transition-colors px-3 py-2 rounded-md text-sm font-medium">Home</NuxtLink>
        <NuxtLink to="/books" class="hover:text-secondary transition-colors px-3 py-2 rounded-md text-sm font-medium">Books</NuxtLink>
        <NuxtLink to="/about" class="hover:text-secondary transition-colors px-3 py-2 rounded-md text-sm font-medium">About</NuxtLink>

        <template v-if="isLoggedIn">
          <NuxtLink to="/profile" class="hover:text-secondary transition-colors px-3 py-2 rounded-md text-sm font-medium">Profile</NuxtLink>
          <NuxtLink to="/settings" class="hover:text-secondary transition-colors px-3 py-2 rounded-md text-sm font-medium">Settings</NuxtLink>
          <Button variant="ghost" size="sm" @click="handleLogout">Logout</Button>
        </template>
        <template v-else>
          <NuxtLink to="/auth/login">
            <Button variant="secondary" size="sm">Login</Button>
          </NuxtLink>
          <NuxtLink to="/auth/register">
            <Button variant="outline" size="sm" class="text-primary-foreground border-primary-foreground/50 hover:bg-primary-foreground/10 hover:text-primary-foreground">
              Register
            </Button>
          </NuxtLink>
        </template>

        <!-- Theme Switcher Button (Desktop) -->
        <Button variant="ghost" size="icon" @click="toggleTheme" aria-label="Toggle theme">
          <IconSun v-if="currentTheme === 'light'" class="h-5 w-5" />
          <IconMoon v-else-if="currentTheme === 'dark'" class="h-5 w-5" />
          <IconLaptop v-else class="h-5 w-5" /> <!-- System theme icon -->
        </Button>
      </nav>

      <!-- Mobile Menu Button -->
      <div class="md:hidden">
        <Button variant="ghost" size="icon" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Open menu">
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
      <div v-if="mobileMenuOpen" class="md:hidden absolute top-16 left-0 right-0 bg-primary shadow-lg z-40">
        <nav class="container mx-auto flex flex-col px-4 py-2 space-y-1">
          <NuxtLink to="/" class="hover:bg-primary-foreground/10 transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Home</NuxtLink>
          <NuxtLink to="/books" class="hover:bg-primary-foreground/10 transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Books</NuxtLink>
          <NuxtLink to="/about" class="hover:bg-primary-foreground/10 transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">About</NuxtLink>

          <template v-if="isLoggedIn">
            <NuxtLink to="/profile" class="hover:bg-primary-foreground/10 transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Profile</NuxtLink>
            <NuxtLink to="/settings" class="hover:bg-primary-foreground/10 transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Settings</NuxtLink>
            <Button variant="ghost" class="w-full justify-start px-3 py-2 text-base font-medium hover:bg-primary-foreground/10" @click="handleLogoutAndCloseMenu">Logout</Button>
          </template>
          <template v-else>
            <NuxtLink to="/auth/login" class="hover:bg-primary-foreground/10 transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Login</NuxtLink>
            <NuxtLink to="/auth/register" class="hover:bg-primary-foreground/10 transition-colors block px-3 py-2 rounded-md text-base font-medium" @click="closeMobileMenu">Register</NuxtLink>
          </template>

          <div class="border-t border-primary-foreground/20 pt-2 mt-2">
             <Button variant="ghost" class="w-full justify-start px-3 py-2 text-base font-medium flex items-center gap-2 hover:bg-primary-foreground/10" @click="toggleThemeAndCloseMenu" aria-label="Toggle theme">
                <IconSun v-if="currentTheme === 'light'" class="h-5 w-5" />
                <IconMoon v-else-if="currentTheme === 'dark'" class="h-5 w-5" />
                <IconLaptop v-else class="h-5 w-5" />
                <span>Change Theme ({{ currentTheme }})</span>
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
// Icon components (IconSun, IconMoon, IconLaptop, IconMenu) are expected to be auto-imported by nuxt-lucide-icons

const { settings: readerSettings } = useReaderSettings();

const mobileMenuOpen = ref(false);

// Mock authentication state
const isLoggedIn = ref(false); // Set to true to see authenticated state, or integrate with actual auth state

const handleLogout = () => {
  isLoggedIn.value = false;
  // In a real app, call auth service and navigate
  alert('Logged out (mock action)');
  navigateTo('/'); // Navigate to home after logout
};

const closeMobileMenu = () => {
  mobileMenuOpen.value = false;
};

const handleLogoutAndCloseMenu = () => { // Corrected function name from spec
  handleLogout();
  closeMobileMenu();
};

const currentTheme = computed(() => readerSettings.value.theme);

const toggleTheme = () => {
  const themes: Array<typeof readerSettings.value.theme> = ['light', 'dark', 'system'];
  const currentIndex = themes.indexOf(readerSettings.value.theme);
  const nextIndex = (currentIndex + 1) % themes.length;
  readerSettings.value.theme = themes[nextIndex];
};

const toggleThemeAndCloseMenu = () => {
  toggleTheme();
  closeMobileMenu();
};
</script>
