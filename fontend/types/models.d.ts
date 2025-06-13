export interface User {
  id: string;
  name: string;
  email: string;
  bio?: string;
  profilePictureUrl?: string;
  readingHistory?: Array<{ bookId: string; lastReadDate: string; progress: number }>;
}

export interface Chapter {
  id: string;
  chapterNumber: number;
  title: string;
  content: string; // For simplicity, content is a long string. Could be structured.
}

export interface Book {
  id: string;
  title: string;
  author: string;
  isbn: string;
  coverImageUrl: string;
  description: string;
  genre: string[];
  price?: number; // Optional, for books that can be purchased
  sampleChapters?: Chapter[];
  fullChapters?: Chapter[]; // For purchased/owned books
  publicationDate: string;
  publisher: string;
  averageRating?: number;
  reviews?: Array<{ userId: string; rating: number; comment: string; date: string }>;
  language?: string;
}

// BlogPost matches the GET_BLOG_POSTS query in fontend/pages/blog/index.vue
export interface BlogPost {
  id: string;
  slug: string;
  title: string;
  summary: string;
  content?: string; // Not fetched in the index page query, but can be part of the model
  imageUrl?: string;
  author_name: string;
  published_date: string;
  category_name?: string;
  tags?: string[];
  metaTitle?: string; // Not fetched in the index page query
  metaDescription?: string; // Not fetched in the index page query
}

// PaginatorInfo matches the paginatorInfo from GET_BLOG_POSTS query
export interface PaginatorInfo {
  currentPage: number;
  lastPage: number;
  total: number;
  count: number;
  hasMorePages: boolean;
  // The example PaginatorInfo had firstItem, lastItem, perPage.
  // These are not in the GET_BLOG_POSTS query's paginatorInfo.
  // Adding them as optional if they might appear in other queries or if the API is inconsistent.
  firstItem?: number;
  lastItem?: number;
  perPage?: number;
}

// Wrapper type for the GetBookaiBlogs query result
export interface BookaiBlogsData {
  bookaiBlogs: {
    data: BlogPost[];
    paginatorInfo: PaginatorInfo;
  };
}
