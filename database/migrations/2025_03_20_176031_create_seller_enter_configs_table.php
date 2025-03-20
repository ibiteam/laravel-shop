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
        Schema::create('seller_enter_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->string('type')->comment('组件类型');
            $table->integer('sort')->default(0)->comment('排序，越大越靠前');
            $table->tinyInteger('is_need')->default(1)->comment('是否必须');
            $table->tinyInteger('is_show')->default(1)->comment('是否显示');
            $table->string('limit_type')->nullable()->comment('限制类型');
            $table->integer('limit_number')->default(0)->comment('限制数量');
            $table->text('select_options')->nullable()->comment('下拉选');
            $table->string('template_name')->nullable()->comment('模板名称');
            $table->string('template_url')->nullable()->comment('模板地址');
            $table->string('tips')->nullable()->comment('提示文字');
            $table->integer('style_type')->nullable()->comment('展示样式');
            $table->integer('style')->nullable()->comment('样式效果');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_enter_configs');
    }
};
