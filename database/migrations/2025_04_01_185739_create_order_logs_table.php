<?php

use App\Models\Order;
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
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('订单ID')->index();
            $table->string('operate_type')->comment('类型');
            $table->integer('operate_type_id')->comment('类型ID');
            $table->tinyInteger('type')->comment('可见类型 1用户 2管理员');
            $table->tinyInteger('order_status')->comment('订单状态')->index();
            $table->tinyInteger('pay_status')->comment('支付状态')->index();
            $table->tinyInteger('ship_status')->comment('发货状态')->index();
            $table->string('comment')->comment('操作描述');

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on((new Order)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_logs');
    }
};
