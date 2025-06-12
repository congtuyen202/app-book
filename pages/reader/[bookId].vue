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
        <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
          <Button v-if="speechSynthesisSupported && currentChapter && !isSpeaking && !isPaused" @click="startSpeech" variant="outline" size="icon" title="Listen to chapter">
            <IconVolume2 class="h-5 w-5" />
          </Button>
          <Button v-if="speechSynthesisSupported && isSpeaking && !isPaused" @click="pauseSpeech" variant="outline" size="icon" title="Pause speech">
            <IconPauseCircle class="h-5 w-5" />
          </Button>
          <Button v-if="speechSynthesisSupported && isPaused" @click="resumeSpeech" variant="outline" size="icon" title="Resume speech">
            <IconPlayCircle class="h-5 w-5" />
          </Button>
          <Button v-if="speechSynthesisSupported && (isSpeaking || isPaused)" @click="stopSpeech" variant="outline" size="icon" title="Stop speech">
            <IconStopCircle class="h-5 w-5" />
          </Button>
          <NuxtLink to="/settings">
            <Button variant="outline" size="icon" title="Reading Settings">
              <IconSettings class="h-5 w-5" />
            </Button>
          </NuxtLink>
        </div>
      </div>
    </header>

    <!-- Reader Content -->
    <main ref="readerContentArea" class="flex-grow overflow-y-auto" :style="readerStyles">
      <div class="container mx-auto py-6 md:py-10 lg:py-12 px-4">
        <div v-if="pending && !currentChapter" class="space-y-4 py-10 max-w-prose mx-auto animate-pulse">
          <div class="h-6 bg-muted rounded w-3/4"></div>
          <div class="h-4 bg-muted rounded w-full"></div>
          <div class="h-4 bg-muted rounded w-5/6"></div>
          <div class="h-4 bg-muted rounded w-2/3 mt-4"></div>
          <div class="h-4 bg-muted rounded w-full"></div>
          <div class="h-4 bg-muted rounded w-1/2"></div>
        </div>
        <div v-else-if="error || !currentChapter" class="text-center py-10 text-destructive">
          <p>{{ error ? error.message : 'Chapter content not found.' }}</p>
        </div>
        <Transition name="chapter-fade" mode="out-in">
          <article
            v-if="currentChapter && currentChapter.content"
            :key="currentChapter.id"
            class="prose prose-lg max-w-prose mx-auto dark:prose-invert"
            v-html="formattedContent(currentChapter.content)"
          ></article>
          <div v-else-if="!pending && currentChapter && !currentChapter.content"
               class="text-center py-10 text-muted-foreground">
            Chapter content is empty or not available.
          </div>
        </Transition>
      </div>
    </main>

    <!-- Reader Footer (Navigation) -->
    <footer class="bg-background border-t p-4 shadow-sm sticky bottom-0 z-10">
      <div class="container mx-auto flex justify-between items-center">
        <Button variant="outline" @click="goToPreviousChapter" :disabled="!hasPreviousChapter || pending" class="w-28 sm:w-32">
          Previous
        </Button>
        <div class="flex-grow flex justify-center items-center min-w-[150px] sm:min-w-[200px] px-1 sm:px-2">
          <Select v-if="chapters.length > 1" v-model="selectedChapterIdForNavigation" @update:modelValue="navigateToSelectedChapter">
            <SelectTrigger class="h-9 text-sm w-full">
              <SelectValue placeholder="Go to chapter..." />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="(chap, index) in chapters"
                :key="chap.id"
                :value="chap.id"
              >
                {{ index + 1 }}. {{ chap.title }}
              </SelectItem>
            </SelectContent>
          </Select>
           <span v-else-if="currentChapter" class="text-sm text-muted-foreground whitespace-nowrap">
             Chapter {{ currentChapterIndex + 1 }} of {{ chapters.length }}
           </span>
           <span v-else class="text-sm text-muted-foreground">&nbsp;</span>
        </div>
        <Button variant="outline" @click="goToNextChapter" :disabled="!hasNextChapter || pending" class="w-28 sm:w-32">
          Next
        </Button>
      </div>
    </footer>

    <!-- Highlight Toolbar -->
    <div
      v-if="showHighlightToolbar"
      class="absolute z-20 bg-background border border-border shadow-lg rounded-md p-1 flex space-x-1"
      :style="{ top: toolbarPosition.top + 'px', left: toolbarPosition.left + 'px' }"
      @mousedown.prevent
    >
      <Button variant="outline" size="icon" class="w-7 h-7 bg-yellow-200 hover:bg-yellow-300 border-yellow-400 dark:bg-yellow-700 dark:hover:bg-yellow-600 dark:border-yellow-500" @click="applyHighlight('yellow')" title="Highlight Yellow"></Button>
      <Button variant="outline" size="icon" class="w-7 h-7 bg-green-200 hover:bg-green-300 border-green-400 dark:bg-green-700 dark:hover:bg-green-600 dark:border-green-500" @click="applyHighlight('green')" title="Highlight Green"></Button>
      <Button variant="outline" size="icon" class="w-7 h-7 bg-pink-200 hover:bg-pink-300 border-pink-400 dark:bg-pink-700 dark:hover:bg-pink-600 dark:border-pink-500" @click="applyHighlight('pink')" title="Highlight Pink"></Button>
      <Button variant="outline" size="icon" class="w-7 h-7" @click="removeHighlightUnderSelection" title="Remove Highlight"><IconEraser class="h-4 w-4"/></Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch, nextTick, onBeforeUnmount } from 'vue';
