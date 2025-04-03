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
        Schema::create('order_evaluate_counts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->integer('total')->comment('评论总数');
            $table->integer('has_image_total')->comment('晒图数量');
            $table->integer('rank_total')->comment('好评数量');
            $table->integer('goods_rank_total')->comment('产品好数量');
            $table->integer('price_rank_total')->comment('价格合理数量');
            $table->integer('bus_rank_total')->comment('服务好数量');
            $table->integer('delivery_rank_total')->comment('售后服务好数量');
            $table->integer('service_rank_total')->comment('交货快数量');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_evaluate_counts');
    }
};
