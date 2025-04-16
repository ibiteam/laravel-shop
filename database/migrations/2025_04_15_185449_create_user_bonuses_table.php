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
        Schema::create('user_bonuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bonus_id')->comment('红包id');
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->unsignedBigInteger('order_id')->comment('订单id');
            $table->timestamp('used_time')->nullable()->comment('使用时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bonuses');
    }
};
