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
        Schema::create('goods_sku_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id')->comment('入驻商家ID');
            $table->string('name')->comment('模板名称');
            $table->json('values')->comment('模板值');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_sku_templates');
    }
};
