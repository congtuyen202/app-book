<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Hoặc bất kỳ model nào khác mà bạn muốn gán quyền trực tiếp
use App\Models\Permission; // Model Permission của bạn (App\Models\Permission)
use Illuminate\Support\Facades\DB; // Để truy cập bảng trực tiếp nếu cần

class DirectPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy một người dùng mẫu (ví dụ: người dùng 'admin' hoặc một user ngẫu nhiên)
        $user = User::where('email', 'admin@example.com')->first();
        // Hoặc: $user = User::inRandomOrder()->first();

        // Lấy một quyền mẫu mà bạn muốn gán trực tiếp
        $permission = Permission::where('name', 'view-public-content-api')->first();
        // Hoặc: $permission = Permission::where('name', 'view-ai-logs')->first();
        if ($user && $permission) {
            // Gán quyền trực tiếp cho người dùng bằng phương thức của Spatie
            // Đây là cách được khuyến nghị nhất nếu bạn dùng trait HasRoles trên User model
            // Phương thức givePermissionTo() sẽ tự động tạo bản ghi trong bookai_model_has_permissions
            $user->givePermissionTo($permission->name, $permission->guard_name);

            $this->command->info("Gán quyền '{$permission->name}' trực tiếp cho người dùng '{$user->username}'.");
        } else {
            $this->command->warn('Không tìm thấy người dùng hoặc quyền để gán trực tiếp.');
        }

        // Ví dụ gán một quyền khác cho một người dùng khác
        $anotherUser = User::where('email', '!=', 'admin@example.com')->inRandomOrder()->first();
        $anotherPermission = Permission::where('name', 'manage-users')->first();

        if ($anotherUser && $anotherPermission) {
            $anotherUser->givePermissionTo($anotherPermission->name, $anotherPermission->guard_name);
            $this->command->info("Gán quyền '{$anotherPermission->name}' trực tiếp cho người dùng '{$anotherUser->username}'.");
        }
    }
}