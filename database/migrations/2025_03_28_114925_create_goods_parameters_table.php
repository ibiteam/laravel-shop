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
        Schema::create('goods_parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->string('name')->comment('参数名称');
            $table->string('value')->comment('参数值');
            $table->timestamps();

            $table->foreign('goods_id')->references('id')->on((new Goods)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_parameters');
    }
};
