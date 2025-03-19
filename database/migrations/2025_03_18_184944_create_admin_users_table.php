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
            $table->string('phone')->comment('手机号')->unique();
            $table->string('avatar')->comment('头像');
            $table->string('password')->comment('登录密码');
            $table->string('nickname')->comment('昵称');
            $table->boolean('status')->default(1)->comment('状态：1启用 0禁用');
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
