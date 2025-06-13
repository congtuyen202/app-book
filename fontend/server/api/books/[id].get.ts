import { mockBooks } from '~/server/mocks/books';
import type { Book } from '~/types/models';

export default defineEventHandler(async (event): Promise<Book | undefined> => {
  await new Promise(resolve => setTimeout(resolve, 500));
  const bookId = event.context.params?.id;
  return mockBooks.find(b => b.id === bookId);
});
