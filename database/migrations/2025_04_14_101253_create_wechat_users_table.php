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
        Schema::create('wechat_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->comment('用户ID')->index();
            $table->string('unionid', 100)->nullable()->comment('微信 unionid');
            $table->string('openid', 100)->comment('微信 openid')->index();
            $table->string('nickname')->nullable()->comment('昵称');
            $table->string('avatar')->nullable()->comment('头像');
            $table->boolean('is_subscribe')->default(1)->comment('是否关注');
            $table->dateTime('subscribe_time')->comment('关注/取消关注时间');
            $table->string('language')->comment('用户语言');
            $table->string('remark')->comment('备注');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_users');
    }
};
