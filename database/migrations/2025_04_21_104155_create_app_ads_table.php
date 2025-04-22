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
        Schema::create('app_ads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ad_cate_id')->comment('广告分类id');
            $table->string('name')->nullable()->comment('标题');
            $table->string('image')->comment('广告图');
            $table->integer('sort')->default(1)->comment('排序');
            $table->tinyInteger('link_type')->default(1)->comment('链接类型 1、https 2、移动端链接');
            $table->string('link')->nullable()->comment('链接');
            $table->tinyInteger('is_show')->default(1)->comment('是否展示 1、是 0、否');
            $table->tinyInteger('type')->default(1)->comment('广告类型 1、时间限制 2、长久广告');
            $table->timestamp('start_time')->nullable()->comment('开始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_ads');
    }
};
