<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_website_decorations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->string('type',191)->comment('类型:1：底部菜单；2：一级页面；3：二级页面');
            $table->boolean('is_show')->comment('是否显示');
            $table->string('alias')->comment('页面别名');
            $table->integer('parent_id')->comment('父级集合id');
            $table->integer('admin_user_id')->comment('最后一次装修人ID/管理员ID');
            $table->string('image_url')->comment('封面地址');
            $table->string('title')->nullable()->comment('网页TDK:标题');
            $table->string('keywords')->nullable()->comment('网页TDK:关键词');
            $table->string('description')->nullable()->comment('网页TDK:描述');
            $table->boolean('version')->default(1)->comment('版本：1买家版 2卖家版');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `user_addresses` COMMENT '移动端装修表'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_website_decorations');
    }
};
