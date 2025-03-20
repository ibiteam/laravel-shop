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
        Schema::create('phone_msgs', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->comment('手机号');
            $table->string('code', 6)->comment('短信验证码');
            $table->boolean('type')->comment('短信类型');
            $table->string('start_time', 11)->comment('开始时间');
            $table->string('end_time', 11)->comment('结束时间');
            $table->string('ip');
            $table->string('info')->nullable()->comment('描述');
            $table->boolean('status')->default(false)->comment('短信验证码状态 默认为0 验证了为1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_msgs');
    }
};
