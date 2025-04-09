<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no')->comment('支付流水号')->unique();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父交易ID');
            $table->string('transaction_type', 30)->comment('交易类型:pay:支付,refund:退款');
            $table->string('type')->comment('交易业务类型');
            $table->integer('type_id')->comment('交易业务ID');
            $table->unsignedBigInteger('payment_id')->comment('支付方式ID');
            $table->decimal('amount', 15)->comment('支付金额');
            $table->string('remark')->comment('支付备注');
            $table->tinyInteger('status')->comment('状态:0待处理,1处理成功');
            $table->dateTime('paid_at')->nullable()->comment('支付完成时间');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on((new User)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
