<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quote;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        Quote::factory()->count(500)->create(); // Tạo 500 trích dẫn mẫu
    }
}