<template>
  <div class="flex flex-col h-screen">
    <!-- Reader Header -->
    <header class="bg-background border-b p-4 shadow-sm sticky top-0 z-10">
      <div class="container mx-auto flex justify-between items-center">
        <div class="truncate flex-1 min-w-0 mr-4">
          <NuxtLink :to="`/books/${bookId}`" class="hover:text-primary text-sm text-muted-foreground block truncate">
            &larr; Back to Book Details
          </NuxtLink>
          <h1 v-if="book" class="text-xl font-semibold truncate" :title="book.title">
            {{ book.title }}
          </h1>
          <p v-else-if="!pending && bookId" class="text-xl font-semibold truncate">Book Title Loading...</p>
          <p v-if="currentChapter" class="text-sm text-muted-foreground truncate" :title="currentChapter.title">
            Chapter {{ currentChapter.chapterNumber }}: {{ currentChapter.title }}
          </p>
          <p v-else-if="!pending && chapterIdQuery" class="text-sm text-muted-foreground truncate">Chapter Title Loading...</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <NuxtLink to="/settings">
            <Button variant="outline" size="icon" title="Reading Settings">
              <IconSettings class="h-5 w-5" />
            </Button>
          </NuxtLink>
        </div>
      </div>
    </header>

    <!-- Reader Content -->
    <main class="flex-grow overflow-y-auto" :style="readerStyles">
      <div class="container mx-auto py-6 md:py-10 lg:py-12 px-4">
        <div v-if="pending && !currentChapter" class="text-center py-10">
          <p>Loading chapter...</p>
          <!-- Basic Spinner -->
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary mt-4"></div>
        </div>
        <div v-else-if="error || !currentChapter" class="text-center py-10 text-destructive">
          <p>{{ error ? error.message : 'Chapter content not found.' }}</p>
        </div>
        <article v-else class="prose prose-lg max-w-prose mx-auto">
          <div v-html="formattedContent(currentChapter.content)"></div>
        </article>
      </div>
    </main>

    <!-- Reader Footer (Navigation) -->
    <footer class="bg-background border-t p-4 shadow-sm sticky bottom-0 z-10">
      <div class="container mx-auto flex justify-between items-center">
        <Button variant="outline" @click="goToPreviousChapter" :disabled="!hasPreviousChapter || pending">
          Previous
        </Button>
        <div class="text-sm text-muted-foreground">
          <span v-if="currentChapter && chapters.length > 0">
            Chapter {{ currentChapterIndex + 1 }} of {{ chapters.length }}
          </span>
          <span v-else>Loading...</span>
        </div>
        <Button variant="outline" @click="goToNextChapter" :disabled="!hasNextChapter || pending">
          Next
        </Button>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
// useRoute and useRouter are auto-imported by Nuxt
import type { Book, Chapter } from '~/types/models';
import { Button } from '@/components/ui/button';
// Assuming IconSettings is globally registered by nuxt-lucide-icons or similar
// If not, it would be: import { SettingsIcon as IconSettings } from 'lucide-vue-next';
// or whatever name nuxt-lucide-icons exposes it as. For now, rely on auto-import or global.
import { useReaderSettings } from '~/composables/useReaderSettings';

definePageMeta({
  layout: 'blank',
});

const route = useRoute();
const router = useRouter();

const bookId = computed(() => route.params.bookId as string);
const chapterIdQuery = computed(() => route.query.chapterId as string | undefined);
const typeQuery = computed(() => (route.query.type || 'sample') as 'sample' | 'full');

const book = ref<Book | null>(null);
const chapters = ref<Chapter[]>([]);
const currentChapter = ref<Chapter | null>(null);
const pending = ref(true); // Global pending state for initial load and chapter changes
const error = ref<Error | null>(null);

const { settings: userSettings } = useReaderSettings(); // Renamed to userSettings to match existing code

const readerStyles = computed(() => ({
  fontFamily: userSettings.value.fontFamily,
  fontSize: `${userSettings.value.fontSize}px`,
  lineHeight: userSettings.value.lineHeight,
  // backgroundColor and color for theme are handled by global dark class on <html>
}));

const formattedContent = (content: string | undefined) => {
    if (!content) return '';
    // Basic formatting: replace multiple newlines with paragraph breaks, single newlines with <br>
    // This is a simplified approach. For complex HTML, sanitization (e.g. DOMPurify) is crucial.
    return content
      .split(/\n\s*\n+/) // Split by one or more empty lines
      .map(paragraph => `<p>${paragraph.replace(/\n/g, '<br />')}</p>`)
      .join('');
};

