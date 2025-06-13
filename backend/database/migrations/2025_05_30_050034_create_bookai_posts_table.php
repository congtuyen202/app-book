<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookai_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('content');
            $table->unsignedBigInteger('book_id')->nullable(); // Có thể liên kết với một cuốn sách
            $table->string('post_type', 50)->default('text'); // text, image, link
            $table->string('image_url')->nullable();
            $table->string('visibility', 50)->default('public'); // public, friends, private
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookai_posts');
    }
};