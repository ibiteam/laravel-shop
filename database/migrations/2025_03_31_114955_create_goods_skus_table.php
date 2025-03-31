<?php

use App\Models\Goods;
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
        Schema::create('goods_skus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->string('sku_value')->comment('属性值ID“|”分割');
            $table->decimal('price', 13)->comment('价格');
            $table->decimal('integral', 13)->default(0.00)->comment('积分');
            $table->integer('number')->comment('库存');
            $table->boolean('is_show')->comment('是否展示 1展示 0不展示');
            $table->smallInteger('sort')->comment('排序');
            $table->timestamps();

            $table->foreign('goods_id')->references('id')->on((new Goods)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_skus');
    }
};
