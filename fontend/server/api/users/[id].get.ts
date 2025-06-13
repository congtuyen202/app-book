import { mockUsers } from '~/server/mocks/users';
import type { User } from '~/types/models';

export default defineEventHandler(async (event): Promise<User | undefined> => {
  // Simulate API delay
  await new Promise(resolve => setTimeout(resolve, 500));
  const userId = event.context.params?.id;
  return mockUsers.find(u => u.id === userId);
});
