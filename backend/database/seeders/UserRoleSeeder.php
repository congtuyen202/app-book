<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Gán vai trò 'admin' cho user 'admin@example.com'
        $adminUser = User::where('email', 'admin@example.com')->first();
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminUser && $adminRole) {
            $adminUser->roles()->attach($adminRole->id);
        }

        // Gán vai trò 'member' cho tất cả người dùng còn lại
        $memberRole = Role::where('name', 'member')->first();
        if ($memberRole) {
            User::where('email', '!=', 'admin@example.com')->get()->each(function ($user) use ($memberRole) {
                $user->roles()->attach($memberRole->id);
            });
        }
    }
}