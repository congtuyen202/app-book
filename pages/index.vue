<template>
  <div> <!-- Root element -->
    <!-- Hero Banner Section -->
    <section class="relative bg-gradient-to-br from-primary/10 via-background to-secondary/10 text-foreground py-20 md:py-32 min-h-[calc(70vh)] md:min-h-[calc(80vh)] flex items-center justify-center overflow-hidden">
      <!-- Simple SVG Background Pattern - Conceptual -->
      <div class="absolute inset-0 z-0 opacity-[0.03] dark:opacity-[0.02]">
         <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
           <defs>
             <pattern id="subtleGrid" width="20" height="20" patternUnits="userSpaceOnUse">
               <path d="M 20 0 L 0 0 0 20" fill="none" stroke="currentColor" stroke-width="0.3"/>
             </pattern>
           </defs>
           <rect width="100%" height="100%" fill="url(#subtleGrid)" />
         </svg>
       </div>

      <div class="container mx-auto px-4 text-center relative z-10">
        <h1
          data-aos="fade-down"
          class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 tracking-tight"
        >
          Welcome to <span class="text-primary">MyAppLogo</span>
        </h1>
        <p
          data-aos="fade-up" data-aos-delay="200"
          class="text-lg sm:text-xl md:text-2xl text-muted-foreground max-w-2xl mx-auto mb-10"
        >
          Discover your next favorite book. Join our community of readers and explore a vast world of stories.
        </p>
        <div
          data-aos="fade-up" data-aos-delay="400"
          class="flex flex-col sm:flex-row items-center justify-center gap-4"
        >
          <NuxtLink to="/auth/register">
            <Button size="lg" class="px-8 py-3 text-lg w-full sm:w-auto">
              Get Started - Register Now
            </Button>
          </NuxtLink>
          <Button variant="outline" size="lg" class="px-8 py-3 text-lg border-foreground/50 hover:bg-foreground/5 w-full sm:w-auto">
            <IconDownload class="mr-2 h-5 w-5" />
            Download App (Soon)
          </Button>
        </div>
      </div>
    </section>

    <!-- Key Features Section -->
    <section id="features" class="py-16 md:py-24 bg-background">
      <div class="container mx-auto px-4">
        <h2 data-aos="fade-up" class="text-3xl md:text-4xl font-bold text-center mb-12">
          Why <span class="text-primary">MyAppLogo</span>?
        </h2>
        <div class="grid md:grid-cols-3 gap-8">
          <div data-aos="fade-up" data-aos-delay="100" class="text-center p-6 border border-border rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <IconBookOpen class="h-12 w-12 text-primary mx-auto mb-4" />
            <h3 class="text-xl font-semibold mb-2">Vast Library</h3>
            <p class="text-muted-foreground text-sm">Access thousands of books across all genres. From bestsellers to hidden gems.</p>
          </div>
          <div data-aos="fade-up" data-aos-delay="200" class="text-center p-6 border border-border rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <IconSettings2 class="h-12 w-12 text-primary mx-auto mb-4" />
            <h3 class="text-xl font-semibold mb-2">Customizable Reader</h3>
            <p class="text-muted-foreground text-sm">Tailor your reading experience with adjustable fonts, themes, and layouts.</p>
          </div>
          <div data-aos="fade-up" data-aos-delay="300" class="text-center p-6 border border-border rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <IconUsers class="h-12 w-12 text-primary mx-auto mb-4" />
            <h3 class="text-xl font-semibold mb-2">Community Focused</h3>
            <p class="text-muted-foreground text-sm">Connect with fellow readers, share reviews, and discover new perspectives.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Explore Genres Section (Placeholder) -->
    <section id="genres" class="py-16 md:py-24 bg-muted/50 dark:bg-muted/20">
      <div class="container mx-auto px-4">
        <h2 data-aos="fade-up" class="text-3xl md:text-4xl font-bold text-center mb-12">
          Explore Popular <span class="text-primary">Genres</span>
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
          <!-- Placeholder Genre Cards -->
          <div data-aos="zoom-in" class="aspect-square bg-primary/10 dark:bg-primary/20 rounded-lg flex items-center justify-center p-4 hover:scale-105 transition-transform cursor-pointer hover:bg-primary/20 dark:hover:bg-primary/30">
            <p class="text-xl font-semibold text-primary">Fiction</p>
          </div>
          <div data-aos="zoom-in" data-aos-delay="100" class="aspect-square bg-primary/10 dark:bg-primary/20 rounded-lg flex items-center justify-center p-4 hover:scale-105 transition-transform cursor-pointer hover:bg-primary/20 dark:hover:bg-primary/30">
            <p class="text-xl font-semibold text-primary">Mystery</p>
          </div>
          <div data-aos="zoom-in" data-aos-delay="200" class="aspect-square bg-primary/10 dark:bg-primary/20 rounded-lg flex items-center justify-center p-4 hover:scale-105 transition-transform cursor-pointer hover:bg-primary/20 dark:hover:bg-primary/30">
            <p class="text-xl font-semibold text-primary">Sci-Fi</p>
          </div>
          <div data-aos="zoom-in" data-aos-delay="300" class="aspect-square bg-primary/10 dark:bg-primary/20 rounded-lg flex items-center justify-center p-4 hover:scale-105 transition-transform cursor-pointer hover:bg-primary/20 dark:hover:bg-primary/30">
            <p class="text-xl font-semibold text-primary">Fantasy</p>
          </div>
          <!-- Add more genre placeholders as needed -->
        </div>
      </div>
    </section>

    <!-- Testimonials Section (Placeholder) -->
    <section id="testimonials" class="py-16 md:py-24 bg-background">
      <div class="container mx-auto px-4">
        <h2 data-aos="fade-up" class="text-3xl md:text-4xl font-bold text-center mb-12">
          What Our <span class="text-primary">Readers Say</span>
        </h2>
        <div class="grid md:grid-cols-2 gap-8">
          <Card data-aos="fade-right" class="shadow-sm hover:shadow-md transition-shadow">
            <CardHeader class="flex flex-row items-center gap-4">
              <Avatar class="h-12 w-12">
                <AvatarImage src="https://source.unsplash.com/random/100x100/?person,woman" alt="User 1" />
                <AvatarFallback>U1</AvatarFallback>
              </Avatar>
              <div>
                <CardTitle>Sarah W.</CardTitle>
                <CardDescription> Avid Reader</CardDescription>
              </div>
            </CardHeader>
            <CardContent>
              <p class="text-muted-foreground">"MyAppLogo has revolutionized how I discover and read books. The customizable reader is a game-changer!"</p>
            </CardContent>
          </Card>
          <Card data-aos="fade-left" data-aos-delay="100" class="shadow-sm hover:shadow-md transition-shadow">
            <CardHeader class="flex flex-row items-center gap-4">
              <Avatar class="h-12 w-12">
                <AvatarImage src="https://source.unsplash.com/random/100x100/?person,man" alt="User 2" />
                <AvatarFallback>U2</AvatarFallback>
              </Avatar>
              <div>
                <CardTitle>John B.</CardTitle>
                <CardDescription>Bookworm</CardDescription>
              </div>
            </CardHeader>
            <CardContent>
              <p class="text-muted-foreground">"The vast library and community features keep me coming back. Highly recommended!"</p>
            </CardContent>
          </Card>
        </div>
      </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 md:py-24 bg-muted/50 dark:bg-muted/20">
      <div class="container mx-auto px-4">
        <h2 data-aos="fade-up" class="text-3xl md:text-4xl font-bold text-center mb-12">
          Getting Started is <span class="text-primary">Easy</span>
        </h2>
        <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
          <div data-aos="fade-up" data-aos-delay="0" class="flex flex-col items-center text-center p-6">
            <div class="bg-primary text-primary-foreground rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold mb-4">1</div>
            <h3 class="text-xl font-semibold mb-2">Sign Up</h3>
            <p class="text-muted-foreground text-sm">Create your free account in minutes to unlock a world of books.</p>
          </div>
          <div data-aos="fade-up" data-aos-delay="150" class="flex flex-col items-center text-center p-6">
            <div class="bg-primary text-primary-foreground rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold mb-4">2</div>
            <h3 class="text-xl font-semibold mb-2">Explore & Discover</h3>
            <p class="text-muted-foreground text-sm">Browse our vast library, find new authors, and get personalized recommendations.</p>
          </div>
          <div data-aos="fade-up" data-aos-delay="300" class="flex flex-col items-center text-center p-6">
            <div class="bg-primary text-primary-foreground rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold mb-4">3</div>
            <h3 class="text-xl font-semibold mb-2">Read Anywhere</h3>
            <p class="text-muted-foreground text-sm">Enjoy your books on any device with our customizable and friendly reader.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Final Call to Action (CTA) Section -->
    <section id="final-cta" class="py-20 md:py-32 bg-background text-center">
      <div class="container mx-auto px-4">
        <h2 data-aos="zoom-in" class="text-3xl sm:text-4xl md:text-5xl font-bold mb-6 tracking-tight">
          Ready to Dive Into Your Next Adventure?
        </h2>
        <p data-aos="fade-up" data-aos-delay="200" class="text-lg sm:text-xl text-muted-foreground max-w-xl mx-auto mb-10">
          Join MyAppLogo today and start exploring. Your next favorite story awaits!
        </p>
        <div data-aos="fade-up" data-aos-delay="400">
          <NuxtLink to="/auth/register">
            <Button size="lg" class="px-10 py-3 text-lg">
              Sign Up For Free
            </Button>
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
// Icons for sections (IconDownload, IconBookOpen, IconSettings2, IconUsers) are expected to be auto-imported by nuxt-lucide-icons
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

// AOS is initialized in the plugin.
// NuxtLink is auto-imported.
</script>
