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
        Schema::create('bookai_books', function (Blueprint $table) {
            $table->id(); // Tương đương BIGINT UNSIGNED, Primary Key, Auto Increment
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->text('description')->nullable();
            $table->year('published_year')->nullable();
            $table->string('isbn', 20)->unique()->nullable();
            $table->string('language', 50)->nullable();
            $table->unsignedInteger('page_count')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->decimal('avg_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('rating_count')->default(0);
            $table->timestamps(); // Thêm created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookai_books');
    }
};
