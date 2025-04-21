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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name', 100)->comment('用户名')->unique();
            $table->string('password')->comment('密码');
            $table->string('nickname', 100)->comment('昵称');
            $table->string('phone', 11)->comment('手机号')->unique();
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('register_ip', 20)->comment('注册IP');
            $table->boolean('is_modify')->default(0)->comment('是否已修改用户名');
            $table->bigInteger('integral')->default(0)->comment('积分总数');
            $table->string('source', 30)->comment('来源')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
