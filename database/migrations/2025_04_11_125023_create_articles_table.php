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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_category_id')->default(0)->comment('分类id');
            $table->string('title')->nullable()->comment('标题');
            $table->string('cover')->nullable()->comment('封面');
            $table->string('keywords')->nullable()->comment('关键词');
            $table->string('description')->nullable()->comment('描述');
            $table->string('author')->nullable()->comment('作者');
            $table->tinyInteger('is_top')->default(0)->comment('是否置顶');
            $table->tinyInteger('is_recommend')->default(0)->comment('是否推荐');
            $table->tinyInteger('is_login')->default(0)->comment('是否需要登录');
            $table->tinyInteger('is_show')->default(1)->comment('是否显示');
            $table->integer('sort')->default(0)->comment('排序');
            $table->integer('click_count')->default(0)->comment('点击次数');
            $table->text('file_url')->nullable()->comment('文件路径');
            $table->unsignedBigInteger('goods_category_id')->default(0)->comment('商品分类id');
            $table->unsignedBigInteger('admin_user_id')->comment('管理员id')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
