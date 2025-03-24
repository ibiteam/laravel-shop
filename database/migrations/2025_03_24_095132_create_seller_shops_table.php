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
        Schema::create('seller_shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id')->comment('入驻商家id')->index();
            $table->string('name')->comment('名称')->index();
            $table->string('logo')->comment('Logo');
            $table->string('title')->nullable()->comment('标题');
            $table->string('keyword')->nullable()->comment('关键字');
            $table->string('description')->nullable()->comment('描述');
            $table->tinyInteger('status')->default(1)->comment('店铺状态：0关闭,1开启');
            $table->integer('country')->nullable()->comment('所在国家');
            $table->integer('province')->nullable()->comment('所在省份');
            $table->integer('city')->nullable()->comment('所在城市');
            $table->string('address')->nullable()->comment('详细地址');
            $table->string('ship_address')->nullable()->comment('发货地址');
            $table->string('main_cate')->nullable()->comment('主营类目');
            $table->string('kf_phone')->nullable()->comment('客服电话');
            $table->string('leader_name')->nullable()->comment('负责人姓名');
            $table->string('leader_position')->nullable()->comment('负责人职位');
            $table->string('leader_phone')->nullable()->comment('负责人联系方式');
            $table->string('leader_email')->nullable()->comment('负责人邮箱');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_shops');
    }
};
