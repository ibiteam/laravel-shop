<?php

use App\Models\Goods;
use App\Models\GoodsLabel;
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
        Schema::create('goods_has_labels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->unsignedBigInteger('goods_label_id')->comment('商品标签ID');
            $table->timestamps();

            $table->foreign('goods_id')->references('id')->on((new Goods())->getTable());
            $table->foreign('goods_label_id')->references('id')->on((new GoodsLabel())->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_has_labels');
    }
};
