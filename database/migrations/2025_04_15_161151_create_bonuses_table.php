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
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('红包名称');
            $table->decimal('money', 10)->comment('红包金额');
            $table->integer('number')->default(0)->comment('红包数量');
            $table->integer('limit')->default(0)->comment('限领数量');
            $table->timestamp('send_start_time')->nullable()->comment('发放开始时间');
            $table->timestamp('send_end_time')->nullable()->comment('发放结束时间');
            $table->timestamp('use_start_time')->nullable()->comment('使用开始时间');
            $table->timestamp('use_end_time')->nullable()->comment('使用结束时间');
            $table->tinyInteger('type')->default(0)->comment('红包类型 0、不限制 1、限商品 2、限分类');
            $table->string('type_values')->nullable()->comment('红包类型限制的值');
            $table->tinyInteger('can_use_coupon')->default(0)->comment('是否跟优惠券一起使用 0、不可以 1、可以');
            $table->tinyInteger('is_add')->default(0)->comment('是否可以叠加 0、不可以 1、可以');
            $table->decimal('min_amount')->default(0)->comment('最小使用金额');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