import type { Book, Chapter } from '~/types/models';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useReaderSettings } from '~/composables/useReaderSettings';

definePageMeta({
  layout: 'blank',
});

const route = useRoute();
const router = useRouter();

const readerContentArea = ref<HTMLElement | null>(null);
const bookId = computed(() => route.params.bookId as string);
const chapterIdQuery = computed(() => route.query.chapterId as string | undefined);
const typeQuery = computed(() => (route.query.type || 'sample') as 'sample' | 'full');

const book = ref<Book | null>(null);
const chapters = ref<Chapter[]>([]);
const currentChapter = ref<Chapter | null>(null);
const pending = ref(true);
const error = ref<Error | null>(null);

const { settings: userSettings } = useReaderSettings();

const speechSynthesisSupported = ref(false);
const isSpeaking = ref(false);
const isPaused = ref(false);
let utterance: SpeechSynthesisUtterance | null = null;

const showHighlightToolbar = ref(false);
const toolbarPosition = ref({ top: 0, left: 0 });
let currentSelection: Selection | null = null;
let currentRange: Range | null = null;
const highlights = ref<HighlightData[]>([]);

interface HighlightData {
  id: string; bookId: string; chapterId: string; type: 'sample' | 'full';
  startContainerPath: string; startOffset: number;
  endContainerPath: string; endOffset: number;
  text: string; color: string; createdAt: number;
}

const selectedChapterIdForNavigation = ref<string | undefined>(undefined);

const readerStyles = computed(() => ({
  fontFamily: userSettings.value.fontFamily,
  fontSize: `${userSettings.value.fontSize}px`,
  lineHeight: userSettings.value.lineHeight,
}));

const formattedContent = (content: string | undefined) => {
    if (!content) return '';
    return content.split(/\n\s*\n+/).map(p => `<p>${p.replace(/\n/g, '<br />')}</p>`).join('');
};

async function fetchBookDetails() {
  if (!bookId.value) return;
  try {
    book.value = await $fetch<Book>(`/api/books/${bookId.value}`);
  } catch (e) { console.error("Failed to fetch book details:", e); }
}

async function fetchChaptersAndSetCurrent() {
  if (!bookId.value) { error.value = new Error('Book ID is missing.'); pending.value = false; return; }
  pending.value = true; error.value = null; currentChapter.value = null;
  try {
    chapters.value = await $fetch<Chapter[]>(`/api/books/${bookId.value}/chapters`, { params: { type: typeQuery.value } }) || [];
    if (chapters.value.length === 0) { error.value = new Error(`No ${typeQuery.value} chapters found.`); return; }
    const targetChapterId = chapterIdQuery.value || chapters.value[0]?.id;
    if (!targetChapterId) { error.value = new Error('Chapter ID missing.'); return; }
    currentChapter.value = chapters.value.find(c => c.id === targetChapterId) || null;
    if (!currentChapter.value) error.value = new Error(`Chapter ID '${targetChapterId}' not found.`);
  } catch (e: any) {
    console.error("Error fetching chapters:", e);
    error.value = e instanceof Error ? e : new Error('Failed to load chapters.');
    chapters.value = [];
  } finally { pending.value = false; }
}

