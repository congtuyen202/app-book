<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Like;

class LikeSeeder extends Seeder
{
    public function run(): void
    {
        Like::factory()->count(10000)->create(); // Tạo 10000 lượt like mẫu
    }
}