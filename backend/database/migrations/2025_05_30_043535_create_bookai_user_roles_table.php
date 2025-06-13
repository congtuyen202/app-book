<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookai_user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
            // Có thể thêm unique(['user_id', 'role_id']) nếu không muốn một user có cùng một role nhiều lần
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookai_user_roles');
    }
};