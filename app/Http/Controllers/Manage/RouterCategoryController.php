<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Models\AdminOperationLog;
use App\Models\Permission;
use App\Models\Router;
use App\Models\RouterCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// 访问地址分类
class RouterCategoryController extends BaseController
{
    /**
     * 列表.
     */
    public function index(Request $request)
    {
        $name = $request->get('name');
        $alias = $request->get('alias');
        $is_show = intval($request->get('is_show'));

        $data = RouterCategory::query()
            ->when($name, fn ($query) => $query->where('name', 'like', '%'.$name.'%'))
            ->when($alias, fn ($query) => $query->where('alias', 'like', '%'.$alias.'%'))
            ->when($is_show > -1, fn ($query) => $query->where('is_show', '=', $is_show))
            ->with('allChildren')->whereParentId(0)
            ->orderByDesc('id')
            ->get();

        return $this->success($data);
    }

    /**
     * 获取信息.
     */
    public function info(Request $request)
    {
        $id = $request->get('id') ?? 0;

        // 分类（顶级+可以选下级的）
        $top_categories = RouterCategory::query()->whereParentId(0)
            ->whereType(RouterCategory::TYPE_PAGE)  // 页面类型
            ->selectRaw('id AS value,name AS label')
            ->get()->toArray();
        array_unshift($top_categories, ['value' => 0, 'label' => '顶级分类']);

        // 获取页面权限
        $page_permissions = Permission::query()->where('parent_id', '>', 0)
            ->whereIsLeftNav(Permission::IS_LEFT_NAV)
            ->whereDoesntHave('childrens')
            ->select(['id', 'name', 'display_name'])->limit(5)
            ->get()->toArray();

        $info = null;

        if ($id) {
            $info = RouterCategory::query()->whereId($id)->first();

            if (! $info instanceof RouterCategory) {
                return $this->error('数据不存在');
            }
            $info->type = strval($info->type);

            if ($info->page_name) {
                $page_perm = Permission::query()->whereName($info->page_name)->first();

                if ($page_perm && ! in_array($page_perm->id, array_column($page_permissions, 'id'))) {
                    array_unshift($page_permissions, ['id' => $page_perm->id, 'name' => $page_perm->name, 'display_name' => $page_perm->display_name]);
                }
            }
        }

        return $this->success([
            'top_categories' => $top_categories,
            'page_permissions' => $page_permissions,
            'info' => $info,
        ]);
    }

    /**
     * 获取权限页面.
     */
    public function getPages(Request $request)
    {
        $keywords = $request->get('keywords', '');

        $query = Permission::query()->where('parent_id', '>', 0)
            ->whereIsLeftNav(Permission::IS_LEFT_NAV)
            ->whereDoesntHave('childrens')
            ->select(['id', 'name', 'display_name'])
            ->orderByDesc('id');

        if ($keywords) {
            $query->where(function ($query) use ($keywords) {
                $query->where('name', 'like', '%'.$keywords.'%')->orWhere('display_name', 'like', '%'.$keywords.'%');
            });
        } else {
            $query->limit(10);
        }

        $permissions = $query->get()->toArray();

        return $this->success($permissions);
    }

    /**
     * 添加/编辑.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'parent_id' => 'required|integer',
                'name' => 'required|string',
                'alias' => 'required|string',
                'type' => 'required|in:1,2',
                'page_name' => 'nullable|string',
                'is_show' => 'required|boolean',
            ], [], [
                'id' => '分类ID',
                'name' => '分类名称',
                'alias' => '分类别名',
                'type' => '类型',
                'page_name' => '页面名称',
                'is_show' => '是否显示',
            ]);

            $validated['id'] = $validated['id'] ?? 0;

            if ($validated['id']) {
                $router_category = RouterCategory::whereId($validated['id'])->first();

                if (! $router_category) {
                    throw new BusinessException('访问地址分类不存在');
                }

                if (RouterCategory::where('id', '!=', $validated['id'])->whereName($validated['name'])->first()) {
                    throw new BusinessException('访问地址分类名称已存在');
                }

                if (RouterCategory::where('id', '!=', $validated['id'])->whereAlias($validated['alias'])->first()) {
                    throw new BusinessException('访问地址分类别名已存在');
                }
            } else {
                $router_category = new RouterCategory;

                if (RouterCategory::whereName($validated['name'])->first()) {
                    throw new BusinessException('访问地址分类名称已存在');
                }

                if (RouterCategory::where('id', '!=', $validated['id'])->whereAlias($validated['alias'])->first()) {
                    throw new BusinessException('访问地址分类别名已存在');
                }
            }

            if ($validated['type'] == RouterCategory::TYPE_PAGE && $validated['parent_id'] > 0 && ! $validated['page_name'] ?? '') {
                throw new BusinessException('访问地址分类下级是页面时, 页面名称必须存在');
            }

            $router_category->parent_id = $validated['parent_id'];
            $router_category->name = $validated['name'];
            $router_category->alias = $validated['alias'];
            $router_category->type = $validated['type'];
            $router_category->page_name = $validated['page_name'] ?? '';
            $router_category->is_show = intval($validated['is_show']);

            if (! $router_category->save()) {
                throw new BusinessException('保存失败');
            }

            if ($validated['id']) {
                $log = "编辑访问地址分类[id:{$router_category->id}]".implode(',', array_map(function ($k, $v) {
                        return sprintf('%s=`%s`', $k, $v);
                    }, array_keys($router_category->getChanges()), $router_category->getChanges()));
                admin_operation_log($this->adminUser(), $log, AdminOperationLog::TYPE_UPDATE);
            } else {
                $log = "新增访问地址分类[id:{$router_category->id}]";
                admin_operation_log($this->adminUser(), $log, AdminOperationLog::TYPE_STORE);
            }

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('访问地址分类操作异常~');
        }
    }

    /**
     * 删除.
     */
    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '分类ID',
            ]);
            $route_category = RouterCategory::query()->whereId($validated['id'])->first();

            if (! $route_category instanceof RouterCategory) {
                return $this->error('数据不存在');
            }

            if (RouterCategory::query()->whereParentId($route_category->id)->exists()) {
                return $this->error('请先删除子分类');
            }

            if (Router::query()->whereRouterCategoryId($route_category->id)->exists()) {
                return $this->error('请先删除该分类下的地址');
            }

            if ($route_category->delete()) {
                // 记录日志
                admin_operation_log($this->adminUser(), "删除了访问地址分类:{$route_category->name}[{$route_category->id}]", AdminOperationLog::TYPE_DESTROY);

                return $this->success('删除成功');
            }

            throw new BusinessException('删除失败');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 切换是否显示.
     */
    public function changeShow(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'is_show' => 'required|integer|in:0,1',
            ], [], [
                'id' => '访问地址分类ID',
                'is_show' => '是否显示',
            ]);

            $router_category = RouterCategory::query()->whereId($validated['id'])->first();

            if (! $router_category) {
                throw new BusinessException('访问地址分类不存在');
            }

            $router_category->is_show = $validated['is_show'];

            if (! $router_category->save()) {
                throw new BusinessException('切换失败');
            }

            $log = "更改访问地址分类显示隐藏[id:{$validated['id']}]".implode(
                    ',',
                    array_map(function ($k, $v) {
                        return sprintf('%s=`%s`', $k, $v);
                    }, array_keys($router_category->getChanges()), $router_category->getChanges())
                );
            admin_operation_log($this->adminUser(), $log, AdminOperationLog::TYPE_UPDATE);

            return $this->success('切换成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('切换是否展示异常~');
        }
    }
}
