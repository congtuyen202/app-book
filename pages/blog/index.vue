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
      <Card v-for="n in 3" :key="`skel-${n}`">
        <div class="aspect-[16/9] bg-muted rounded-t-lg"></div>
        <CardHeader>
          <div class="h-4 bg-muted rounded w-1/4 mb-2"></div>
          <CardTitle class="h-6 bg-muted rounded w-3/4"></CardTitle>
        </CardHeader>
        <CardContent>
          <div class="h-4 bg-muted rounded w-full mb-1"></div>
          <div class="h-4 bg-muted rounded w-full mb-1"></div>
          <div class="h-4 bg-muted rounded w-2/3"></div>
        </CardContent>
        <CardFooter>
          <div class="h-4 bg-muted rounded w-1/3"></div>
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
            <NuxtLink v-if="post.category" :to="`/blog/category/${post.category.slug}`" class="inline-block mb-2">
              <Badge variant="secondary" class="text-xs">{{ post.category.name }}</Badge>
            </NuxtLink>
            <CardTitle class="text-xl font-semibold group-hover:text-primary transition-colors">{{ post.title }}</CardTitle>
          </CardHeader>
          <CardContent class="flex-grow">
            <p class="text-sm text-muted-foreground line-clamp-3">{{ post.summary }}</p>
            <div v-if="post.tags && post.tags.length > 0" class="mt-3 flex flex-wrap gap-1">
              <NuxtLink v-for="tag in post.tags.slice(0, 3)" :key="tag.id" :to="`/blog/tag/${tag.slug}`">
                <Badge variant="outline" size="sm">#{{ tag.name }}</Badge>
              </NuxtLink>
            </div>
          </CardContent>
          <CardFooter class="text-xs text-muted-foreground">
            <span>By {{ post.author?.name || 'Unknown Author' }} on {{ formatDate(post.publishedDate) }}</span>
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
import { ref, onMounted, computed } from 'vue';
import type { BlogPost } from '~/types/models'; // BlogPost interface already expects relational structure
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { useNuxtApp } from '#app';

const { $axios } = useNuxtApp();

const listPostsQuery = `
  query GetBookaiBlogs($first: Int!, $page: Int) {
    bookaiBlogs(first: $first, page: $page) {
      data {
        id
        slug
        title
        summary
        imageUrl
        author { # Changed from author_name to fetch object
          id
          name
        }
        publishedDate # Changed from published_date
        category { # Changed from category_name to fetch object
          id
          name
          slug
        }
        tags { # New: array of tag objects
          id
          name
          slug
        }
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
const paginatorInfo = ref<any>(null); // Define a proper interface for PaginatorInfo later
const allPostsLoaded = ref(false);

const pending = ref(false);
const error = ref<Error | null>(null);

async function fetchBlogPosts(pageToFetch: number = 1) {
  if (pageToFetch === 1) {
    posts.value = [];
    allPostsLoaded.value = false;
  }
  pending.value = true;
  error.value = null;
  try {
    const response: any = await $axios.post(
      'http://localhost:8000/graphql',
      {
        query: listPostsQuery,
        variables: {
          first: 6,
          page: pageToFetch,
        },
      }
    );

    if (response.data.errors) {
      throw new Error(response.data.errors.map((e: any) => e.message).join('\n'));
    }

    const result = response.data?.data?.bookaiBlogs;
    if (result && result.data) {
      if (pageToFetch === 1) {
        posts.value = result.data;
      } else {
        posts.value.push(...result.data);
      }
      paginatorInfo.value = result.paginatorInfo;
      if (!result.paginatorInfo?.hasMorePages) {
        allPostsLoaded.value = true;
      }
      currentPage.value = pageToFetch;
    } else {
      if (pageToFetch === 1) posts.value = [];
      allPostsLoaded.value = true;
    }
  } catch (e: any) {
    console.error('Failed to fetch blog posts:', e);
    error.value = e;
    if (pageToFetch === 1) posts.value = [];
  } finally {
    pending.value = false;
  }
}

onMounted(() => {
  fetchBlogPosts(1);
});

const loadMorePosts = () => {
  if (paginatorInfo.value?.hasMorePages && !pending.value) {
    fetchBlogPosts(currentPage.value + 1);
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
