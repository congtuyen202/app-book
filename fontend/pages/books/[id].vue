<template>
  <div class="container mx-auto py-8 px-4">
    <div v-if="pending" class="grid md:grid-cols-3 gap-8 lg:gap-12 animate-pulse">
      <div class="md:col-span-1 space-y-6">
        <div class="bg-muted rounded-lg w-full aspect-[2/3]"></div>
        <div class="h-12 bg-muted rounded w-full"></div>
        <div class="h-12 bg-muted rounded w-full"></div>
      </div>
      <div class="md:col-span-2 space-y-4">
        <div class="h-10 bg-muted rounded w-3/4"></div>
        <div class="h-6 bg-muted rounded w-1/2"></div>
        <div class="h-4 bg-muted rounded w-1/3 mb-4"></div>
        <div class="space-y-2">
          <div class="h-4 bg-muted rounded"></div>
          <div class="h-4 bg-muted rounded"></div>
          <div class="h-4 bg-muted rounded w-5/6"></div>
        </div>
        <div class="h-6 bg-muted rounded w-1/4 mt-6"></div>
        <div class="space-y-2 mt-2">
          <div class="h-4 bg-muted rounded w-3/4"></div>
          <div class="h-4 bg-muted rounded w-3/4"></div>
          <div class="h-4 bg-muted rounded w-2/3"></div>
        </div>
      </div>
    </div>
    <div v-else-if="error || !book" class="text-center text-destructive">
      <p>{{ error ? error.message : 'Book not found.' }}</p>
      <NuxtLink to="/books" class="mt-4 inline-block">
        <Button variant="outline">Back to Books</Button>
      </NuxtLink>
    </div>
    <div v-else class="grid md:grid-cols-3 gap-8 lg:gap-12">
      <!-- Left Column: Cover Image & Actions -->
      <div class="md:col-span-1 space-y-6">
        <img
          :src="book.coverImageUrl || 'https://source.unsplash.com/random/400x600/?book,placeholder&id='+book.id"
          :alt="book.title"
          class="rounded-lg shadow-lg w-full object-cover aspect-[2/3]"
        />
        <div class="space-y-2">
          <Button v-if="book.price" class="w-full" size="lg" @click="handleAddToCart">
            Add to Cart ({{ formatPrice(book.price) }})
          </Button>
          <Button class="w-full" variant="secondary" @click="handleReadSample" :disabled="!book.sampleChapters || book.sampleChapters.length === 0">
            Read Sample
          </Button>
        </div>
         <NuxtLink to="/books" class="inline-block w-full">
            <Button variant="outline" class="w-full">Back to All Books</Button>
          </NuxtLink>
      </div>

      <!-- Right Column: Book Details -->
      <div class="md:col-span-2">
        <h1 class="text-3xl lg:text-4xl font-bold mb-2">{{ book.title }}</h1>
        <p class="text-xl text-muted-foreground mb-1">by {{ book.author }}</p>
        <div class="flex items-center mb-4">
          <span v-if="book.averageRating" class="text-yellow-500 mr-2 flex items-center">
            <template v-for="i in 5" :key="`star-${i}`">
              <span v-if="i <= Math.round(book.averageRating || 0)" class="text-lg">★</span>
              <span v-else class="text-lg text-gray-300">☆</span>
            </template>
            <span class="ml-1">({{ book.averageRating.toFixed(1) }})</span>
          </span>
          <span v-if="book.reviews && book.reviews.length > 0" class="text-sm text-muted-foreground">
            {{ book.reviews.length }} review{{ book.reviews.length === 1 ? '' : 's' }}
          </span>
        </div>

        <p class="text-base lg:text-lg mb-6 leading-relaxed">{{ book.description }}</p>

        <div class="space-y-3 mb-6">
          <h3 class="text-xl font-semibold border-b pb-2 mb-3">Book Details</h3>
          <p><strong>Publisher:</strong> {{ book.publisher }}</p>
          <p><strong>Publication Date:</strong> {{ formatDate(book.publicationDate) }}</p>
          <p><strong>ISBN:</strong> {{ book.isbn }}</p>
          <div v-if="book.genre && book.genre.length > 0" class="flex items-center flex-wrap gap-2">
            <strong>Genres:</strong>
            <Badge v-for="g in book.genre" :key="g" variant="secondary">{{ g }}</Badge>
          </div>
        </div>

        <div v-if="book.sampleChapters && book.sampleChapters.length > 0" class="mb-6">
          <h3 class="text-xl font-semibold border-b pb-2 mb-3">Sample Chapters</h3>
          <ul class="space-y-1">
            <li v-for="chapter in book.sampleChapters" :key="chapter.id">
              <NuxtLink :to="`/reader/${book?.id}?chapterId=${chapter.id}&type=sample`" class="text-primary hover:underline">
                {{ chapter.chapterNumber }}. {{ chapter.title }}
              </NuxtLink>
            </li>
          </ul>
        </div>

        <div v-if="book.reviews && book.reviews.length > 0">
            <h3 class="text-xl font-semibold border-b pb-2 mb-3">Reviews</h3>
            <div class="space-y-4">
                <Card v-for="review in book.reviews.slice(0,3)" :key="review.userId + review.date">
                    <CardHeader class="pb-2 pt-3">
                         <div class="flex items-center">
                            <span class="text-yellow-500 mr-1 flex items-center">
                                <template v-for="i in 5" :key="`review-star-${i}`">
                                  <span v-if="i <= review.rating" class="text-sm">★</span>
                                  <span v-else class="text-sm text-gray-300">☆</span>
                                </template>
                            </span>
                            <span class="font-semibold ml-1">{{ review.rating.toFixed(1) }}</span>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            By User {{ review.userId }} on {{ formatDate(review.date) }}
                        </p>
                    </CardHeader>
                    <CardContent class="pb-3">
                        <p>{{ review.comment }}</p>
                    </CardContent>
                </Card>
                <Button v-if="book.reviews.length > 3" variant="link" class="px-0">View all {{book.reviews.length}} reviews</Button>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
