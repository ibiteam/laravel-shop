<?php

use App\Models\User;
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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->unsignedBigInteger('goods_id')->comment('商品id');
            $table->integer('goods_sku_id')->default(0)->comment('商品规格id');
            $table->integer('buy_number')->default(0)->comment('购买数量');
            $table->tinyInteger('is_check')->default(0)->comment('是否选中结算：1是 0否');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on((new User)->getTable());
            $table->foreign('goods_id')->references('id')->on((new Goods)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
