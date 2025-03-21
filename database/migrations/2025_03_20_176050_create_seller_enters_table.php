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
        Schema::create('seller_enters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('会员id');
            $table->json('enter_info')->comment('入驻信息');
            $table->string('source')->comment('来源');
            $table->tinyInteger('check_status')->default(0)->comment('审核状态：0未审核，1审核通过，2审核不通过');
            $table->unsignedBigInteger('admin_user_id')->default(0)->comment('审核人');
            $table->string('check_desc')->nullable()->comment('审核说明');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on((new User())->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_enters');
    }
};
