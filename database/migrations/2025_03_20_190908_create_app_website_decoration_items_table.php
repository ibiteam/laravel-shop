<?php

use App\Models\AppWebsiteDecoration;
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
        Schema::create('app_website_decoration_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_website_decoration_id')->comment('网站装修ID');
            $table->string('name',60)->comment('板块名称');
            $table->string('component_name',60)->comment('组件名称');
            $table->boolean('is_show')->comment('是否展示 1、展示 0、不展示');
            $table->integer('sort')->default(0)->comment('排序');
            $table->longText('content')->comment('组件内容 json格式');
            $table->boolean('is_fixed_assembly')->default('0')->comment('是否是固定组件 1是 0否');
            $table->timestamps();

            $table->foreign('app_website_decoration_id')->references('id')->on((new AppWebsiteDecoration())->getTable());
        });
        DB::statement("ALTER TABLE `app_website_decoration_items` COMMENT '移动端装修组件表'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_website_decoration_items');
    }
};
