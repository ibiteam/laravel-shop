<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Dao\CategoryDao;
use App\Models\AdminOperationLog;
use App\Models\Category;
use App\Models\Goods;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

/**
 * 商品分类.
 */
class GoodsCategoryController extends BaseController
{
    /**
     * 列表.
     */
    public function index(Request $request): JsonResponse
    {
        $data = Category::query()->whereParentId(0)->with('allChildren')->get();
        $vue_app_url = rtrim(config('host.vue_app_url'), '/');
        $data = $data->map(function (Category $category) use ($vue_app_url) {
            $category->setAttribute('h5_url', $vue_app_url.'/category?cat_id='.$category->id);  // 分类h5地址

            return $category;
        })->toArray();

        return $this->success($data);
    }

    /**
     * 获取信息.
     */
    public function edit(Request $request, CategoryDao $category_dao)
    {
        $id = $request->get('id') ?? 0;

        $tree_categories = $category_dao->getTreeList();

        $info = null;

        if ($id) {
            $info = Category::query()->whereId($id)->select(['id', 'name', 'title', 'keywords', 'description', 'logo', 'parent_id', 'sort', 'is_show'])->first();

            if (! $info instanceof Category) {
                return $this->error('分类不存在');
            }
        }

        return $this->success([
            'tree_categories' => $tree_categories,
            'info' => $info,
        ]);
    }

    /**
     * 添加|修改.
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'parent_id' => 'required|integer',
                'name' => 'required|string|max:80',
                'title' => 'required|string|max:80',
                'keywords' => 'required|string|max:80',
                'description' => 'required|string|max:80',
                'logo' => 'required|string|url',
                'sort' => 'required|integer',
                'is_show' => 'required|boolean',
            ], [], [
                'id' => '分类ID',
                'parent_id' => '上级分类',
                'name' => '分类名称',
                'title' => '分类标题',
                'keywords' => '分类关键字',
                'description' => '分类描述',
                'logo' => '分类图标',
                'sort' => '排序',
                'is_show' => '是否显示',
            ]);

            if ($validated['parent_id']) {
                $parent_category = Category::query()->whereId($validated['parent_id'])->first();

                if ($parent_category->parent_id > 0) {
                    throw new BusinessException('仅支持二级分类');
                }
            }
            $tmp_data = Arr::only($validated, ['parent_id', 'name', 'title', 'keywords', 'description', 'logo', 'sort', 'is_show']);

            if ($validated['id'] > 0) {
                $goods_category = Category::query()->whereId($validated['id'])->first();

                if (! $goods_category instanceof Category) {
                    throw new BusinessException('商品分类未找到');
                }

                if ($validated['parent_id']) {
                    // 判断当前分类下是否存在子分类
                    $children_category = Category::query()->whereParentId($goods_category->id)->first();

                    if ($children_category instanceof Category) {
                        throw new BusinessException('二级分类下不能再添加分类');
                    }
                }

                if (! $goods_category->update($tmp_data)) {
                    throw new BusinessException('更新失败');
                }

                // 记录日志
                admin_operation_log($this->adminUser(), "修改了商品分类:{$goods_category->name}[{$goods_category->id}]", AdminOperationLog::TYPE_UPDATE);

                return $this->success([]);
            }

            $goods_category = Category::query()->create($tmp_data);

            // 记录日志
            admin_operation_log($this->adminUser(), "添加了商品分类:{$goods_category->name}[{$goods_category->id}]", AdminOperationLog::TYPE_STORE);

            return $this->success([]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 删除.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '分类ID',
            ]);
            $category = Category::query()->whereId($validated['id'])->first();

            if (! $category instanceof Category) {
                return $this->error('数据不存在');
            }

            if (Category::query()->whereParentId($category->id)->exists()) {
                return $this->error('请先删除子分类');
            }

            if (Goods::query()->whereCategoryId($category->id)->exists()) {
                return $this->error('请先删除该分类下的商品');
            }

            if ($category->delete()) {
                // 记录日志
                admin_operation_log($this->adminUser(), "删除了商品分类:{$category->name}[{$category->id}]", AdminOperationLog::TYPE_DESTROY);

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
                'id' => '分类ID',
                'is_show' => '是否显示',
            ]);

            $category = Category::query()->whereId($validated['id'])->first();

            if (! $category) {
                throw new BusinessException('商品分类不存在');
            }
            $category->is_show = $validated['is_show'];

            if (! $category->save()) {
                throw new BusinessException('切换失败');
            }

            $log = "更改商品分类显示隐藏[id:{$validated['id']}]".implode(
                ',',
                array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($category->getChanges()), $category->getChanges())
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
