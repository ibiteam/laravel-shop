<?php

use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no', 50)->comment('订单编号')->unique();
            $table->unsignedBigInteger('user_id')->comment('用户ID')->index();
            $table->string('type', 50)->comment('订单类型')->index();
            $table->tinyInteger('order_status')->comment('订单状态')->index();
            $table->tinyInteger('pay_status')->comment('支付状态')->index();
            $table->dateTime('paid_at')->nullable()->comment('最新支付时间');
            $table->tinyInteger('ship_status')->comment('发货状态')->index();
            $table->boolean('is_edit_address')->default(0)->comment('是否修改过收货地址');
            $table->dateTime('shipped_at')->nullable()->comment('最新发货时间');
            $table->dateTime('received_at')->nullable()->comment('最新收货时间');
            $table->unsignedBigInteger('province_id')->comment('省份ID');
            $table->unsignedBigInteger('city_id')->comment('城市ID');
            $table->unsignedBigInteger('district_id')->comment('区县ID');
            $table->string('address')->comment('详细地址');
            $table->string('consignee')->comment('收货人');
            $table->string('phone', 11)->comment('收货人手机号');
            $table->decimal('goods_amount', 15)->comment('订单商品金额');
            $table->decimal('order_amount', 15)->comment('订单总金额');
            $table->decimal('shipping_fee', 15)->comment('运费');
            $table->integer('integral')->default(0)->comment('消耗积分数量');
            $table->decimal('coupon_amount', 15)->comment('优惠劵金额');
            $table->unsignedBigInteger('coupon_id')->default(0)->comment('优惠券ID');
            $table->string('payment_method',30)->comment('支付方式');
            $table->decimal('money_paid', 15)->default(0)->comment('已支付金额');
            $table->string('remark')->comment('用户备注');
            $table->string('cancel_reason')->nullable()->comment('取消原因');
            $table->string('source', 30)->comment('下单来源');
            $table->string('ip', 20)->comment('下单IP');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on((new User)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