const currentChapterIndex = computed(() => {
  if (!currentChapter.value || chapters.value.length === 0) return -1;
  return chapters.value.findIndex(c => c.id === currentChapter.value!.id);
});

const hasPreviousChapter = computed(() => currentChapterIndex.value > 0);
const hasNextChapter = computed(() => currentChapterIndex.value !== -1 && currentChapterIndex.value < chapters.value.length - 1);

const navigateToChapter = (chapter: Chapter | undefined) => {
  if (chapter) router.push({ path: `/reader/${bookId.value}`, query: { chapterId: chapter.id, type: typeQuery.value } });
};
const goToPreviousChapter = () => { if (hasPreviousChapter.value) navigateToChapter(chapters.value[currentChapterIndex.value - 1]); };
const goToNextChapter = () => { if (hasNextChapter.value) navigateToChapter(chapters.value[currentChapterIndex.value + 1]); };
const navigateToSelectedChapter = (chapterId: string | undefined) => {
  if (chapterId) navigateToChapter(chapters.value.find(c => c.id === chapterId));
};

watch(currentChapter, (newChap) => { selectedChapterIdForNavigation.value = newChap?.id; }, { immediate: true });

onMounted(async () => {
  await fetchBookDetails();
  await fetchChaptersAndSetCurrent();
  if (currentChapter.value) {
    await restoreScrollPosition();
    await loadHighlights();
  }
  await nextTick();
  if (readerContentArea.value) {
    readerContentArea.value.addEventListener('scroll', handleScroll);
    readerContentArea.value.addEventListener('mouseup', handleTextSelection);
  }
  document.addEventListener('click', globalClickHandler);
  if (typeof window !== 'undefined' && 'speechSynthesis' in window) speechSynthesisSupported.value = true;
});

onBeforeUnmount(() => {
  if (readerContentArea.value) {
    readerContentArea.value.removeEventListener('scroll', handleScroll);
    readerContentArea.value.removeEventListener('mouseup', handleTextSelection);
    const key = getScrollStorageKey();
    if (key && readerContentArea.value) localStorage.setItem(key, readerContentArea.value.scrollTop.toString());
  }
  document.removeEventListener('click', globalClickHandler);
  if (scrollSaveTimeout) clearTimeout(scrollSaveTimeout);
  if (speechSynthesisSupported.value && (speechSynthesis.speaking || speechSynthesis.pending || speechSynthesis.paused)) stopSpeech();
});

watch(() => [route.query.chapterId, route.query.type], async ([newChapterId, newType], [oldChapterId, oldType]) => {
  const nCid = newChapterId as string | undefined; const oCid = oldChapterId as string | undefined;
  const nT = (newType || 'sample') as string; const oT = (oldType || 'sample') as string;
  if (nCid !== oCid || nT !== oT) {
    if (isSpeaking.value || isPaused.value) stopSpeech();
    showHighlightToolbar.value = false;
    await fetchChaptersAndSetCurrent();
    if (currentChapter.value) {
      await restoreScrollPosition();
      await loadHighlights();
    }
  }
}, { deep: true, immediate: false });

const getScrollStorageKey = () => {
  if (!bookId.value || !currentChapter.value?.id || !typeQuery.value) return null;
  return `readerScroll-${bookId.value}-${currentChapter.value.id}-${typeQuery.value}`;
};
let scrollSaveTimeout: ReturnType<typeof setTimeout> | null = null;
const handleScroll = () => {
  if (!readerContentArea.value || pending.value) return;
  if (scrollSaveTimeout) clearTimeout(scrollSaveTimeout);
  scrollSaveTimeout = setTimeout(() => {
    const key = getScrollStorageKey();
    if (key && readerContentArea.value) localStorage.setItem(key, readerContentArea.value.scrollTop.toString());
  }, 250);
};
const restoreScrollPosition = async () => {
  await nextTick(); if (!readerContentArea.value) return;
  const key = getScrollStorageKey();
  if (key) {
    const savedPosition = localStorage.getItem(key);
    readerContentArea.value.scrollTop = savedPosition ? parseInt(savedPosition, 10) : 0;
  } else {
    readerContentArea.value.scrollTop = 0;
  }
};

