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
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->comment('登录用户名')->unique();
            $table->string('password')->comment('登录密码');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('phone')->comment('手机号')->unique();
            $table->string('job_no')->nullable()->comment('工号');
            $table->boolean('status')->default(1)->comment('状态：1启用 0禁用');
            $table->dateTime('latest_login_time')->nullable()->comment('最新登录时间');
            $table->string('latest_login_ip')->nullable()->comment('最新登录IP');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
