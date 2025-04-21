<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Dao\PermissionDao;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 权限菜单.
 */
class PermissionController extends BaseController
{
    /**
     * 列表.
     */
    public function index(Request $request, PermissionDao $permission_dao)
    {
        $keywords = $request->get('keywords') ?: '';

        $all_permissions = $permission_dao->allData($keywords);

        return $this->success($all_permissions);
    }

    /**
     * 编辑.
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'parent_id' => 'required|integer',
                'name' => 'required|string',
                'display_name' => 'required|string',
                'icon' => 'nullable|string',
                'sort' => 'nullable|integer',
            ], [], [
                'id' => '权限ID',
                'parent_id' => '权限分类',
                'name' => '权限值',
                'display_name' => '名称',
                'icon' => '图标',
                'sort' => '排序',
            ]);

            $permission = Permission::whereId($validated['id'])->first();

            if (! $permission) {
                throw new BusinessException('权限不存在');
            }

            $permission->parent_id = $validated['parent_id'];
            $permission->name = $validated['name'];
            $permission->display_name = $validated['display_name'];
            $permission->icon = $validated['icon'] ?? '';
            $permission->sort = $validated['sort'] ?? 0;

            if (! $permission->save()) {
                return $this->error('保存失败');
            }

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('权限操作异常~');
        }
    }
}
