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
        Schema::create('bookai_blog_category_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bookai_blog_id');
            $table->unsignedBigInteger('bookai_blog_category_id');

            $table->foreign('bookai_blog_id')->references('id')->on('bookai_blogs')->onDelete('cascade');
            $table->foreign('bookai_blog_category_id', 'blog_category_pivot_category_foreign')->references('id')->on('bookai_blog_categories')->onDelete('cascade');

            $table->unique(['bookai_blog_id', 'bookai_blog_category_id'], 'blog_blog_category_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookai_blog_category_pivot');
    }
};
