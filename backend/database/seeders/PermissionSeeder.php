<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission; // Đảm bảo bạn đã import đúng Model Permission của mình

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === Quyền Chung ===
        // Quyền này có thể dùng cho cả web và API, nhưng thường được xem là quyền cơ bản của người dùng web
        Permission::firstOrCreate(
            ['name' => 'view-public-content', 'guard_name' => 'web'],
            ['description' => 'Xem nội dung công khai trên giao diện web.']
        );
        Permission::firstOrCreate(
            ['name' => 'view-public-content-api', 'guard_name' => 'sanctum'], // Quyền xem nội dung công khai qua API
            ['description' => 'Xem nội dung công khai qua API.']
        );


        // === Quyền AI ===
        // Các quyền này thường chỉ dùng qua API, nên gán cho 'sanctum' guard
        Permission::firstOrCreate(
            ['name' => 'use-basic-ai-features', 'guard_name' => 'sanctum'],
            ['description' => 'Sử dụng các tính năng AI cơ bản (ví dụ: tóm tắt, gợi ý).']
        );
        Permission::firstOrCreate(
            ['name' => 'use-premium-ai-features', 'guard_name' => 'sanctum'],
            ['description' => 'Sử dụng các tính năng AI cao cấp (ví dụ: phân tích chuyên sâu).']
        );


        // === Quyền Người dùng ===
        // Quyền quản lý người dùng thường thuộc về admin qua giao diện web/admin panel
        Permission::firstOrCreate(
            ['name' => 'manage-users', 'guard_name' => 'web'],
            ['description' => 'Quản lý người dùng và vai trò.']
        );
        Permission::firstOrCreate(
            ['name' => 'view-user-profiles', 'guard_name' => 'sanctum'], // Xem hồ sơ người dùng khác qua API
            ['description' => 'Xem hồ sơ người dùng khác.']
        );
        Permission::firstOrCreate(
            ['name' => 'edit-own-profile', 'guard_name' => 'sanctum'], // Chỉnh sửa hồ sơ cá nhân qua API
            ['description' => 'Chỉnh sửa hồ sơ của bản thân.']
        );


        // === Quyền Sách ===
        // Thường là các tác vụ qua API
        Permission::firstOrCreate(
            ['name' => 'create-book', 'guard_name' => 'sanctum'],
            ['description' => 'Thêm sách mới vào thư viện.']
        );
        Permission::firstOrCreate(
            ['name' => 'edit-book', 'guard_name' => 'sanctum'],
            ['description' => 'Chỉnh sửa thông tin bất kỳ cuốn sách nào.']
        );
        Permission::firstOrCreate(
            ['name' => 'delete-book', 'guard_name' => 'sanctum'],
            ['description' => 'Xóa sách khỏi thư viện.']
        );


        // === Quyền Đánh giá & Trích dẫn (Review & Quote) ===
        // Các tác vụ qua API
        Permission::firstOrCreate(
            ['name' => 'create-review', 'guard_name' => 'sanctum'],
            ['description' => 'Viết đánh giá cho sách.']
        );
        Permission::firstOrCreate(
            ['name' => 'edit-own-review', 'guard_name' => 'sanctum'],
            ['description' => 'Chỉnh sửa đánh giá của bản thân.']
        );
        Permission::firstOrCreate(
            ['name' => 'delete-own-review', 'guard_name' => 'sanctum'],
            ['description' => 'Xóa đánh giá của bản thân.']
        );
        Permission::firstOrCreate(
            ['name' => 'edit-any-review', 'guard_name' => 'sanctum'],
            ['description' => 'Chỉnh sửa bất kỳ đánh giá nào.']
        );
        Permission::firstOrCreate(
            ['name' => 'delete-any-review', 'guard_name' => 'sanctum'],
            ['description' => 'Xóa bất kỳ đánh giá nào.']
        );
        Permission::firstOrCreate(
            ['name' => 'create-quote', 'guard_name' => 'sanctum'],
            ['description' => 'Tạo trích dẫn từ sách.']
        );
        Permission::firstOrCreate(
            ['name' => 'edit-own-quote', 'guard_name' => 'sanctum'],
            ['description' => 'Chỉnh sửa trích dẫn của bản thân.']
        );
        Permission::firstOrCreate(
            ['name' => 'delete-own-quote', 'guard_name' => 'sanctum'],
            ['description' => 'Xóa trích dẫn của bản thân.']
        );


        // === Quyền Mạng xã hội ===
        // Các tác vụ qua API
        Permission::firstOrCreate(
            ['name' => 'create-post', 'guard_name' => 'sanctum'],
            ['description' => 'Đăng bài viết mới trên trang cá nhân/nhóm.']
        );
        Permission::firstOrCreate(
            ['name' => 'edit-own-post', 'guard_name' => 'sanctum'],
            ['description' => 'Chỉnh sửa bài viết của bản thân.']
        );
        Permission::firstOrCreate(
            ['name' => 'delete-own-post', 'guard_name' => 'sanctum'],
            ['description' => 'Xóa bài viết của bản thân.']
        );
        Permission::firstOrCreate(
            ['name' => 'edit-any-post', 'guard_name' => 'sanctum'],
            ['description' => 'Chỉnh sửa bất kỳ bài viết nào.']
        );
        Permission::firstOrCreate(
            ['name' => 'delete-any-post', 'guard_name' => 'sanctum'],
            ['description' => 'Xóa bất kỳ bài viết nào.']
        );
        Permission::firstOrCreate(
            ['name' => 'comment-on-posts', 'guard_name' => 'sanctum'],
            ['description' => 'Bình luận bài viết và đánh giá.']
        );
        Permission::firstOrCreate(
            ['name' => 'like-content', 'guard_name' => 'sanctum'],
            ['description' => 'Like các nội dung (bài viết, đánh giá, bình luận).']
        );
        Permission::firstOrCreate(
            ['name' => 'follow-users', 'guard_name' => 'sanctum'],
            ['description' => 'Theo dõi người dùng khác.']
        );
        Permission::firstOrCreate(
            ['name' => 'create-reading-list', 'guard_name' => 'sanctum'],
            ['description' => 'Tạo danh sách đọc cá nhân.']
        );
        Permission::firstOrCreate(
            ['name' => 'edit-own-reading-list', 'guard_name' => 'sanctum'],
            ['description' => 'Chỉnh sửa danh sách đọc của bản thân.']
        );
        Permission::firstOrCreate(
            ['name' => 'delete-own-reading-list', 'guard_name' => 'sanctum'],
            ['description' => 'Xóa danh sách đọc của bản thân.']
        );


        // === Quyền Quản lý AI ===
        // Các quyền này thường thuộc về admin và được sử dụng qua API
        Permission::firstOrCreate(
            ['name' => 'view-ai-logs', 'guard_name' => 'sanctum'],
            ['description' => 'Xem nhật ký các yêu cầu AI.']
        );
        Permission::firstOrCreate(
            ['name' => 'manage-ai-settings', 'guard_name' => 'sanctum'],
            ['description' => 'Quản lý cấu hình và cài đặt AI.']
        );
    }
}