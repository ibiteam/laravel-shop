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
        Schema::table('apply_refund_logs', function (Blueprint $table) {
            $table->json('apply_refund_data')->nullable()->comment('当前的申请售后数据')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apply_refund_logs', function (Blueprint $table) {
            $table->dropColumn('apply_refund_data');
        });
    }
};
