<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Models\AdminOperationLog;
use App\Models\Category;
use App\Models\Goods;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class GoodsCategoryController extends BaseController
{
    /**
     * 商品分类列表.
     */
    public function index(Request $request): JsonResponse
    {
        $data = Category::query()->whereParentId(0)->with('allChildren')->get();

        return $this->success($data);
    }

    /**
     * 商品分类编辑.
     */
    public function edit(Request $request): JsonResponse
    {
        $top_categories = Category::query()->whereParentId(0)->selectRaw('id AS value,name AS label')->get()->toArray();
        array_unshift($top_categories, ['value' => 0, 'label' => '顶级分类']);
        $info = null;

        if ($id = $request->get('id')) {
            $info = Category::query()->whereId($id)->select(['id', 'name', 'title', 'keywords', 'description', 'logo', 'parent_id', 'sort', 'is_show'])->first();

            if (! $info instanceof Category) {
                return $this->error('数据不存在');
            }
        }

        return $this->success([
            'top_categories' => $top_categories,
            'info' => $info,
        ]);
    }

    /**
     * 添加|修改商品分类.
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
     * 删除商品分类.
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
}
