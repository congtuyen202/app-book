<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class FollowerSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $numUsers = $users->count();

        // Tạo khoảng 2000 - 5000 mối quan hệ follow ngẫu nhiên
        for ($i = 0; $i < 4000; $i++) {
            $follower = $users->random();
            $followed = $users->random();

            // Đảm bảo người theo dõi không phải là người được theo dõi và mối quan hệ chưa tồn tại
            if ($follower->id !== $followed->id) {
                try {
                    DB::table('bookai_followers')->insert([
                        'follower_id' => $follower->id,
                        'followed_id' => $followed->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Bỏ qua nếu có lỗi trùng lặp (unique constraint)
                    continue;
                }
            }
        }
    }
}