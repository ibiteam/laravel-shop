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
        Schema::create('sensitive_words', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->tinyInteger('type')->comment('类型 1、违禁词  2、广告敏感词');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensitive_words');
    }
};
