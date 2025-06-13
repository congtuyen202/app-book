<template>
  <div class="bg-background text-foreground">
    <Head>
      <Title>{{ seoTitle }}</Title>
      <Meta name="description" :content="seoDescription" />
      <Meta property="og:title" :content="seoTitle" />
      <Meta property="og:description" :content="seoDescription" />
      <Meta property="og:image" :content="post?.imageUrl || 'https://source.unsplash.com/random/1200x630/?book,reading'" />
      <Meta property="og:type" content="article" />
      <Meta property="article:published_time" :content="post?.published_date" /> {/* Changed to published_date */}
      <Meta v-for="tag in post?.tags" :key="tag" property="article:tag" :content="tag" />
    </Head>

    <div v-if="pendingPost" class="container mx-auto py-8 px-4 animate-pulse">
      <div class="max-w-3xl mx-auto">
        <div class="h-10 bg-muted rounded w-3/4 mb-4"></div>
        <div class="flex items-center space-x-4 mb-6">
          <div class="h-4 bg-muted rounded w-1/4"></div>
          <div class="h-4 bg-muted rounded w-1/4"></div>
        </div>
        <div class="aspect-[16/9] bg-muted rounded-lg mb-8"></div>
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
          <span>By {{ post.author_name }}</span> &bull; {/* Changed to author_name */}
          <span>Published on {{ formatDate(post.published_date) }}</span> {/* Changed to published_date */}
          <span v-if="post.category_name"> &bull; In <Badge variant="outline">{{ post.category_name }}</Badge></span> {/* Changed to category_name */}
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
import { useRoute } from 'vue-router';
import type { BlogPost } from '~/types/models';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useNuxtApp } from '#app'; // For $axios

const { $axios } = useNuxtApp();
const route = useRoute();
const slug = route.params.slug as string;

const getPostBySlugQuery = `
  query GetBookaiBlogBySlug($slug: String!) {
    bookaiBlogBySlug(slug: $slug) {
      id
      slug
      title
      summary
      content
      imageUrl
      author_name
      published_date
      category_name
      tags
      metaTitle
      metaDescription
    }
  }
`;

const { data: post, pending: pendingPost, error: fetchError } = await useAsyncData<BlogPost | undefined>(
  `blog-post-${slug}`,
  async () => {
    try {
      const response: any = await $axios.post('http://localhost:8000/graphql', { // Ensure this URL is correct
        query: getPostBySlugQuery,
        variables: { slug: slug },
      });

      if (response.data.errors) {
        const errorMessage = response.data.errors.map((e: any) => e.message).join('\n');
        // Check if GraphQL itself indicates "not found" via a specific error code or message pattern if any
        // For now, we assume if data.bookaiBlogBySlug is null, it's a 404.
        if (response.data.data?.bookaiBlogBySlug === null) {
            showError({ statusCode: 404, statusMessage: 'Blog Post Not Found', fatal: true });
            return undefined;
        }
        throw new Error(errorMessage);
      }

      const fetchedPost = response.data?.data?.bookaiBlogBySlug;
      if (!fetchedPost) {
        showError({ statusCode: 404, statusMessage: 'Blog Post Not Found', fatal: true });
        return undefined;
      }
      return fetchedPost as BlogPost;

    } catch (e: any) {
      console.error(`Failed to fetch blog post with slug ${slug}:`, e);
      let statusCode = 500;
      let statusMessage = 'Failed to load post.';
      if (e.isH3Error) { // H3Error from our API routes
        statusCode = e.statusCode;
        statusMessage = e.statusMessage || e.message;
      } else if (e.response && e.response.status) { // Axios error
        statusCode = e.response.status;
        statusMessage = e.response.data?.message || e.message;
      } else if (e.message) {
        statusMessage = e.message;
      }
      // Avoid overriding a 404 that might have been set if showError was already called by the try block
      if (!(fetchError.value && fetchError.value.statusCode === 404)) {
         showError({ statusCode, statusMessage, fatal: true });
      }
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

definePageMeta({
  layout: "default",
});
</script>

<style scoped>
.lg\:w-64 { width: 16rem; }
.xl\:w-72 { width: 18rem; }

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
   color: hsl(var(--muted-foreground));
}
.dark .prose-invert a {
  color: hsl(var(--primary));
}
.dark .prose-invert a:hover {
  color: hsl(var(--primary) / 0.8);
}
</style>
