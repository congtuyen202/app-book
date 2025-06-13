<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookai_ai_requests_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ai_model_name', 100);
            $table->string('request_type', 100); // summarize, content_generation, search_query
            $table->longText('input_text')->nullable(); // Cắt ngắn nếu quá dài
            $table->longText('output_text')->nullable(); // Cắt ngắn nếu quá dài
            $table->unsignedInteger('tokens_used')->nullable();
            $table->decimal('cost', 10, 4)->nullable(); // Chi phí ước tính (ví dụ: $0.0010)
            $table->string('status', 50); // success, failed, rate_limited
            $table->text('error_message')->nullable();
            $table->timestamp('requested_at'); // Thời điểm yêu cầu được gửi
            $table->timestamps(); // updated_at để ghi thời điểm request hoàn thành/cập nhật trạng thái
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookai_ai_requests_log');
    }
};