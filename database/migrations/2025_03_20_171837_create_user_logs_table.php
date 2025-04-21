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
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID')->index();
            $table->string('type')->comment('类型');
            $table->string('source', 30)->comment('来源');
            $table->string('ip', 20)->comment('IP');
            $table->string('status')->comment('状态');
            $table->string('description')->comment('描述');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on((new User)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
