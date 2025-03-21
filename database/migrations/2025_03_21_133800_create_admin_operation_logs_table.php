<?php

use App\Models\AdminUser;
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
        Schema::create('admin_operation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->comment('管理员id')->index();
            $table->text('description')->nullable()->comment('操作描述');
            $table->tinyInteger('type')->default(0)->comment('类型');
            $table->string('table')->nullable()->comment('表名');
            $table->integer('table_id')->default(0)->comment('表名主键ID');
            $table->string('ip')->comment('操作IP');
            $table->timestamps();

            $table->foreign('admin_user_id')->references('id')->on((new AdminUser())->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_operation_logs');
    }
};
