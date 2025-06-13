<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $moderatorRole = Role::where('name', 'moderator')->first();
        $memberRole = Role::where('name', 'member')->first();
        $guestRole = Role::where('name', 'guest')->first();

        // Gán tất cả quyền cho Admin
        if ($adminRole) {
            $adminRole->permissions()->sync(Permission::all()->pluck('id'));
        }

        // Gán quyền cho Moderator
        if ($moderatorRole) {
            $moderatorRole->permissions()->sync([
                Permission::where('name', 'view-public-content')->first()->id,
                Permission::where('name', 'use-basic-ai-features')->first()->id,
                Permission::where('name', 'view-user-profiles')->first()->id,
                Permission::where('name', 'edit-any-review')->first()->id,
                Permission::where('name', 'delete-any-review')->first()->id,
                Permission::where('name', 'edit-any-post')->first()->id,
                Permission::where('name', 'delete-any-post')->first()->id,
                Permission::where('name', 'comment-on-posts')->first()->id,
                Permission::where('name', 'like-content')->first()->id,
            ]);
        }

        // Gán quyền cho Member
        if ($memberRole) {
            $memberRole->permissions()->sync([
                Permission::where('name', 'view-public-content')->first()->id,
                Permission::where('name', 'use-basic-ai-features')->first()->id,
                Permission::where('name', 'edit-own-profile')->first()->id,
                Permission::where('name', 'create-review')->first()->id,
                Permission::where('name', 'edit-own-review')->first()->id,
                Permission::where('name', 'delete-own-review')->first()->id,
                Permission::where('name', 'create-quote')->first()->id,
                Permission::where('name', 'edit-own-quote')->first()->id,
                Permission::where('name', 'delete-own-quote')->first()->id,
                Permission::where('name', 'create-post')->first()->id,
                Permission::where('name', 'edit-own-post')->first()->id,
                Permission::where('name', 'delete-own-post')->first()->id,
                Permission::where('name', 'comment-on-posts')->first()->id,
                Permission::where('name', 'like-content')->first()->id,
                Permission::where('name', 'follow-users')->first()->id,
                Permission::where('name', 'create-reading-list')->first()->id,
                Permission::where('name', 'edit-own-reading-list')->first()->id,
                Permission::where('name', 'delete-own-reading-list')->first()->id,
            ]);
        }

        // Gán quyền cho Guest
        if ($guestRole) {
            $guestRole->permissions()->sync([
                Permission::where('name', 'view-public-content')->first()->id,
                Permission::where('name', 'use-basic-ai-features')->first()->id,
            ]);
        }
    }
}