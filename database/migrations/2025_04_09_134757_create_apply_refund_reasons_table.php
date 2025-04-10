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
        Schema::create('apply_refund_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('content')->comment('内容');
            $table->tinyInteger('type')->comment('类型 0:退仅款；1退货退款');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apply_refund_reasons');
    }
};
