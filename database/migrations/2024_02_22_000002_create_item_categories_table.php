<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 创建物品分类表迁移
 * 包含分类名称和所属用户，同时添加与items表的外键关联
 */
return new class extends Migration
{
    /**
     * 运行迁移
     * 1. 创建item_categories表，包含以下字段：
     * - id: 主键
     * - name: 分类名称
     * - user_id: 所属用户ID
     * 2. 添加items表的category_id外键约束
     */
    public function up(): void
    {
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // 移除外键约束
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * 回滚迁移
     * 1. 删除items表的category_id外键约束
     * 2. 删除item_categories表
     */
    public function down(): void
    {
        Schema::dropIfExists('item_categories');
    }
}; 