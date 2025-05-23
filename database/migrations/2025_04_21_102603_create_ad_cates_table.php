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
        Schema::create('ad_cates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('广告分类名称');
            $table->string('alias')->default('')->comment('别名');
            $table->integer('width')->default(0)->comment('宽度');
            $table->integer('height')->default(0)->comment('高度');
            $table->tinyInteger('type')->default(1)->comment('类型 1、移动端 2、PC端');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_cates');
    }
};
