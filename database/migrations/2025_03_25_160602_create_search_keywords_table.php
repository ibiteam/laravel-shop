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
        Schema::create('search_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keywords')->comment('关键字');
            $table->integer('count')->default(1)->comment('搜索数量');
            $table->tinyInteger('is_can_search')->default(0)->comment('是否可查询');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_keywords');
    }
};
