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
        Schema::create('access_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('statistic_date')->comment('统计日期');
            $table->string('referer')->nullable()->comment('访问来源');
            $table->integer('pv_number')->default(0)->comment('访问量');
            $table->integer('uv_number')->default(0)->comment('独立访问人数');
            $table->integer('ip_number')->default(0)->comment('独立访问IP数');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_statistics');
    }
};
