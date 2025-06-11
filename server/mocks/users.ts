import type { User } from '~/types/models';

export const mockUsers: User[] = [
  {
    id: 'user1',
    name: 'Alice Wonderland',
    email: 'alice@example.com',
    profilePictureUrl: 'https://source.unsplash.com/random/100x100/?person,woman',
    readingHistory: [
      { bookId: 'book1', lastReadDate: '2024-07-15', progress: 75 },
      { bookId: 'book2', lastReadDate: '2024-07-20', progress: 30 },
    ],
  },
  {
    id: 'user2',
    name: 'Bob The Builder',
    email: 'bob@example.com',
    profilePictureUrl: 'https://source.unsplash.com/random/100x100/?person,man',
  },
];
