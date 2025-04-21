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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('优惠券名称');
            $table->decimal('money', 10)->comment('优惠券金额');
            $table->integer('number')->default(0)->comment('优惠券数量 0 不限量');
            $table->integer('limit')->default(0)->comment('每人限领数量 0 不限制');
            $table->tinyInteger('style')->default(1)->comment('优惠券类型 1、满减券');
            $table->tinyInteger('type')->default(1)->comment('优惠券限制类型 0、无限制 1、限商品 2、限分类');
            $table->string('type_values')->default('')->comment('限制类型的值');
            $table->decimal('min_amount', 10)->default(0)->comment('最小使用金额');
            $table->timestamp('start_time')->nullable()->comment('开始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->integer('send_start_time')->default(0)->comment('发放开始时间');
            $table->integer('send_end_time')->default(0)->comment('发放结束时间');
            $table->tinyInteger('is_add')->default(0)->comment('是否可以叠加 1、可以叠加 0、不可以叠加');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
