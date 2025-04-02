<?php

use App\Models\AdminUser;
use App\Models\AppDecoration;
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
        Schema::create('app_decoration_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_decoration_id')->comment('装修页面ID');
            $table->unsignedBigInteger('admin_user_id')->comment('保存人');
            $table->string('app_decoration_item_ids')->comment('组件ID合集');
            $table->timestamps();

            $table->foreign('app_decoration_id')->references('id')->on((new AppDecoration)->getTable());
            $table->foreign('admin_user_id')->references('id')->on((new AdminUser)->getTable());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_decoration_logs');
    }
};
