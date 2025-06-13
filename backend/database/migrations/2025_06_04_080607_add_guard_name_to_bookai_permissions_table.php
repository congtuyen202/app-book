<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookai_permissions', function (Blueprint $table) {
             $table->string('guard_name', 255)->after('name')->nullable();

            // Cập nhật các quyền hiện có để có guard_name mặc định (ví dụ: 'web')
            // Điều này chỉ áp dụng nếu bạn đã có dữ liệu quyền trong bảng
            // và muốn đặt guard_name cho chúng.
            // Nếu bạn chạy migrate:fresh --seed, có thể bỏ qua bước này.
            // DB::table('bookai_permissions')->update(['guard_name' => 'web']);

            // Đảm bảo cặp (name, guard_name) là duy nhất
            // Nếu bạn đã có index unique trên 'name', bạn cần drop nó trước
            $table->dropUnique(['name']); // Nếu có unique index trên 'name'
            $table->unique(['name', 'guard_name']); // Thêm unique index ch
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookai_permissions', function (Blueprint $table) {
             $table->dropUnique(['name', 'guard_name']); // Xóa unique index mới
            // $table->unique(['name']); // Khôi phục unique index cũ nếu có
            $table->dropColumn('guard_name'); // Xóa cột guard_name
        });
    }
};
