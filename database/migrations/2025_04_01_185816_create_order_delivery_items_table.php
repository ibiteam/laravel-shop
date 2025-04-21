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
        Schema::create('order_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_delivery_id')->comment('发货订单ID');
            $table->unsignedBigInteger('order_detail_id')->comment('订单明细ID');
            $table->integer('send_number')->comment('发货数量');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_delivery_items');
    }
};
