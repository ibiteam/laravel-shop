<?php

use App\Models\Order;
use App\Models\ShipCompany;
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
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_no')->comment('运单号')->unique();
            $table->unsignedBigInteger('order_id')->comment('订单ID')->index();
            $table->unsignedBigInteger('ship_company_id')->comment('快递公司ID');
            $table->string('ship_no')->comment('快递单号')->index();
            $table->tinyInteger('status')->comment('状态');
            $table->dateTime('shipped_at')->comment('发货时间');
            $table->dateTime('received_at')->nullable()->comment('收货时间');
            $table->string('remark')->comment('备注');
            $table->unsignedBigInteger('admin_user_id')->comment('操作人ID');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on((new Order)->getTable());
            $table->foreign('ship_company_id')->references('id')->on((new ShipCompany)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
};
