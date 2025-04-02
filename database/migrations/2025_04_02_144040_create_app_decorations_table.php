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
        Schema::create('app_decorations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->boolean('is_show')->comment('是否显示');
            $table->string('alias')->comment('页面别名');
            $table->integer('parent_id')->comment('父级集合id');
            $table->integer('admin_user_id')->comment('最后一次装修人ID/管理员ID');
            $table->string('image_url')->comment('封面地址');
            $table->string('title')->nullable()->comment('网页TDK:标题');
            $table->string('keywords')->nullable()->comment('网页TDK:关键词');
            $table->string('description')->nullable()->comment('网页TDK:描述');
            $table->timestamp('release_time')->nullable()->comment('发布时间');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `app_decorations` COMMENT '移动端装修表'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_decorations');
    }
};
