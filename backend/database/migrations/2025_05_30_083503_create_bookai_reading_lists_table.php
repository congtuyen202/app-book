<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookai_reading_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('visibility', 50)->default('private'); // public, private
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookai_reading_lists');
    }
};