// useRoute is auto-imported in Nuxt 3
import type { Book } from '~/types/models';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card'; // CardTitle not used directly
import { Badge } from '@/components/ui/badge';
// NuxtLink and navigateTo are auto-imported/globally available

const route = useRoute();
const book = ref<Book | null>(null);
const pending = ref(true);
const error = ref<Error | null>(null);

// Ensure bookId is a string, or null if not present/valid
const bookId = Array.isArray(route.params.id) ? route.params.id[0] : route.params.id;

onMounted(async () => {
  if (!bookId) {
    // This case should ideally be handled by Nuxt routing if the param is required
    error.value = new Error('Book ID is missing or invalid.');
    pending.value = false;
    return;
  }
  try {
    const fetchedBook = await $fetch<Book>(`/api/books/${bookId}`);
    if (fetchedBook && Object.keys(fetchedBook).length > 0) { // Check if object is not empty
      book.value = fetchedBook;
    } else {
      // API might return empty object or null/undefined for not found (if not throwing 404)
      error.value = new Error('Book not found.');
      // Set HTTP status code for Nuxt to render error page, if desired
      // It's better if the API route itself throws createError({ statusCode: 404 })
      // For now, we just set error.value for local handling.
    }
  } catch (e: any) {
    console.error("Fetch error:", e);
    if (e.statusCode === 404 || e.message.includes('404')) { // Check for 404 status from $fetch error
        error.value = new Error('Book not found (404).');
    } else {
        error.value = new Error(e.message || 'An unexpected error occurred.');
    }
  } finally {
    pending.value = false;
  }
});

const formatPrice = (price: number | undefined): string => {
  if (price === undefined || price === null) return 'N/A';
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const formatDate = (dateString: string | undefined): string => {
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
  } catch (e) {
    return 'Invalid Date';
  }
};

const handleAddToCart = () => {
  if (!book.value) return;
  alert(`Added "${book.value.title}" to cart.`);
  console.log('Add to cart:', book.value.id);
};

const handleReadSample = () => {
  if (!book.value || !book.value.sampleChapters || book.value.sampleChapters.length === 0) {
    alert("No sample chapters available for this book.");
    return;
  }
  const firstSampleChapter = book.value.sampleChapters[0];
  // Ensure book.value.id is valid before navigating
  if (book.value.id && firstSampleChapter.id) {
    navigateTo(`/reader/${book.value.id}?chapterId=${firstSampleChapter.id}&type=sample`);
  } else {
    alert("Error: Missing book or chapter ID for navigation.");
  }
};
</script>

<style scoped>
.aspect-\[2\/3\] {
  aspect-ratio: 2 / 3;
}
</style>