const startSpeech = () => {
  if (!speechSynthesisSupported.value || !currentChapter.value?.content) return;
  if (speechSynthesis.speaking || speechSynthesis.pending) speechSynthesis.cancel();
  const articleEl = readerContentArea.value?.querySelector('article.prose');
  let text = currentChapter.value.content;
  if (articleEl) text = (articleEl as HTMLElement).innerText || text;
  else text = text.replace(/<br\s*\/?>/gi, '\n').replace(/<\/?[^>]+(>|$)/g, "").replace(/\s+/g, ' ').trim();
  if (!text.trim()) { console.warn("No text to speak."); return; }
  utterance = new SpeechSynthesisUtterance(text);
  utterance.onstart = () => { isSpeaking.value = true; isPaused.value = false; };
  utterance.onend = () => { isSpeaking.value = false; isPaused.value = false; utterance = null; };
  utterance.onerror = (e) => { console.error('Speech error:', e.error); isSpeaking.value = false; isPaused.value = false; utterance = null; };
  speechSynthesis.speak(utterance);
};
const pauseSpeech = () => { if (speechSynthesis.speaking) { speechSynthesis.pause(); isPaused.value = true; } };
const resumeSpeech = () => { if (speechSynthesis.paused) { speechSynthesis.resume(); isPaused.value = false; } };
const stopSpeech = () => {
  if (speechSynthesis.speaking || speechSynthesis.pending || speechSynthesis.paused) speechSynthesis.cancel();
  isSpeaking.value = false; isPaused.value = false; utterance = null;
};

