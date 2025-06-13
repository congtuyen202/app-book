<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'admin', 'description' => 'Quản trị viên toàn quyền']);
        Role::create(['name' => 'moderator', 'description' => 'Kiểm duyệt nội dung']);
        Role::create(['name' => 'member', 'description' => 'Người dùng đã đăng ký']);
        Role::create(['name' => 'guest', 'description' => 'Khách truy cập']);
    }
}