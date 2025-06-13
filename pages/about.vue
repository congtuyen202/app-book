<template>
  <div class="container mx-auto py-8 px-4">
    <Card class="max-w-2xl mx-auto">
      <CardHeader>
        <CardTitle class="text-3xl font-bold">About Us</CardTitle>
      </CardHeader>
      <CardContent>
        <p class="mt-4 text-lg text-muted-foreground">
          Welcome to MyApp! This is the about page.
          We are passionate about bringing you the best reading experience.
        </p>
        <p class="mt-2 text-lg text-muted-foreground">
          More details about our mission and team will be added here soon.
        </p>
      </CardContent>
      <CardFooter>
        <NuxtLink to="/" class="mt-6 inline-block">
          <Button variant="outline">Go back to Home</Button>
        </NuxtLink>
      </CardFooter>
    </Card>
  </div>
</template>

<script setup lang="ts">
// Assuming Button, Card, CardHeader, CardContent, CardFooter, CardTitle
// are auto-imported by Nuxt/Shadcn or globally registered.
// If not, they would need explicit imports:
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
</script>
