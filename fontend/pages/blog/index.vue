<template>
  <div class="container mx-auto py-8 px-4">
    <Head>
      <Title>Our Blog - MyAppLogo</Title>
      <Meta name="description" content="Latest articles and insights from the MyAppLogo team on books, reading, and more." />
    </Head>

    <h1 class="text-4xl font-bold mb-8 text-center">
      The MyAppLogo <span class="text-primary">Blog</span>
    </h1>

    <div v-if="pending && posts.length === 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-pulse">
      <!-- Skeleton Loader Cards -->
      <Card v-for="n in 3" :key="`skel-${n}`">
        <div class="aspect-[16/9] bg-muted rounded-t-lg"></div>
        <CardHeader>
          <div class="h-4 bg-muted rounded w-1/4 mb-2"></div> {/* Category Badge Placeholder */}
          <CardTitle class="h-6 bg-muted rounded w-3/4"></CardTitle> {/* Title Placeholder */}
        </CardHeader>
        <CardContent>
          <div class="h-4 bg-muted rounded w-full mb-1"></div> {/* Summary line 1 */}
          <div class="h-4 bg-muted rounded w-full mb-1"></div> {/* Summary line 2 */}
          <div class="h-4 bg-muted rounded w-2/3"></div>   {/* Summary line 3 */}
        </CardContent>
        <CardFooter>
          <div class="h-4 bg-muted rounded w-1/3"></div> {/* Author/Date Placeholder */}
        </CardFooter>
      </Card>
    </div>

    <div v-else-if="error && posts.length === 0" class="text-center text-muted-foreground py-10">
      <p>Could not load blog posts. Error: {{ error.message }}</p>
      <NuxtLink to="/" class="mt-4 inline-block">
        <Button variant="outline">Back to Home</Button>
      </NuxtLink>
    </div>

    <div v-else-if="posts.length === 0 && !pending" class="text-center text-muted-foreground py-10">
        <p>No blog posts available at the moment. Please check back later!</p>
         <NuxtLink to="/" class="mt-4 inline-block">
            <Button variant="outline">Back to Home</Button>
          </NuxtLink>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <NuxtLink v-for="post in posts" :key="post.id" :to="`/blog/${post.slug}`" class="block group">
        <Card class="h-full flex flex-col overflow-hidden transition-shadow duration-300 ease-in-out group-hover:shadow-xl dark:border-border">
          <div class="aspect-[16/9] bg-muted overflow-hidden">
            <img
              :src="post.imageUrl || 'https://source.unsplash.com/random/800x450/?book,technology,reading&sig='+post.id"
              :alt="post.title || 'Blog post image'"
              class="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105"
            />
          </div>
          <CardHeader>
            <Badge v-if="post.category_name" variant="secondary" class="mb-2 text-xs">{{ post.category_name }}</Badge>
            <CardTitle class="text-xl font-semibold group-hover:text-primary transition-colors">{{ post.title }}</CardTitle>
          </CardHeader>
          <CardContent class="flex-grow">
            <p class="text-sm text-muted-foreground line-clamp-3">{{ post.summary }}</p>
          </CardContent>
          <CardFooter class="text-xs text-muted-foreground">
            <span>By {{ post.author_name }} on {{ formatDate(post.published_date) }}</span>
          </CardFooter>
        </Card>
      </NuxtLink>
    </div>

    <div v-if="posts && posts.length > 0 && paginatorInfo && paginatorInfo.hasMorePages && !allPostsLoaded" class="mt-12 text-center">
      <Button variant="outline" @click="loadMorePosts" :disabled="pending">
        {{ pending ? 'Loading...' : 'Load More Posts' }}
      </Button>
    </div>
    <div v-if="allPostsLoaded && posts && posts.length > 0" class="mt-12 text-center text-muted-foreground">
      <p>All posts loaded.</p>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { gql, useQuery } from '#imports';
import type { BlogPost, BookaiBlogsData } from '~/types/models'; // Updated import
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