async function fetchBookDetails() {
  if (!bookId.value) return;
  try {
    const fetchedBook = await $fetch<Book>(`/api/books/${bookId.value}`);
    book.value = fetchedBook;
  } catch (e) {
    console.error("Failed to fetch book details for reader:", e);
    // This error is not necessarily fatal for chapter reading if chapters load
  }
}

async function fetchChaptersAndSetCurrent() {
  if (!bookId.value) {
    error.value = new Error('Book ID is missing.');
    pending.value = false;
    return;
  }

  pending.value = true;
  error.value = null;
  currentChapter.value = null; // Clear previous chapter while loading

  try {
    const fetchedChapters = await $fetch<Chapter[]>(`/api/books/${bookId.value}/chapters`, {
      params: { type: typeQuery.value }
    });
    chapters.value = fetchedChapters || [];

    if (chapters.value.length === 0) {
      error.value = new Error(`No ${typeQuery.value} chapters found for this book.`);
      return;
    }

    const targetChapterId = chapterIdQuery.value || chapters.value[0]?.id; // Default to first chapter if no ID
    if (!targetChapterId) {
        error.value = new Error('Chapter ID is missing and no chapters found to default to.');
        return;
    }

    currentChapter.value = chapters.value.find(c => c.id === targetChapterId) || null;

    if (!currentChapter.value) {
      error.value = new Error(`Chapter with ID '${targetChapterId}' not found in ${typeQuery.value} list.`);
      // Optionally, load the first chapter as a fallback if a specific one isn't found
      // currentChapter.value = chapters.value[0] || null;
    }

  } catch (e: any) {
    console.error("Error fetching chapters:", e);
    error.value = e instanceof Error ? e : new Error('Failed to load chapters.');
    chapters.value = []; // Clear chapters on error
  } finally {
    pending.value = false;
  }
}

const currentChapterIndex = computed(() => {
  if (!currentChapter.value || chapters.value.length === 0) return -1;
  return chapters.value.findIndex(c => c.id === currentChapter.value!.id);
});

const hasPreviousChapter = computed(() => currentChapterIndex.value > 0);
const hasNextChapter = computed(() => currentChapterIndex.value !== -1 && currentChapterIndex.value < chapters.value.length - 1);

const navigateToChapterByIndex = (index: number) => {
  const chapter = chapters.value[index];
  if (chapter) {
    // Use router.push to update query params and trigger watcher
    router.push({
      path: `/reader/${bookId.value}`,
      query: { chapterId: chapter.id, type: typeQuery.value }
    });
  }
};

const goToPreviousChapter = () => {
  if (hasPreviousChapter.value) {
    navigateToChapterByIndex(currentChapterIndex.value - 1);
  }
};

const goToNextChapter = () => {
  if (hasNextChapter.value) {
    navigateToChapterByIndex(currentChapterIndex.value + 1);
  }
};

onMounted(async () => {
  await fetchBookDetails(); // Fetch book title etc. (non-blocking for content)
  await fetchChaptersAndSetCurrent(); // Fetch chapters and the target chapter
});

// Watch for route query changes to reload chapter
watch(
  () => [route.query.chapterId, route.query.type],
  async ([newChapterId, newType], [oldChapterId, oldType]) => {
    // Ensure values are strings for comparison, or handle undefined
    const nCid = newChapterId as string | undefined;
    const oCid = oldChapterId as string | undefined;
    const nT = (newType || 'sample') as string;
    const oT = (oldType || 'sample') as string;

    if (nCid !== oCid || nT !== oT) {
      // Update computed refs that depend on route.query if necessary, or just refetch
      // chapterIdQuery and typeQuery are already computed and will update
      await fetchChaptersAndSetCurrent();
    }
  },
  { deep: true } // Use deep watch if query params could be objects, though here they are strings
);
</script>

<style>
/* Basic prose styling for v-html content, Tailwind Typography plugin is preferred */
/* These styles will be applied if @tailwindcss/typography is configured correctly in tailwind.config.js */
/* Example of manual prose styles if not using the plugin: */
/*
.prose p {
  margin-bottom: 1em;
  line-height: 1.7;
}
.prose h1, .prose h2, .prose h3 {
  margin-bottom: 0.5em;
  margin-top: 1.5em;
  font-weight: bold;
}
*/
/* Ensure reader content area takes up space and scrolls */
main[style*="font-family"] { /* Hack to target styled main */
  padding-bottom: 4rem; /* Ensure space for footer */
}
</style>
