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
        Schema::create('routers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->string('alias')->nullable()->comment('别名');
            $table->char('is_show', 1)->default('1')->comment('是否显示');
            $table->string('url')->nullable()->comment('pc地址');
            $table->string('h5_url')->nullable()->comment('h5地址');
            $table->string('app_url')->nullable()->comment('app地址');
            $table->string('mini_url')->nullable()->comment('小程序地址');
            $table->string('harmony_url')->comment('鸿蒙跳转地址')->nullable();
            $table->json('params')->nullable()->comment('参数');
            $table->char('scan_need_login', 1)->default('0')->comment('扫码的页面是否需要登录');
            $table->string('hd_image_url')->nullable()->comment('分享基础缩略图');
            $table->char('android_is_open')->comment('安卓是否开启')->default('1');
            $table->char('ios_is_open')->comment('ios是否开启')->default('1');
            $table->char('harmony_is_open')->comment('鸿蒙是否开启')->default('0');
            $table->char('mini_is_open')->comment('小程序是否开启')->default('1');
            $table->timestamps();
        });
        DB::statement("alter table `routers` comment '路由地址表'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