const getPathTo = (node: Node, root: Node): string => {
  if (node === root) return ''; if (!node.parentNode) return '';
  const idx = Array.from(node.parentNode.childNodes).indexOf(node as ChildNode);
  return getPathTo(node.parentNode, root) + `/${node.nodeName}[${idx}]`;
};
const getNodeByPath = (path: string, root: Node): Node | null => {
  try {
    return path.split('/').filter(Boolean).reduce((curr, seg) => {
      if (!curr) return null; const m = seg.match(/([\w-#]+)\[(\d+)\]/); if (!m) throw new Error(`Invalid path: ${seg}`);
      return (curr as Element).childNodes[parseInt(m[2])] || null;
    }, root as Node | null);
  } catch (e) { console.error("Error resolving path:", path, e); return null; }
};
const handleTextSelection = (event: MouseEvent) => {
  if (!readerContentArea.value?.contains(event.target as Node)) return;
  const sel = window.getSelection();
  if (sel && sel.rangeCount > 0 && !sel.isCollapsed) {
    const range = sel.getRangeAt(0);
    const article = readerContentArea.value!.querySelector('article.prose');
    if (!article || !article.contains(range.commonAncestorContainer)) { showHighlightToolbar.value = false; return; }
    currentSelection = sel; currentRange = range;
    const rect = range.getBoundingClientRect(); const readerRect = readerContentArea.value!.getBoundingClientRect();
    toolbarPosition.value = { top: rect.top - readerRect.top + readerContentArea.value!.scrollTop - 40, left: rect.left - readerRect.left + rect.width / 2 - 50 };
    showHighlightToolbar.value = true;
  } else {
    const tb = document.querySelector('.absolute.z-20.bg-background');
    if (tb && event.target && tb.contains(event.target as Node)) return;
    showHighlightToolbar.value = false; currentSelection = null; currentRange = null;
  }
};
const globalClickHandler = (event: MouseEvent) => {
  const tb = document.querySelector('.absolute.z-20.bg-background');
  const sel = window.getSelection();
  if (tb && !tb.contains(event.target as Node) && (!sel || sel.isCollapsed)) showHighlightToolbar.value = false;
};
const applyHighlight = (color: string) => {
  if (!currentRange || !currentSelection || !readerContentArea.value || !currentChapter.value) return;
  const article = readerContentArea.value.querySelector('article.prose'); if (!article) return;
  const id = `hl-${Date.now()}`; const span = document.createElement('span');
  span.className = `highlight-${color}`; span.id = id;
  try {
    span.appendChild(currentRange.extractContents()); currentRange.insertNode(span);
    currentSelection.removeAllRanges();
    saveHighlight({
      id, bookId: bookId.value, chapterId: currentChapter.value.id, type: typeQuery.value,
      startContainerPath: getPathTo(currentRange.startContainer, article), startOffset: currentRange.startOffset,
      endContainerPath: getPathTo(currentRange.endContainer, article), endOffset: currentRange.endOffset,
      text: span.textContent || "", color, createdAt: Date.now()
    });
  } catch (e) { console.error("Error applying highlight:", e); }
  showHighlightToolbar.value = false;
};
const getHighlightsStorageKey = () => `readerHighlights-${bookId.value}`;
const saveHighlight = (hlData: HighlightData) => {
  const key = getHighlightsStorageKey(); let items = JSON.parse(localStorage.getItem(key) || '[]') as HighlightData[];
  items.push(hlData); localStorage.setItem(key, JSON.stringify(items)); highlights.value.push(hlData);
};
const loadHighlights = async () => {
  await nextTick(); const article = readerContentArea.value?.querySelector('article.prose');
  if (!article || !currentChapter.value) return;
  const key = getHighlightsStorageKey(); const items = JSON.parse(localStorage.getItem(key) || '[]') as HighlightData[];
  const chapHls = items.filter(h => h.chapterId === currentChapter.value!.id && h.type === typeQuery.value);
  highlights.value = chapHls; chapHls.sort((a,b) => a.createdAt - b.createdAt);
  for (const hl of chapHls) {
    const startN = getNodeByPath(hl.startContainerPath, article); const endN = getNodeByPath(hl.endContainerPath, article);
    if (startN && endN) {
      try {
        const r = document.createRange();
        if (hl.startOffset > (startN.textContent?.length || 0) || hl.endOffset > (endN.textContent?.length || 0)) { console.warn("Invalid offsets", hl.id); continue; }
        r.setStart(startN, hl.startOffset); r.setEnd(endN, hl.endOffset);
        if (document.getElementById(hl.id)) continue;
        const s = document.createElement('span'); s.className = `highlight-${hl.color}`; s.id = hl.id;
        if (r.toString().trim() === "" && hl.text.trim() !== "") { console.warn("Empty range", hl.id); continue; }
        s.appendChild(r.extractContents()); r.insertNode(s);
      } catch (e) { console.warn("Can't re-apply highlight:", hl.id, e); }
    } else { console.warn("Node not found for highlight:", hl.id); }
  }
};
const removeHighlightUnderSelection = () => {
  if (!currentRange || !readerContentArea.value) return; let n = currentRange.commonAncestorContainer; let removed = false;
  while (n && n !== readerContentArea.value) {
    if (n.nodeType === Node.ELEMENT_NODE && (n as HTMLElement).id?.startsWith('hl-')) {
      const id = (n as HTMLElement).id; const p = n.parentNode; if (!p) break;
      while (n.firstChild) p.insertBefore(n.firstChild, n);
      p.removeChild(n);
      const key = getHighlightsStorageKey(); let items = JSON.parse(localStorage.getItem(key) || '[]') as HighlightData[];
      items = items.filter(h => h.id !== id); localStorage.setItem(key, JSON.stringify(items));
      highlights.value = highlights.value.filter(h => h.id !== id); removed = true; break;
    }
    n = n.parentNode!;
  }
  if (!removed) console.warn("No highlight to remove.");
  showHighlightToolbar.value = false; currentSelection?.removeAllRanges();
};

</script>

<style>
.highlight-yellow { background-color: rgba(250, 204, 21, 0.4); }
.highlight-green { background-color: rgba(74, 222, 128, 0.4); }
.highlight-pink { background-color: rgba(244, 114, 182, 0.4); }
.dark .highlight-yellow { background-color: rgba(250, 204, 21, 0.3); }
.dark .highlight-green { background-color: rgba(74, 222, 128, 0.3); }
.dark .highlight-pink { background-color: rgba(244, 114, 182, 0.3); }

.chapter-fade-enter-active,
.chapter-fade-leave-active {
  transition: opacity 0.2s ease-in-out;
}
.chapter-fade-enter-from,
.chapter-fade-leave-to {
  opacity: 0;
}

main[style*="font-family"] {
  padding-bottom: 4rem;
}
</style>
