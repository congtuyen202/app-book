<template>
  <div class="container mx-auto py-8 px-4">
    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-4">Explore Our Books</h1>
      <div class="flex gap-2">
        <Input
          v-model="searchQuery"
          type="text"
          placeholder="Search by title or author..."
          class="max-w-sm"
          @keyup.enter="fetchBooks"
        />
        <Button @click="fetchBooks">Search</Button>
      </div>
    </div>

    <div v-if="pending" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <Card v-for="n in 8" :key="n" class="animate-pulse">
        <div class="h-64 bg-muted rounded-t-lg"></div>
        <CardContent class="pt-4 space-y-2">
          <div class="h-5 bg-muted rounded w-3/4"></div>
          <div class="h-4 bg-muted rounded w-1/2"></div>
          <div class="h-4 bg-muted rounded w-1/3"></div>
        </CardContent>
        <CardFooter class="mt-2">
          <div class="h-10 bg-muted rounded w-full"></div>
        </CardFooter>
      </Card>
    </div>
    <div v-else-if="error" class="text-center text-destructive">
      <p>Could not load books: {{ error.message || 'Unknown error' }}</p>
    </div>
    <div v-else-if="books && books.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <Card v-for="book in books" :key="book.id">
        <NuxtLink :to="`/books/${book.id}`" class="block hover:no-underline">
          <CardHeader class="p-0">
            <img
              :src="book.coverImageUrl || 'https://source.unsplash.com/random/300x450/?book,placeholder&id='+book.id"
              :alt="book.title"
              class="rounded-t-lg object-cover h-64 w-full"
            />
          </CardHeader>
          <CardContent class="pt-4">
            <CardTitle class="text-lg font-semibold hover:text-primary truncate h-[2.5em]" :title="book.title">
              {{ book.title }}
            </CardTitle>
            <p class="text-sm text-muted-foreground mt-1">{{ book.author }}</p>
            <p v-if="book.price" class="text-md font-semibold mt-2">
              {{ formatPrice(book.price) }}
            </p>
            <div v-if="book.genre && book.genre.length > 0" class="mt-2 flex flex-wrap gap-1 h-[2.2em] overflow-hidden">
              <Badge v-for="g in book.genre.slice(0, 2)" :key="g" variant="outline">{{ g }}</Badge>
            </div>
          </CardContent>
        </NuxtLink>
        <CardFooter class="pt-3"> <!-- Adjusted padding top -->
           <Button class="w-full" variant="outline" @click="viewBook(book.id)">View Details</Button>
        </CardFooter>
      </Card>
    </div>
    <div v-else class="text-center text-muted-foreground">
      <p>No books found matching your criteria.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import type { Book } from '~/types/models';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
// NuxtLink and navigateTo are auto-imported/globally available in Nuxt 3

const books = ref<Book[]>([]);
const searchQuery = ref('');
const pending = ref(true);
const error = ref<Error | null>(null);

const fetchBooks = async () => {
  pending.value = true;
  error.value = null;
  try {
    const params: Record<string, string> = {};
    if (searchQuery.value.trim()) {
      params.search = searchQuery.value.trim();
    }

    const fetchedBooks = await $fetch<Book[]>('/api/books', { params });
    books.value = fetchedBooks;
  } catch (e: any) {
    console.error('Failed to fetch books:', e);
    if (e instanceof Error) {
      error.value = e;
    } else if (typeof e === 'object' && e !== null && 'message' in e) {
      error.value = new Error(String(e.message));
    } else {
      error.value = new Error('An unknown error occurred while fetching books.');
    }
    books.value = [];
  } finally {
    pending.value = false;
  }
};

const formatPrice = (price: number | undefined): string => {
  if (price === undefined || price === null) return '';
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const viewBook = (bookId: string) => {
  navigateTo(`/books/${bookId}`);
};

onMounted(fetchBooks);

// Optional: Watch searchQuery for live search (debounced would be better in real app)
// import { watchDebounced } from '@vueuse/core'; // Example if using VueUse
// watchDebounced(searchQuery, () => fetchBooks(), { debounce: 500, maxWait: 1000 });
</script>

<style scoped>
/* Ensure consistent card height if needed, or use flex properties for alignment */
/* Adding a min-height to card content or title can help with visual consistency if text lengths vary wildly */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: normal; /* Allow wrapping for a couple of lines */
  display: -webkit-box;
  -webkit-line-clamp: 2; /* Limit to 2 lines */
  -webkit-box-orient: vertical;
}
</style>
