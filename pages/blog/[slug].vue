<template>
  <div class="bg-background text-foreground">
    <Head>
      <Title>{{ seoTitle }}</Title>
      <Meta name="description" :content="seoDescription" />
      <Meta property="og:title" :content="seoTitle" />
      <Meta property="og:description" :content="seoDescription" />
      <Meta property="og:image" :content="post?.imageUrl || 'https://source.unsplash.com/random/1200x630/?book,reading'" />
      <Meta property="og:type" content="article" />
      <Meta property="article:published_time" :content="post?.publishedDate" />
      <Meta v-for="tag in post?.tags" :key="tag" property="article:tag" :content="tag" />
      <!-- Add more meta tags as needed, e.g., canonical URL -->
    </Head>

    <div v-if="pendingPost" class="container mx-auto py-8 px-4 animate-pulse">
      <!-- Skeleton Loader for Detail Page -->
      <div class="max-w-3xl mx-auto">
        <div class="h-10 bg-muted rounded w-3/4 mb-4"></div> {/* Title Placeholder */}
        <div class="flex items-center space-x-4 mb-6">
          <div class="h-4 bg-muted rounded w-1/4"></div> {/* Author Placeholder */}
          <div class="h-4 bg-muted rounded w-1/4"></div> {/* Date Placeholder */}
        </div>
        <div class="aspect-[16/9] bg-muted rounded-lg mb-8"></div> {/* Image Placeholder */}
        <div class="space-y-3">
          <div class="h-4 bg-muted rounded w-full"></div>
          <div class="h-4 bg-muted rounded w-full"></div>
          <div class="h-4 bg-muted rounded w-5/6"></div>
          <div class="h-4 bg-muted rounded w-full mt-4"></div>
          <div class="h-4 bg-muted rounded w-3/4"></div>
        </div>
      </div>
    </div>

    <div v-else-if="fetchError || !post" class="container mx-auto py-10 px-4 text-center">
      <p class="text-2xl text-destructive mb-4">
        {{ fetchError && fetchError.statusCode === 404 ? 'Blog Post Not Found' : (fetchError ? 'Error loading post.' : 'Blog post not found.') }}
      </p>
      <p v-if="fetchError && fetchError.statusCode !== 404" class="text-muted-foreground mb-6">{{ fetchError.message }}</p>
      <NuxtLink to="/blog">
        <Button variant="outline">Back to Blog</Button>
      </NuxtLink>
    </div>

    <article v-else class="py-8">
      <header class="container mx-auto px-4 max-w-3xl text-center mb-10">
        <NuxtLink to="/blog" class="text-sm text-primary hover:underline mb-2 inline-block">&larr; Back to Blog</NuxtLink>
        <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">{{ post.title }}</h1>
        <div class="text-sm text-muted-foreground">
          <span>By {{ post.author }}</span> &bull;
          <span>Published on {{ formatDate(post.publishedDate) }}</span>
          <span v-if="post.category"> &bull; In <Badge variant="outline">{{ post.category }}</Badge></span>
        </div>
        <div v-if="post.tags && post.tags.length > 0" class="mt-4">
          <Badge v-for="tag in post.tags" :key="tag" variant="secondary" class="mr-1.5 mb-1.5">{{ tag }}</Badge>
        </div>
      </header>

      <div v-if="post.imageUrl" class="container mx-auto px-4 max-w-4xl mb-10">
        <img
          :src="post.imageUrl"
          :alt="post.title || 'Blog post image'"
          class="w-full h-auto max-h-[500px] object-cover rounded-lg shadow-lg"
        />
      </div>

      <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row lg:gap-8 justify-center">
          <div
            class="prose prose-lg dark:prose-invert max-w-prose lg:max-w-2xl flex-shrink mx-auto lg:mx-0"
            v-html="post.content"
          ></div>

          <aside class="w-full lg:w-64 xl:w-72 flex-shrink-0 mt-10 lg:mt-0 lg:pl-4">
            <div class="sticky top-20 space-y-6">
              <div data-aos="fade-left" class="bg-muted/50 dark:bg-muted/20 p-4 rounded-lg text-center">
                <p class="text-sm text-muted-foreground font-semibold mb-2">Advertisement</p>
                <div class="aspect-video bg-muted dark:bg-muted/30 flex items-center justify-center mt-1 rounded">
                  <span class="text-xs text-muted-foreground">(300x250 Ad)</span>
                </div>
              </div>
              <div data-aos="fade-left" data-aos-delay="100" class="bg-muted/50 dark:bg-muted/20 p-4 rounded-lg">
                 <p class="text-sm text-muted-foreground font-semibold mb-2 text-center">Related Posts</p>
                 <ul class="text-xs text-primary space-y-1">
                    <li><NuxtLink to="#" class="hover:underline">Placeholder: Another Great Article</NuxtLink></li>
                    <li><NuxtLink to="#" class="hover:underline">Placeholder: Tips for Readers</NuxtLink></li>
                    <li><NuxtLink to="#" class="hover:underline">Placeholder: Author Spotlight</NuxtLink></li>
                 </ul>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </article>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
