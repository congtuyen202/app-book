<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Users & Roles & Permissions (phải có trước tiên)
            // UserSeeder::class,
            // RoleSeeder::class,
            // PermissionSeeder::class,
            // RolePermissionSeeder::class, // Gán quyền cho vai trò
            // UserRoleSeeder::class,       // Gán vai trò cho người dùng
            // DirectPermissionSeeder::class,

            // 2. Sách & các thành phần liên quan (cần User để review, tác giả để gán)
            BookSeeder::class,
            AuthorSeeder::class, // Tác giả cần có trước để gán cho sách
            GenreSeeder::class,  // Thể loại cần có trước để gán cho sách

            // 3. Nội dung do người dùng tạo (cần User và Book)
            ReviewSeeder::class,
            QuoteSeeder::class,

            // 4. Mạng xã hội (cần User, Post, Review, Comment)
            PostSeeder::class,
            CommentSeeder::class,
            LikeSeeder::class,
            FollowerSeeder::class,
            ReadingListSeeder::class, // Tạo danh sách đọc

            // 5. Nhật ký AI (cần User và Book)
            AiSummarySeeder::class,
            AiRequestLogSeeder::class,
        ]);
    }
}