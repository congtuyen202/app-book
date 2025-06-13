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
        Schema::create('bookai_blog_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bookai_blog_id');
            $table->unsignedBigInteger('user_id')->nullable(); // Allowing anonymous comments
            $table->text('content');
            $table->timestamps();

            $table->foreign('bookai_blog_id')->references('id')->on('bookai_blogs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookai_blog_comments');
    }
};