// useRoute, useHead, useAsyncData, showError, $fetch, definePageMeta are auto-imported by Nuxt
import type { BlogPost } from '~/types/models';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
// Card components are used in the skeleton loader, but not directly in the script for the loaded post.
// If you were to use Card for the loaded post itself, you'd import it here.

const route = useRoute();
const slug = route.params.slug as string;

const { data: post, pending: pendingPost, error: fetchError } = await useAsyncData<BlogPost | undefined>(
  `blog-post-${slug}`,
  async () => {
    try {
      const posts = await $fetch<BlogPost[]>('/api/blog/posts');
      const foundPost = posts.find(p => p.slug === slug);
      if (!foundPost) {
        // Use Nuxt's showError to trigger 404 page
        // It's important that showError is called in a way that Nuxt can handle it for proper SSR/client error pages.
        // Throwing an error that Nuxt's error handling can catch is also an option.
        // Using fatal: true will stop client-side navigation and show the error page.
        showError({ statusCode: 404, statusMessage: 'Blog Post Not Found', fatal: true });
        return undefined;
      }
      return foundPost;
    } catch (apiError: any) {
      console.error("API error fetching posts for slug:", slug, apiError);
      let statusCode = 500;
      let statusMessage = 'Error loading blog post.';
      if (apiError.isH3Error) { // Check if it's an H3Error from the API route
          statusCode = apiError.statusCode;
          statusMessage = apiError.statusMessage || apiError.message;
      } else if (apiError.statusCode) { // Check for $fetch error object
          statusCode = apiError.statusCode;
          statusMessage = apiError.data?.message || apiError.statusMessage || apiError.message;
      }
      showError({ statusCode: statusCode, statusMessage: statusMessage, fatal: true });
      return undefined;
    }
  }
);

const formatDate = (dateString: string | undefined): string => {
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleDateString(undefined, {
      year: 'numeric', month: 'long', day: 'numeric',
    });
  } catch(e) {
    return 'Invalid Date';
  }
};

const seoTitle = computed(() => post.value?.metaTitle || post.value?.title || 'Blog Post - MyAppLogo');
const seoDescription = computed(() => post.value?.metaDescription || post.value?.summary?.substring(0,160) || 'Read this interesting blog post from MyAppLogo.');

// useHead is handled by the <Head> component in the template for dynamic values.
// If you needed to set it programmatically for more complex logic, you could do:
// useHead({
//   title: seoTitle, // This will be a ref
//   meta: () => [ // Use a function to make meta tags reactive to post.value changes
//     { name: 'description', content: seoDescription.value },
//     { property: 'og:title', content: seoTitle.value },
//     { property: 'og:description', content: seoDescription.value },
//     { property: 'og:image', content: post.value?.imageUrl || 'https://source.unsplash.com/random/1200x630/?book,reading' },
//     { property: 'og:type', content: 'article' },
//     { property: 'article:published_time', content: post.value?.publishedDate },
//     ...(post.value?.tags?.map(tag => ({ property: 'article:tag', content: tag })) || []),
//   ],
// });

// Ensure the default layout is used, which includes AppHeader and AppFooter
definePageMeta({
  layout: "default",
});
</script>

<style scoped>
/* Styles for this page, if any, beyond Tailwind prose classes */
/* For example, if the ad placeholder needs specific styling */
.lg\:w-64 { width: 16rem; } /* 256px */
.xl\:w-72 { width: 18rem; } /* 288px */

/* Ensure prose styles apply correctly in dark mode if not handled by global typography setup */
.dark .prose-invert {
  /* color: hsl(var(--foreground)); */ /* Usually handled by prose-invert */
}
.dark .prose-invert h1,
.dark .prose-invert h2,
.dark .prose-invert h3,
.dark .prose-invert h4,
.dark .prose-invert strong {
  color: hsl(var(--foreground));
}
.dark .prose-invert p,
.dark .prose-invert ul,
.dark .prose-invert li,
.dark .prose-invert em {
   color: hsl(var(--muted-foreground)); /* Or a slightly lighter gray than main foreground */
}
.dark .prose-invert a {
  color: hsl(var(--primary));
}
.dark .prose-invert a:hover {
  color: hsl(var(--primary) / 0.8);
}

</style>
