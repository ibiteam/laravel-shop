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
        Schema::create('shop_configs', function (Blueprint $table) {
            $table->id();
            $table->string('group_name')->comment('分组名称')->index();
            $table->string('code', 50)->comment('标识')->unique();
            $table->json('value')->nullable()->comment('值');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_configs');
    }
};
