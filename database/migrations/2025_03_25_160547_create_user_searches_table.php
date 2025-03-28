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
        Schema::create('user_searches', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->comment('用户id')->index();
            $table->string('goods_ids')->nullable()->comment('搜索的商品id');
            $table->string('keywords')->nullable()->comment('搜索关键词');
            $table->string('source')->nullable()->comment('来源');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_searches');
    }
};
