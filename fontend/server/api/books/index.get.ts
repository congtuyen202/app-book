import { mockBooks } from '~/server/mocks/books';
import type { Book } from '~/types/models';

export default defineEventHandler(async (event): Promise<Book[]> => {
  await new Promise(resolve => setTimeout(resolve, 500));
  const { search, genre } = getQuery(event);
  let books = mockBooks;

  if (search) {
    books = books.filter(b =>
      b.title.toLowerCase().includes((search as string).toLowerCase()) ||
      b.author.toLowerCase().includes((search as string).toLowerCase())
    );
  }
  if (genre) {
    books = books.filter(b => b.genre.includes(genre as string));
  }
  return books;
});
