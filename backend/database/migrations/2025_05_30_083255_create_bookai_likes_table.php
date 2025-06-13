<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookai_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            // Polymorphic columns
            $table->string('likeable_type'); // Tên Model (e.g., App\Models\Post, App\Models\Review, App\Models\Comment)
            $table->unsignedBigInteger('likeable_id'); // ID của đối tượng được like
            $table->timestamps();
            // Có thể thêm unique(['user_id', 'likeable_type', 'likeable_id']) để tránh 1 user like 1 đối tượng nhiều lần
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookai_likes');
    }
};