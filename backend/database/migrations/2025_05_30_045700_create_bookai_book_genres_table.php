<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookai_book_genres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('genre_id');
            $table->timestamps();
            // Có thể thêm unique(['book_id', 'genre_id'])
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookai_book_genres');
    }
};