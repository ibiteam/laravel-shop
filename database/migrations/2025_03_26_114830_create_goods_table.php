<?php

use App\Models\Category;
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
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->comment('分类ID');
            $table->string('no', 80)->comment('商品编号')->unique();
            $table->string('name')->comment('商品标题');
            $table->string('sub_name')->nullable()->comment('商品副标题');
            $table->string('label')->nullable()->comment('商品标签');
            $table->string('image')->comment('商品主图');
            $table->string('unit', 30)->nullable()->comment('商品单位');
            $table->decimal('price', 13)->comment('商品价格');
            $table->integer('integral')->default(0)->comment('积分');
            $table->integer('total')->comment('商品库存');
            $table->integer('sales_volume')->default(0)->comment('销量');
            $table->tinyInteger('type')->comment('库存类型 1下单减库存 2付款减库存');
            $table->boolean('status')->default(1)->comment('上下架状态 1上架 0下架');
            $table->timestamp('status_datetime')->nullable()->comment('上下架时间');
            $table->boolean('can_quota')->default(0)->comment('是否限购 1限购 0不限购');
            $table->integer('quota_number')->default(0)->comment('限购数量');
            $table->smallInteger('sort')->default(0)->comment('排序，值越大越靠前');
            $table->string('video')->nullable()->comment('视频地址');
            $table->integer('video_duration')->default(0)->comment('视频时长');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on((new Category)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};
