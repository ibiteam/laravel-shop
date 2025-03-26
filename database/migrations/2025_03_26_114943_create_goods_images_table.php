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
        Schema::create('goods_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->string('goods_original')->comment('商品图片');
            $table->string('goods_thumb')->comment('商品缩略图');
            $table->timestamps();

            $table->foreign('goods_id')->references('id')->on((new Goods)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_images');
    }
};
