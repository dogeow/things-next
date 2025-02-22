<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 创建区域表
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // 移除外键约束
            $table->string('name');
            $table->timestamps();
        });

        // 创建房间表
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_id');  // 移除外键约束
            $table->string('name');
            $table->timestamps();
        });

        // 创建具体位置表
        Schema::create('spots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');  // 移除外键约束
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spots');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('areas');
    }
}; 