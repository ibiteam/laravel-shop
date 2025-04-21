<?php

use App\Models\User;
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
        Schema::create('order_evaluates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('订单ID')->index();
            $table->unsignedBigInteger('order_detail_id')->comment('订单明细ID')->index();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->unsignedTinyInteger('goods_id')->comment('商品ID');
            $table->boolean('is_anonymous')->comment('是否匿名');
            $table->tinyInteger('status')->comment('状态：0未处理 1通过 2驳回');
            $table->string('comment')->comment('评价内容');
            $table->json('images')->nullable()->comment('评价图片');
            $table->dateTime('comment_at')->comment('评价时间');
            $table->tinyInteger('rank')->comment('综合评分');
            $table->tinyInteger('goods_rank')->comment('商品评分');
            $table->tinyInteger('price_rank')->comment('价格评分');
            $table->tinyInteger('bus_rank')->comment('商家服务评分');
            $table->tinyInteger('delivery_rank')->comment('交货速度评分');
            $table->tinyInteger('service_rank')->comment('服务评分');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on((new User)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_evaluates');
    }
};
