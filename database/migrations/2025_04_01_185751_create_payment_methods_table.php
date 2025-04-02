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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('名称');
            $table->string('alias', 50)->comment('别名')->unique();
            $table->boolean('is_enabled')->comment('是否启用');
            $table->string('icon')->comment('图标');
            $table->string('description', 255)->comment('描述');
            $table->json('config')->comment('配置信息');
            $table->integer('limit')->comment('限额，负数表示不限额');
            $table->boolean('is_recommend')->comment('是否推荐');
            $table->integer('sort')->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
