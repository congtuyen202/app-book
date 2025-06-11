<template>
  <footer class="bg-muted text-muted-foreground py-8 sm:py-12">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Column 1: Logo and Copyright -->
        <div class="space-y-4 text-center md:text-left">
          <NuxtLink to="/" class="inline-block text-xl font-bold text-foreground hover:opacity-80 transition-opacity">
            MyAppLogo
          </NuxtLink>
          <p class="text-sm">
            &copy; {{ new Date().getFullYear() }} MyAppLogo. All rights reserved.
          </p>
          <p class="text-xs">
            Making reading accessible and enjoyable for everyone.
          </p>
        </div>

        <!-- Column 2: Links -->
        <div class="space-y-2 text-center md:text-left">
          <h3 class="text-sm font-semibold text-foreground uppercase tracking-wider">Quick Links</h3>
          <ul class="space-y-1">
            <li><NuxtLink to="/about" class="hover:text-primary transition-colors text-sm">About Us</NuxtLink></li>
            <li><NuxtLink to="/books" class="hover:text-primary transition-colors text-sm">Browse Books</NuxtLink></li>
            <li><NuxtLink to="#" class="hover:text-primary transition-colors text-sm opacity-70 cursor-not-allowed" @click.prevent>Contact Us (Soon)</NuxtLink></li>
            <li><NuxtLink to="#" class="hover:text-primary transition-colors text-sm opacity-70 cursor-not-allowed" @click.prevent>Privacy Policy (Soon)</NuxtLink></li>
            <li><NuxtLink to="#" class="hover:text-primary transition-colors text-sm opacity-70 cursor-not-allowed" @click.prevent>Terms of Service (Soon)</NuxtLink></li>
          </ul>
        </div>

        <!-- Column 3: Social Media -->
        <div class="space-y-2 text-center md:text-left">
          <h3 class="text-sm font-semibold text-foreground uppercase tracking-wider">Connect With Us</h3>
          <div class="flex space-x-4 justify-center md:justify-start">
            <a href="#" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors" aria-label="Facebook">
              <IconFacebook class="h-6 w-6" />
            </a>
            <a href="#" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors" aria-label="Twitter">
              <IconTwitter class="h-6 w-6" />
            </a>
            <a href="#" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors" aria-label="Instagram">
              <IconInstagram class="h-6 w-6" />
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Back to Top Button -->
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-2"
  >
    <Button
      v-if="showBackToTop"
      variant="default"
      size="icon"
      class="fixed bottom-6 right-6 rounded-full shadow-lg z-[999]"
      aria-label="Back to top"
      @click="scrollToTop"
    >
      <IconArrowUp class="h-6 w-6" />
    </Button>
  </Transition>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Button } from '@/components/ui/button';
// NuxtLink is auto-imported
// Icon components (IconFacebook, IconTwitter, IconInstagram, IconArrowUp) are expected to be auto-imported by nuxt-lucide-icons

const showBackToTop = ref(false);

const handleScroll = () => {
  if (typeof window !== 'undefined') {
    if (window.scrollY > 200) {
      showBackToTop.value = true;
    } else {
      showBackToTop.value = false;
    }
  }
};

const scrollToTop = () => {
  if (typeof window !== 'undefined') {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('scroll', handleScroll);
  }
});

onBeforeUnmount(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('scroll', handleScroll);
  }
});
</script>
