<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Models\AdminOperationLog;
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

        $data = RouterCategory::query()
            ->when($name, fn ($query) => $query->where('name', 'like', '%'.$name.'%'))
            ->with('allChildren')->whereParentId(0)
            ->orderByDesc('sort')->orderByDesc('created_at')
            ->get();

        return $this->success($data);
    }

    /**
     * 获取信息.
     */
    public function info(Request $request)
    {
        $top_categories = RouterCategory::query()
            ->whereParentId(0)
            ->whereType(RouterCategory::TYPE_PAGE)  // 页面类型 才有下级
            ->selectRaw('id AS value,name AS label')
            ->get()->toArray();
        array_unshift($top_categories, ['value' => 0, 'label' => '顶级分类']);
        $info = null;

        if ($id = $request->get('id')) {
            $info = RouterCategory::query()->whereId($id)->first();

            if (! $info instanceof RouterCategory) {
                return $this->error('数据不存在');
            }
            $info->type = strval($info->type);
        }

        return $this->success([
            'top_categories' => $top_categories,
            'info' => $info,
        ]);
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
                'sort' => 'nullable|integer',
            ], [], [
                'id' => '分类ID',
                'name' => '分类名称',
                'alias' => '分类别名',
                'type' => '类型',
                'page_name' => '页面名称',
                'is_show' => '是否显示',
                'sort' => '排序',
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
            $router_category->sort = $validated['sort'] ?? 0;

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
