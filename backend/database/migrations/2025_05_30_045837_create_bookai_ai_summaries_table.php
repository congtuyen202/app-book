<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookai_ai_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->string('ai_model_name', 100); // Ví dụ: GPT-3.5, Gemini-Pro, Claude-3
            $table->string('summary_type', 50); // Ví dụ: short, chapter, full-plot
            $table->longText('content'); // Nội dung tóm tắt
            $table->unsignedInteger('token_count')->nullable(); // Số token sử dụng
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookai_ai_summaries');
    }
};