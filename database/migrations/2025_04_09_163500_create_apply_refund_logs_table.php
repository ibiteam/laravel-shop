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
        Schema::create('apply_refund_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apply_refund_id')->comment('申请售后id')->index();
            $table->string('action_name')->nullable()->comment('操作人');
            $table->string('action')->nullable()->comment('操作行为');
            $table->tinyInteger('type')->comment('类型：0买方 1卖方');
            $table->integer('apply_refund_ship_id')->default(0)->comment('售后物流ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apply_refund_logs');
    }
};
