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
        Schema::create('ip_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 20)->comment('IP');
            $table->string('country_ad_code')->comment('国家AdCode');
            $table->string('province_ad_code')->comment('省份AdCode');
            $table->string('city_ad_code')->comment('城市AdCode');
            $table->string('district_ad_code')->comment('区县AdCode');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_addresses');
    }
};
