<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookai_reading_list_books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reading_list_id');
            $table->unsignedBigInteger('book_id');
            $table->string('status', 50)->default('pending'); // reading, completed, dropped
            $table->date('started_reading_at')->nullable();
            $table->date('finished_reading_at')->nullable();
            $table->timestamps();
            $table->unique(['reading_list_id', 'book_id']); // Một sách chỉ xuất hiện một lần trong một danh sách
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookai_reading_list_books');
    }
};