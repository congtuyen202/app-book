import type { Book, Chapter } from '~/types/models';

const sampleContentPage1 = "Đây là trang đầu tiên của chương mẫu. Câu chuyện bắt đầu một cách bí ẩn và hấp dẫn, hứa hẹn nhiều điều thú vị phía trước. Nhân vật chính đang đối mặt với một thử thách lớn.";
const sampleContentPage2 = "Trang thứ hai tiếp tục diễn biến câu chuyện. Những tình tiết mới được hé lộ, và nhân vật của chúng ta phải đưa ra những quyết định quan trọng. Liệu họ có chọn đúng con đường?";

export const mockBooks: Book[] = [
  {
    id: 'book1',
    title: 'Cuộc Phiêu Lưu Kỳ Thú',
    author: 'Nguyễn Văn A',
    isbn: '978-1234567890',
    coverImageUrl: 'https://source.unsplash.com/random/300x450/?book,adventure',
    description: 'Một cuốn sách phiêu lưu đầy hấp dẫn về những vùng đất chưa được khám phá và những bí mật cổ xưa.',
    genre: ['Phiêu lưu', 'Giả tưởng'],
    price: 150000,
    publicationDate: '2023-01-15',
    publisher: 'NXB Trẻ',
    averageRating: 4.5,
    sampleChapters: [
      { id: 'ch1_b1_s', chapterNumber: 1, title: 'Chương Mở Đầu', content: sampleContentPage1 },
      { id: 'ch2_b1_s', chapterNumber: 2, title: 'Hành Trình Bắt Đầu', content: sampleContentPage2 },
    ],
    fullChapters: [
      { id: 'ch1_b1_f', chapterNumber: 1, title: 'Chương Mở Đầu', content: sampleContentPage1 + ' (Nội dung đầy đủ hơn)' },
      { id: 'ch2_b1_f', chapterNumber: 2, title: 'Hành Trình Bắt Đầu', content: sampleContentPage2 + ' (Nội dung đầy đủ hơn)' },
      { id: 'ch3_b1_f', chapterNumber: 3, title: 'Thử Thách Đầu Tiên', content: 'Nội dung đầy đủ của chương 3.' },
    ],
    reviews: [
      { userId: 'user1', rating: 5, comment: 'Tuyệt vời!', date: '2023-02-01' }
    ],
    language: 'vi-VN',
  },
  {
    id: 'book2',
    title: 'Bí Ẩn Dưới Đáy Biển',
    author: 'Trần Thị B',
    isbn: '978-0987654321',
    coverImageUrl: 'https://source.unsplash.com/random/300x450/?book,mystery',
    description: 'Khám phá những bí ẩn sâu thẳm của đại dương và những sinh vật kỳ lạ.',
    genre: ['Bí ẩn', 'Khoa học viễn tưởng'],
    price: 120000,
    publicationDate: '2022-08-20',
    publisher: 'NXB Kim Đồng',
    averageRating: 4.2,
    sampleChapters: [
      { id: 'ch1_b2_s', chapterNumber: 1, title: 'Lời Thì Thầm Của Biển Cả', content: 'Biển cả luôn ẩn chứa những điều bí ẩn...' }
    ],
    reviews: [],
    language: 'vi-VN',
  },
  {
    id: 'book3',
    title: 'Lập Trình Với Vue',
    author: 'AI Developer',
    isbn: '978-1122334455',
    coverImageUrl: 'https://source.unsplash.com/random/300x450/?book,code',
    description: 'Hướng dẫn toàn diện về phát triển ứng dụng web hiện đại với Vue.js và Nuxt.js.',
    genre: ['Công nghệ', 'Giáo dục'],
    publicationDate: '2024-05-10',
    publisher: 'NXB Khoa Học Kỹ Thuật',
    language: 'en-US',
  }
];
