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
        Schema::create('material_files', function (Blueprint $table) {
            $table->id();
            $table->string('admin_user_id')->comment('添加人');
            $table->string('name')->comment('素材名');
            $table->bigInteger('size')->nullable()->comment('素材大小');
            $table->integer('width')->nullable()->comment('素材宽度');
            $table->integer('height')->nullable()->comment('素材高度');
            $table->string('file_path')->nullable()->comment('素材地址');
            $table->tinyInteger('type')->default(1)->comment('素材类型 1、文件夹 2、素材');
            $table->integer('parent_id')->default(0)->comment('父级ID');
            $table->tinyInteger('dir_type')->default(1)->comment('1、图片，2、视频');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_files');
    }
};
