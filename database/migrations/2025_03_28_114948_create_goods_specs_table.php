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
        Schema::create('goods_specs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('规格名');
            $table->json('value')->comment('规格值');
            $table->boolean('is_show')->comment('是否展示 1展示 0不展示');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_specs');
    }
};
