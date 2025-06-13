<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AiSummary;

class AiSummarySeeder extends Seeder
{
    public function run(): void
    {
        AiSummary::factory()->count(1000)->create(); // Tạo 1000 tóm tắt AI mẫu
    }
}