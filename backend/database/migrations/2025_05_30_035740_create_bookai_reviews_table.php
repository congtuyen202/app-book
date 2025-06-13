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
        Schema::create('bookai_reviews', function (Blueprint $table) {
            $table->id();
            // user_id và book_id sẽ được liên kết qua Eloquent sau này
            $table->unsignedBigInteger('user_id'); // ID của người dùng viết review
            $table->unsignedBigInteger('book_id'); // ID của cuốn sách được review
            $table->unsignedTinyInteger('rating'); // Điểm đánh giá (1-5 sao)
            $table->text('review_text')->nullable(); // Nội dung đánh giá
            $table->string('status', 50)->default('approved'); // Trạng thái review (pending, approved, rejected)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookai_reviews');
    }
};