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
        Schema::create('ship_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('名称');
            $table->string('code', 50)->comment('编码')->unique();
            $table->boolean('status')->comment('状态:1启用 0禁用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ship_companies');
    }
};
