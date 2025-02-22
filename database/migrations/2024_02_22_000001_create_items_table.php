<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 创建物品表迁移
 * 包含物品的基本信息、数量、状态、过期时间、购买信息等
 */
return new class extends Migration
{
    /**
     * 运行迁移
     * 创建items表，包含以下字段：
     * - id: 主键
     * - name: 物品名称
     * - description: 物品描述
     * - user_id: 所属用户ID
     * - quantity: 数量，默认1
     * - status: 状态，默认active
     * - expiry_date: 过期时间
     * - purchase_date: 购买时间
     * - purchase_price: 购买价格
     * - category_id: 分类ID
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('物品名称');
            $table->text('description')->nullable()->comment('物品描述');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('所属用户ID');
            $table->integer('quantity')->default(1)->comment('数量');
            $table->string('status')->default('active')->comment('状态');
            $table->date('expiry_date')->nullable()->comment('过期时间');
            $table->date('purchase_date')->nullable()->comment('购买时间');
            $table->decimal('purchase_price', 10, 2)->nullable()->comment('购买价格');
            $table->foreignId('category_id')->nullable()->comment('分类ID');
            $table->timestamps();
        });
    }

    /**
     * 回滚迁移
     * 删除items表
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
}; 