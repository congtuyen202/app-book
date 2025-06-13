<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(100)->create(); // Tạo 100 người dùng mẫu
        // Tạo một người dùng admin cụ thể để dễ dàng đăng nhập
        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Admin User',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}