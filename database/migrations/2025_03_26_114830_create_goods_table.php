<?php

use App\Models\Brand;
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
            $table->unsignedBigInteger('brand_id')->comment('品牌ID');
            $table->string('goods_sn',80)->comment('商品编号')->unique();
            $table->string('goods_name')->comment('商品标题');
            $table->string('goods_sub_name')->comment('商品副标题');
            $table->string('keywords')->comment('商品关键词');
            $table->decimal('price', 13)->comment('商品价格');
            $table->integer('number')->comment('商品库存');
            $table->integer('buy_min_number')->comment('最小起订量');
            $table->boolean('check_status')->default(0)->comment('审核状态 1审核通过 2审核驳回 0未审核');
            $table->dateTime('check_status_datetime')->nullable()->comment('审核时间');
            $table->string('reason')->nullable()->comment('驳回原因');
            $table->boolean('status')->default(1)->comment('上下架状态 1上架 0下架');
            $table->smallInteger('sort')->comment('排序，值越大越靠前');
            $table->string('goods_original')->comment('商品主图');
            $table->string('goods_thumb')->comment('商品缩略图');
            $table->string('video')->nullable()->comment('视频地址');
            $table->integer('video_duration')->default(0)->comment('视频时长');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on((new Category())->getTable());
            $table->foreign('brand_id')->references('id')->on((new Brand())->getTable());
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
