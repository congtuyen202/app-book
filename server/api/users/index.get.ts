import { mockUsers } from '~/server/mocks/users';
import type { User } from '~/types/models';

export default defineEventHandler(async (event): Promise<User[]> => {
  // Simulate API delay
  await new Promise(resolve => setTimeout(resolve, 500));
  return mockUsers;
});
