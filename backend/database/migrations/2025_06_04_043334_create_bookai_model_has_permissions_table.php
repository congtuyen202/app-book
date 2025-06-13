<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookai_model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');

            // Cột model_id và model_type để hỗ trợ quan hệ đa hình (polymorphic)
            // model_id sẽ chứa ID của model (ví dụ: user_id)
            // model_type sẽ chứa tên class của model (ví dụ: 'App\Models\User')
            $table->morphs('model'); // Tạo cột model_id (unsignedBigInteger) và model_type (string)

            $table->index(['model_id', 'model_type'], 'bookai_model_permissions_model_id_model_type_index');

            // Tạo khóa chính kép để đảm bảo mỗi model chỉ có một quyền một lần
            $table->primary(['permission_id', 'model_id', 'model_type'], 'bookai_model_has_permissions_permission_model_primary');

            // Bạn có thể thêm timestamps nếu muốn theo dõi thời gian gán quyền
            // $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookai_model_has_permissions');
    }
};