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
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->default(0)->comment('父级id')->index();
            $table->string('name')->comment('名称');
            $table->string('alias')->nullable()->comment('别名');
            $table->string('title')->nullable()->comment('标题');
            $table->string('keywords')->nullable()->comment('关键词');
            $table->string('description')->nullable()->comment('描述');
            $table->tinyInteger('type')->default(1)->comment('类型');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('is_show')->default(1)->comment('是否显示');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_categories');
    }
};
