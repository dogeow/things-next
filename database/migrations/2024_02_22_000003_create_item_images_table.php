<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 创建物品图片表迁移
 * 用于存储物品的多张图片，包含主图标识和排序
 */
return new class extends Migration
{
    /**
     * 运行迁移
     * 创建item_images表，包含以下字段：
     * - id: 主键
     * - item_id: 关联的物品ID
     * - path: 图片路径
     * - is_primary: 是否为主图
     * - sort_order: 排序顺序
     */
    public function up(): void
    {
        Schema::create('item_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('path');
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * 回滚迁移
     * 删除item_images表
     */
    public function down(): void
    {
        Schema::dropIfExists('item_images');
    }
}; 