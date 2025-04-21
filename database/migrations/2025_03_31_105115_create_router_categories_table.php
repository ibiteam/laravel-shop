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
        Schema::create('router_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->default(0)->comment('父级id')->index();
            $table->string('name')->comment('名称');
            $table->string('alias')->comment('别名');
            $table->tinyInteger('type')->comment('类型');
            $table->string('page_name')->nullable()->comment('页面名称');
            $table->tinyInteger('is_show')->default(1)->comment('是否显示：1显示 2隐藏');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('router_categories');
    }
};
