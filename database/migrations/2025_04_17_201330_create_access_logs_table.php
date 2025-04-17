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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->string('ip', 20)->comment('访问IP');
            $table->string('url')->comment('访问URL');
            $table->string('source', 30)->comment('来源')->index();
            $table->string('method', 10)->comment('请求方式')->index();
            $table->string('referer_url')->comment('来源URL');
            $table->string('user_agent')->comment('用户代理');
            $table->string('browser')->comment('浏览器');
            $table->string('system')->comment('系统');
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
        Schema::dropIfExists('access_logs');
    }
};
