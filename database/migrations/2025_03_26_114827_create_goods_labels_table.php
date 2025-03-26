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
        Schema::create('goods_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('标签名称');
            $table->string('type')->comment('标签类型：1文字标签 2图片标签');
            $table->string('image')->nullable()->comment('图片标签地址');
            $table->string('font_color', 20)->nullable()->comment('文字颜色');
            $table->string('background_color', 20)->nullable()->comment('背景颜色');
            $table->string('border_color', 20)->nullable()->comment('边框颜色');
            $table->boolean('is_show')->comment('是否显示 1显示 0不显示');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_labels');
    }
};
