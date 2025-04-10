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
        Schema::create('apply_refund_ships', function (Blueprint $table) {
            $table->id();
            $table->integer('apply_refund_id')->comment('申请退款ID');
            $table->string('no')->comment('物流单号');
            $table->integer('ship_company_id')->comment('物流公司ID');
            $table->string('phone')->nullable()->comment('手机号');
            $table->string('description')->nullable()->comment('描述说明');
            $table->string('certificate')->nullable()->comment('凭证 ,号分割');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apply_refund_ships');
    }
};
