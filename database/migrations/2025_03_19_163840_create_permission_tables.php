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
        $table_names = config('permission.table_names');
        $column_names = config('permission.column_names');
        $pivot_role = $column_names['role_pivot_key'];
        $pivot_permission = $column_names['permission_pivot_key'];

        if (empty($table_names)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($table_names['permissions'], static function (Blueprint $table) {
            // $table->engine('InnoDB');
            $table->bigIncrements('id'); // permission id
            $table->integer('parent_id')->comment('父级ID')->index();
            $table->string('name')->comment('权限CODE')->unique();
            $table->string('guard_name')->comment('分组名称');
            $table->string('display_name')->comment('中文展示名称');
            $table->string('icon')->nullable()->comment('图标');
            $table->integer('sort')->comment('排序:value越大越靠前');
            $table->boolean('is_left_nav')->default(0)->comment('是否在左侧导航栏 1是 0否')->index();
            $table->timestamps();
        });

        Schema::create($table_names['roles'], static function (Blueprint $table) {
            $table->bigIncrements('id'); // role id
            $table->string('name')->comment('角色CODE')->unique();
            $table->string('guard_name')->comment('分组名称');
            $table->string('display_name')->comment('中文展示名称');
            $table->boolean('is_super_role')->default(0)->comment('是否是超级角色 1是 0否');
            $table->boolean('is_show')->default(1)->comment('是否显示 1是 0否');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create($table_names['model_has_permissions'], static function (Blueprint $table) use ($table_names, $column_names, $pivot_permission) {
            $table->unsignedBigInteger($pivot_permission);

            $table->string('model_type');
            $table->unsignedBigInteger($column_names['model_morph_key']);
            $table->index([$column_names['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivot_permission)
                ->references('id') // permission id
                ->on($table_names['permissions'])
                ->onDelete('cascade');
            $table->primary([$pivot_permission, $column_names['model_morph_key'], 'model_type'], 'model_has_permissions_permission_model_type_primary');
        });

        Schema::create($table_names['model_has_roles'], static function (Blueprint $table) use ($table_names, $column_names, $pivot_role) {
            $table->unsignedBigInteger($pivot_role);

            $table->string('model_type');
            $table->unsignedBigInteger($column_names['model_morph_key']);
            $table->index([$column_names['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivot_role)
                ->references('id') // role id
                ->on($table_names['roles'])
                ->onDelete('cascade');
            $table->primary([$pivot_role, $column_names['model_morph_key'], 'model_type'], 'model_has_roles_role_model_type_primary');
        });

        Schema::create($table_names['role_has_permissions'], static function (Blueprint $table) use ($table_names, $pivot_role, $pivot_permission) {
            $table->unsignedBigInteger($pivot_permission);
            $table->unsignedBigInteger($pivot_role);

            $table->foreign($pivot_permission)
                ->references('id') // permission id
                ->on($table_names['permissions'])
                ->onDelete('cascade');

            $table->foreign($pivot_role)
                ->references('id') // role id
                ->on($table_names['roles'])
                ->onDelete('cascade');

            $table->primary([$pivot_permission, $pivot_role], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table_names = config('permission.table_names');

        if (empty($table_names)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::drop($table_names['role_has_permissions']);
        Schema::drop($table_names['model_has_roles']);
        Schema::drop($table_names['model_has_permissions']);
        Schema::drop($table_names['roles']);
        Schema::drop($table_names['permissions']);
    }
};
