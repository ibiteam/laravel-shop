<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->comment('父级ID');
            $table->integer('code')->comment('行政区划代码');
            $table->string('name')->comment('区域名称');
            $table->tinyInteger('type')->comment('区域类型 1、省 2、市 3、区');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `user_addresses` COMMENT '区域表'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
