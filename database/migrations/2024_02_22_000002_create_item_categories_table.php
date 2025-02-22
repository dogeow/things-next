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
            $table->string('name')->comment('分类名称');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('所属用户ID');
            $table->timestamps();
        });

        // 添加外键约束
        Schema::table('items', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')
                  ->on('item_categories')
                  ->nullOnDelete()
                  ->comment('关联到item_categories表的外键');
        });
    }

    /**
     * 回滚迁移
     * 1. 删除items表的category_id外键约束
     * 2. 删除item_categories表
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });
        Schema::dropIfExists('item_categories');
    }
}; 