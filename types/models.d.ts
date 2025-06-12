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
  language?: string; // Add this line
}
