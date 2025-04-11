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
        Schema::create('app_service_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('名称');
            $table->string('alias')->comment('别名');
            $table->text('config')->comment('配置');
            $table->tinyInteger('is_enable')->comment('是否启用 0 不启用 1 启用');
            $table->tinyInteger('is_record')->comment('是否记录 0 不记录 1 记录');
            $table->integer('error_number')->comment('请求异常数量');
            $table->integer('stop_number')->comment('请求停止数量');
            $table->string('desc')->nullable()->comment('描述');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_service_configs');
    }
};