const GET_BLOG_POSTS = gql`
  query GetBookaiBlogs($first: Int!, $page: Int) {
    bookaiBlogs(first: $first, page: $page) {
      data {
        id
        slug
        title
        summary
        imageUrl
        author_name
        published_date
        category_name
        tags
      }
      paginatorInfo {
        currentPage
        lastPage
        total
        count
        hasMorePages
      }
    }
  }
`;

const currentPage = ref(1);
const posts = ref<BlogPost[]>([]);
const paginatorInfo = ref<any>(null);
const allPostsLoaded = ref(false);

// Reactive variables for useQuery
const queryVariables = computed(() => ({
  first: 6,
  page: currentPage.value,
}));

// Use useQuery with reactive variables and type annotation
const { result, loading: pending, error, fetchMore } = useQuery<BookaiBlogsData>(GET_BLOG_POSTS, queryVariables);

// Watch for changes in the result and update posts and paginatorInfo
watch(result, (newResult) => {
  if (newResult?.bookaiBlogs?.data) { // Check for data array
    const newBlogPosts = newResult.bookaiBlogs.data;
    const newPaginatorInfo = newResult.bookaiBlogs.paginatorInfo;

    if (currentPage.value === 1 || !paginatorInfo.value) {
      posts.value = [...newBlogPosts];
    } else {
      // Append new items, preventing duplicates if result contains all items so far
      const existingPostIds = new Set(posts.value.map(p => p.id));
      const uniqueNewItems = newBlogPosts.filter(p => !existingPostIds.has(p.id));
      posts.value.push(...uniqueNewItems);
    }

    if (newPaginatorInfo) {
      paginatorInfo.value = newPaginatorInfo;
      allPostsLoaded.value = !newPaginatorInfo.hasMorePages;
    }

  } else if (error.value) {
    posts.value = [];
    allPostsLoaded.value = true;
  } else if (!pending.value && !newResult && currentPage.value === 1) {
    posts.value = [];
    allPostsLoaded.value = true;
  }
}, { immediate: true, deep: true });


const loadMorePosts = async () => {
  if (paginatorInfo.value?.hasMorePages && !pending.value) {
    const nextPage = currentPage.value + 1;
    // Try to use fetchMore if available and correctly typed by #imports
    if (typeof fetchMore === 'function') {
      try {
        await fetchMore({
          variables: {
            page: nextPage,
          },
          updateQuery: (previousResult: any, { fetchMoreResult }: any) => {
            if (!fetchMoreResult?.bookaiBlogs) return previousResult;
            // Ensure previousResult.bookaiBlogs.data is an array
            const prevData = previousResult.bookaiBlogs?.data || [];
            return {
              ...previousResult,
              bookaiBlogs: {
                ...previousResult.bookaiBlogs,
                data: [
                  ...prevData,
                  ...fetchMoreResult.bookaiBlogs.data,
                ],
                paginatorInfo: fetchMoreResult.bookaiBlogs.paginatorInfo,
              },
            };
          },
        });
        currentPage.value = nextPage; // Update current page after successful fetchMore
      } catch (e) {
        console.error('Error using fetchMore:', e);
        // error ref from useQuery should capture this
      }
    } else {
      // Fallback if fetchMore is not available or not working as expected:
      // simply increment currentPage. The watch effect on `result` (due to reactive queryVariables)
      // will handle updating the posts. The watch logic needs to be robust for this.
      currentPage.value = nextPage;
    }
  }
};

const formatDate = (dateString: string | undefined): string => {
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleDateString(undefined, {
      year: 'numeric', month: 'long', day: 'numeric',
    });
  } catch (e) {
    return 'Invalid Date';
  }
};

useHead({
  title: 'Our Blog - MyAppLogo',
  meta: [
    { name: 'description', content: 'Latest articles and insights from the MyAppLogo team on books, reading, technology, and more.' },
    { property: 'og:title', content: 'Our Blog - MyAppLogo' },
    { property: 'og:description', content: 'Discover interesting articles about books and reading.' },
  ],
});
</script>

<style scoped>
.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}
</style>
