<?php

use App\Models\Goods;
use App\Models\GoodsSpec;
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
        Schema::create('goods_spec_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->unsignedBigInteger('goods_spec_id')->comment('商品规格ID');
            $table->string('value')->comment('规格值');
            $table->string('thumb')->comment('规格图');
            $table->smallInteger('sort')->comment('排序');
            $table->timestamps();

            $table->foreign('goods_id')->references('id')->on((new Goods)->getTable());
            $table->foreign('goods_spec_id')->references('id')->on((new GoodsSpec)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_spec_values');
    }
};
