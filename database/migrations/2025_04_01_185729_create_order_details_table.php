<?php

use App\Models\Goods;
use App\Models\Order;
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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->string('goods_no')->comment('商品编号');
            $table->string('goods_name')->comment('商品名称');
            $table->integer('goods_number')->comment('商品数量');
            $table->decimal('goods_price', 13)->comment('商品价格');
            $table->integer('goods_integral')->comment('商品积分数量');
            $table->decimal('goods_amount', 13)->comment('商品总价');
            $table->string('goods_unit', 30)->comment('商品单位');
            $table->unsignedBigInteger('goods_sku_id')->default(0)->comment('商品 SKU ID');
            $table->json('goods_sku_value')->nullable()->comment('商品规格值');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on((new Order)->getTable());
            $table->foreign('goods_id')->references('id')->on((new Goods)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
