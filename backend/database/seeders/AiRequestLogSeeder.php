<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AiRequestLog;

class AiRequestLogSeeder extends Seeder
{
    public function run(): void
    {
        AiRequestLog::factory()->count(10000)->create(); // Tạo 10000 nhật ký yêu cầu AI
    }
}