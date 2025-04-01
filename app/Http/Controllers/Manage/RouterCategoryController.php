<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\RouterCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// 路由分类
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
            ->orderByDesc('sort')->orderByDesc('created_at')->paginate();

        $data->getCollection()->transform(function (RouterCategory $router_category) {
            return [
                'id' => $router_category->id,
                'name' => $router_category->name,
                'sort' => $router_category->sort,
                'router_count' => $router_category->routers()->count(),
                'is_show' => $router_category->is_show,
                'created_at' => $router_category->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $router_category->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }

    /**
     * 添加编辑.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'name' => 'required|string',
                'is_show' => 'required|boolean',
                'sort' => 'nullable|integer',
            ], [], [
                'id' => '分类ID',
                'name' => '分类名称',
                'is_show' => '是否显示',
                'sort' => '排序',
            ]);

            $validated['id'] = $validated['id'] ?? 0;

            if ($validated['id']) {
                $router_category = RouterCategory::whereId($validated['id'])->first();

                if (! $router_category) {
                    throw new BusinessException('路由分类不存在');
                }

                if (RouterCategory::where('id', '!=', $validated['id'])->whereName($validated['name'])->first()) {
                    throw new BusinessException('路由分类名称已存在');
                }
            } else {
                $router_category = new RouterCategory;

                if (RouterCategory::whereName($validated['name'])->first()) {
                    throw new BusinessException('路由分类名称已存在');
                }
            }

            $router_category->name = $validated['name'];
            $router_category->is_show = intval($validated['is_show']);
            $router_category->sort = $validated['sort'] ?? 0;

            if (! $router_category->save()) {
                throw new BusinessException('保存失败');
            }

            $log = "新增路由分类[id:{$router_category->id}]";

            if ($validated['id']) {
                $log = "编辑路由分类[id:{$router_category->id}]".implode(',', array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($router_category->getChanges()), $router_category->getChanges()));
            }

            admin_operation_log($this->adminUser(), $log, AdminOperationLog::TYPE_UPDATE);

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('路由分类操作异常~');
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
                'id' => '路由ID',
                'is_show' => '是否显示',
            ]);

            $router_category = RouterCategory::query()->whereId($validated['id'])->first();

            if (! $router_category) {
                throw new BusinessException('路由分类不存在');
            }

            $router_category->is_show = $validated['is_show'];

            if (! $router_category->save()) {
                throw new BusinessException('切换失败');
            }

            $log = "更改路由分类显示隐藏[id:{$validated['id']}]".implode(
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
