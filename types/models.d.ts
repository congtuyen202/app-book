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

// New Relational Interfaces for Blog
export interface BlogAuthor {
  id: string; // Or number, depending on your Laravel PK type
  name: string;
  bio?: string;
  avatar_url?: string;
}

export interface BlogCategory {
  id: string; // Or number
  name: string;
  slug: string;
  description?: string;
}

export interface BlogTag {
  id: string; // Or number
  name: string;
  slug: string;
}

// Updated BlogPost Interface
export interface BlogPost {
  id: string; // Or number
  slug: string;
  title: string;
  summary?: string; // Made optional
  content: string; // Full content, can be HTML or Markdown
  imageUrl?: string;
  author?: BlogAuthor;    // Changed to relational
  category?: BlogCategory;  // Changed to relational
  tags?: BlogTag[];       // Changed to array of BlogTag objects
  publishedDate: string; // Assuming GraphQL returns camelCase 'publishedDate'
  metaTitle?: string;
  metaDescription?: string;
  status?: string; // Optional status, e.g., "published", "draft"
}
