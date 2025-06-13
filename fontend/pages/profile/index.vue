<template>
  <div class="container mx-auto py-8 px-4">
    <Card v-if="user" class="max-w-2xl mx-auto">
      <CardHeader class="flex flex-col items-center sm:flex-row sm:items-start text-center sm:text-left">
        <Avatar class="w-24 h-24 text-6xl mb-4 sm:mb-0 sm:mr-6">
          <AvatarImage v-if="user.profilePictureUrl" :src="user.profilePictureUrl" :alt="user.name" />
          <AvatarFallback>{{ user.name?.charAt(0).toUpperCase() }}</AvatarFallback>
        </Avatar>
        <div class="flex-grow">
          <CardTitle class="text-3xl font-bold">{{ user.name }}</CardTitle>
          <CardDescription class="text-lg">{{ user.email }}</CardDescription>
          <div v-if="user.bio" class="mt-2 text-muted-foreground">
            <p>{{ user.bio }}</p>
          </div>
        </div>
      </CardHeader>
      <CardContent class="mt-6 space-y-6">
        <div>
          <h3 class="text-lg font-semibold mb-2">Reading Stats</h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="bg-muted p-4 rounded-lg text-center">
              <p class="text-2xl font-bold">{{ user.readingHistory?.length || 0 }}</p>
              <p class="text-sm text-muted-foreground">Books Read</p>
            </div>
            <div class="bg-muted p-4 rounded-lg text-center">
              <p class="text-2xl font-bold">{{ favoriteGenre }}</p>
              <p class="text-sm text-muted-foreground">Favorite Genre</p>
            </div>
            <div class="bg-muted p-4 rounded-lg text-center">
              <p class="text-2xl font-bold">{{ booksInProgress }}</p>
              <p class="text-sm text-muted-foreground">In Progress</p>
            </div>
          </div>
        </div>
        <div v-if="user.readingHistory && user.readingHistory.length > 0">
          <h3 class="text-lg font-semibold mb-2">Recent Activity</h3>
          <ul>
            <li v-for="item in user.readingHistory.slice(0, 3)" :key="item.bookId" class="border-b py-2">
              Read book <NuxtLink :to="`/books/${item.bookId}`" class="text-primary hover:underline">{{ item.bookId }}</NuxtLink>
              (Progress: {{ item.progress }}%) on {{ formatDate(item.lastReadDate) }}.
            </li>
          </ul>
        </div>
      </CardContent>
      <CardFooter class="flex flex-col sm:flex-row gap-2 pt-6">
        <Button variant="outline">Edit Profile</Button>
        <Button variant="outline">View Full Reading History</Button>
        <NuxtLink to="/settings">
          <Button variant="outline">App Settings</Button>
        </NuxtLink>
      </CardFooter>
    </Card>
    <div v-else-if="pending" class="max-w-2xl mx-auto space-y-6 animate-pulse">
        <div class="flex flex-col items-center sm:flex-row sm:items-start text-center sm:text-left">
            <div class="w-24 h-24 bg-muted rounded-full mb-4 sm:mb-0 sm:mr-6 shrink-0"></div>
            <div class="flex-grow space-y-3 w-full">
                <div class="h-8 bg-muted rounded w-3/4 sm:w-1/2"></div>
                <div class="h-6 bg-muted rounded w-full sm:w-3/4"></div>
                <div class="h-4 bg-muted rounded w-full sm:w-5/6"></div>
            </div>
        </div>
        <div class="space-y-3 pt-6">
            <div class="h-6 bg-muted rounded w-1/3 mb-3"></div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="bg-muted rounded-lg h-24"></div>
                <div class="bg-muted rounded-lg h-24"></div>
                <div class="bg-muted rounded-lg h-24"></div>
            </div>
        </div>
        <div class="space-y-3 pt-6">
            <div class="h-6 bg-muted rounded w-1/4 mb-3"></div>
            <div class="space-y-2">
                <div class="h-5 bg-muted rounded w-full"></div>
                <div class="h-5 bg-muted rounded w-full"></div>
            </div>
        </div>
    </div>
    <div v-else-if="error" class="text-center text-destructive">
      <p>Could not load profile: {{ error.message }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import type { User } from '~/types/models'; // Import the User interface
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
// Badge might be used if we add genre tags etc.
// import { Badge } from '@/components/ui/badge';
// NuxtLink is auto-imported

const user = ref<User | null>(null);
const pending = ref(true);
const error = ref<Error | null>(null);

// Fetch mock user data (e.g., user1)
// In a real app, you'd get the logged-in user's ID from auth state
const userIdToFetch = 'user1';

onMounted(async () => {
  try {
    // Using $fetch which is Nuxt 3's preferred way for client-side fetching from server routes
    const fetchedUser = await $fetch<User>(`/api/users/${userIdToFetch}`);
    if (fetchedUser) {
      user.value = fetchedUser;
      // If bio is not present in mock but expected in template, provide a default
      if (!user.value.bio) {
        user.value.bio = 'No biography provided.';
      }
    } else {
      // $fetch would throw an error for 404s if the server route handles it correctly (e.g., by throwing createError)
      // If the server route returns undefined or null for not found, this explicit throw is good.
      throw new Error('User not found');
    }
  } catch (e: any) {
    console.error('Failed to fetch user profile:', e);
    // Store the error to display it in the template
    // Make sure it's an Error object for consistent handling
    if (e instanceof Error) {
      error.value = e;
    } else if (typeof e === 'object' && e !== null && 'message' in e) {
      error.value = new Error(String(e.message));
    } else {
      error.value = new Error('An unknown error occurred while fetching the profile.');
    }
  } finally {
    pending.value = false;
  }
});

const favoriteGenre = computed(() => {
  // Mock logic, replace with actual data if available
  // This could be derived from readingHistory if books have genre data associated
  if (user.value && user.value.id === 'user1' && user.value.readingHistory && user.value.readingHistory.length > 0) {
      // Assuming bookId 'book1' is adventure, 'book2' is mystery for mock purposes
      const firstBookId = user.value.readingHistory[0].bookId;
      if (firstBookId === 'book1') return 'Adventure';
      if (firstBookId === 'book2') return 'Mystery';
  }
  return 'N/A';
});

const booksInProgress = computed(() => {
  return user.value?.readingHistory?.filter(item => item.progress < 100).length || 0;
});

const formatDate = (dateString: string | undefined) => {
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
  } catch (e) {
    console.error("Error formatting date:", dateString, e);
    return 'Invalid Date';
  }
};

// Ensure the default layout is used (no definePageMeta needed for layout unless it's not default)
// AppHeader and AppFooter will be shown.
</script>
