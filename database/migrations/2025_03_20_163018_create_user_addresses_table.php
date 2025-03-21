<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->string('recipient_name')->comment('收货人姓名');
            $table->string('recipient_phone')->comment('收货人手机号');
            $table->integer('province')->comment('省份');
            $table->integer('city')->comment('城市');
            $table->integer('district')->comment('区');
            $table->text('address_detail')->comment('详细地址');
            $table->tinyInteger('is_default')->default(0)->comment('是否是默认收货地址 1、默认地址 0、非默认地址');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on((new User)->getTable());
        });
        DB::statement("ALTER TABLE `user_addresses` COMMENT '用户地址表'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
