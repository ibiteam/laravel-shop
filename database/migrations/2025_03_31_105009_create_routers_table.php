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
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('router_category_id')->default(0)->comment('分类id');
            $table->string('name')->comment('名称');
            $table->string('alias')->comment('别名');
            $table->string('h5_url')->nullable()->comment('h5地址');
            $table->json('params')->nullable()->comment('额外参数');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('is_show')->default(1)->comment('是否显示：1显示 2隐藏');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
