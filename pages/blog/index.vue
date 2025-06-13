<template>
  <div class="container mx-auto py-8 px-4">
    <Head>
      <Title>Our Blog - MyAppLogo</Title>
      <Meta name="description" content="Latest articles and insights from the MyAppLogo team on books, reading, and more." />
    </Head>

    <h1 class="text-4xl font-bold mb-8 text-center">
      The MyAppLogo <span class="text-primary">Blog</span>
    </h1>

    <div v-if="pending" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-pulse">
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

    <div v-else-if="error || !posts || posts.length === 0" class="text-center text-muted-foreground py-10">
      <p v-if="error">Could not load blog posts. Error: {{ error.message }}</p>
      <p v-else>No blog posts available at the moment. Please check back later!</p>
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
            <Badge v-if="post.category" variant="secondary" class="mb-2 text-xs">{{ post.category }}</Badge>
            <CardTitle class="text-xl font-semibold group-hover:text-primary transition-colors">{{ post.title }}</CardTitle>
          </CardHeader>
          <CardContent class="flex-grow">
            <p class="text-sm text-muted-foreground line-clamp-3">{{ post.summary }}</p>
          </CardContent>
          <CardFooter class="text-xs text-muted-foreground">
            <span>By {{ post.author }} on {{ formatDate(post.publishedDate) }}</span>
          </CardFooter>
        </Card>
      </NuxtLink>
    </div>

    <!-- Basic Pagination/Load More (Optional - Not implemented in this pass) -->
    <!--
    <div v-if="posts && posts.length > 0 && false" class="mt-12 text-center">
      <Button variant="outline">Load More Posts</Button>
    </div>
    -->
  </div>
</template>

<script setup lang="ts">
import type { BlogPost } from '~/types/models';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
// useHead and useAsyncData are auto-imported by Nuxt

// Fetch blog posts
const { data: posts, pending, error } = await useAsyncData<BlogPost[]>(
  'blog-posts-list', // Unique key for useAsyncData
  () => $fetch('/api/blog/posts'),
  // { lazy: true } // Consider lazy loading for better UX if page is long
);

const formatDate = (dateString: string): string => {
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleDateString(undefined, {
      year: 'numeric', month: 'long', day: 'numeric',
    });
  } catch (e) {
    return 'Invalid Date';
  }
};

// SEO Head (can be set once, or dynamically if needed)
useHead({
  title: 'Our Blog - MyAppLogo',
  meta: [
    { name: 'description', content: 'Latest articles and insights from the MyAppLogo team on books, reading, technology, and more.' },
    { property: 'og:title', content: 'Our Blog - MyAppLogo' },
    { property: 'og:description', content: 'Discover interesting articles about books and reading.' },
    // { property: 'og:image', content: 'your-default-blog-image.jpg' }, // Add a default OG image
  ],
});
</script>

<style scoped>
/* Ensure line-clamp works if not using Tailwind Typography plugin with line-clamp feature */
/* For modern browsers, this should generally work: */
.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}
</style>
