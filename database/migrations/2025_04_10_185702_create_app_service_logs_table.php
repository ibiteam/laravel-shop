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
        Schema::create('app_service_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id')->comment('服务id');
            $table->longText('request_param')->nullable()->comment('请求参数');
            $table->longText('result_data')->nullable()->comment('返回数据');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_service_logs');
    }
};
