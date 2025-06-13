<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookai_followers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('follower_id'); // Người theo dõi
            $table->unsignedBigInteger('followed_id'); // Người được theo dõi
            $table->timestamps();
            $table->unique(['follower_id', 'followed_id']); // Một người chỉ theo dõi một người một lần
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookai_followers');
    }
};