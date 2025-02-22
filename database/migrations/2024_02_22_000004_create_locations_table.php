<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 区域表（第一层）
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // 房间表（第二层）
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // 具体位置表（第三层）
        Schema::create('spots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // 在物品表中添加位置关联
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('spot_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['spot_id']);
            $table->dropColumn('spot_id');
        });
        Schema::dropIfExists('spots');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('areas');
    }
}; 