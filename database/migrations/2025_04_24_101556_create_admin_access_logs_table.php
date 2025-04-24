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
        Schema::create('admin_access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->comment('后台用户ID');
            $table->string('ip', 20)->comment('请求IP');
            $table->string('url')->comment('请求URL');
            $table->string('source', 30)->comment('请求来源')->index();
            $table->string('method', 10)->comment('请求方式')->index();
            $table->string('referer_url')->comment('来源URL');
            $table->text('user_agent')->comment('用户代理');
            $table->string('browser')->comment('浏览器');
            $table->string('system')->comment('请求系统');
            $table->json('request_data')->comment('请求数据');
            $table->dateTime('access_datetime')->comment('访问时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_access_logs');
    }
};
