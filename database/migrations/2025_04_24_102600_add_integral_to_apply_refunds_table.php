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
        Schema::table('apply_refunds', function (Blueprint $table) {
            $table->integer('integral')->default(0)->comment('退款积分')->after('money');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apply_refunds', function (Blueprint $table) {
            $table->dropColumn('integral');
        });
    }
};
