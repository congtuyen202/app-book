import { mockBooks } from '~/server/mocks/books';
import type { Chapter } from '~/types/models';

export default defineEventHandler(async (event): Promise<Chapter[] | undefined> => {
  await new Promise(resolve => setTimeout(resolve, 300));
  const bookId = event.context.params?.id;
  const book = mockBooks.find(b => b.id === bookId);
  // For simplicity, returning sample chapters or full chapters based on a query param
  // In a real app, this would involve checking ownership
  const type = getQuery(event).type || 'sample'; // 'sample' or 'full'
  if (book) {
    return type === 'full' ? book.fullChapters : book.sampleChapters;
  }
  return undefined;
});
