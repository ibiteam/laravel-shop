<?php

use App\Models\AdminUser;
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
        Schema::create('admin_user_login_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->comment('管理员ID')->index();
            $table->string('type', 30)->comment('登录类型')->index();
            $table->boolean('status')->comment('登录状态');
            $table->string('description')->comment('登录描述');
            $table->string('ip', 20)->comment('登录IP');
            $table->timestamps();

            $table->foreign('admin_user_id')->references('id')->on((new AdminUser)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_login_logs');
    }
};
