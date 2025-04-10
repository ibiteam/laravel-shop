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
        Schema::create('apply_refunds', function (Blueprint $table) {
            $table->id();
            $table->string('no')->comment('售后编号')->unique();
            $table->unsignedBigInteger('user_id')->comment('用户ID')->index();
            $table->integer('order_id')->comment('订单id')->index();
            $table->integer('order_detail_id')->comment('订单明细id')->index();
            $table->tinyInteger('type')->comment('类型');
            $table->tinyInteger('status')->comment('售后状态');
            $table->decimal('money')->default(0)->comment('退款金额');
            $table->decimal('number', 10)->default(0)->comment('退款数量');
            $table->integer('reason_id')->default(0)->comment('退款原因表ID');
            $table->string('description')->nullable()->comment('补充描述');
            $table->text('certificate')->nullable()->comment('退款凭证,号分割');
            $table->tinyInteger('is_revoke')->default(0)->comment('是否撤销：0否 1是');
            $table->integer('count')->default(0)->comment('申请次数');
            $table->string('result')->nullable()->comment('结果');
            $table->dateTime('job_time')->nullable()->comment('定时任务执行时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apply_refunds');
    }
};
