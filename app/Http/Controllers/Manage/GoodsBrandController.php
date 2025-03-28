<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Resources\CommonResource;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\Brand;
use App\Models\Goods;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class GoodsBrandController extends BaseController
{
    /**
     * 品牌列表.
     */
    public function index(Request $request): JsonResponse
    {
        $is_show = $request->get('is_show', -1);
        $number = (int) $request->get('number', 10);
        $list = Brand::query()->latest()
            ->when($name = $request->get('name'), fn (Builder $query) => $query->whereLike('name', "%{$name}%"))
            ->when($is_show >= 0, fn (Builder $query) => $query->where('is_show', $is_show))
            ->paginate($number);

        return $this->success(new CommonResourceCollection($list));
    }

    /**
     * 编辑品牌.
     */
    public function edit(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '品牌ID',
            ]);
            $brand = Brand::query()->whereId($validated['id'])->select(['id', 'name', 'logo', 'sort', 'is_show'])->first();

            if (! $brand instanceof Brand) {
                throw new BusinessException('品牌不存在');
            }

            return $this->success(new CommonResource($brand));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取失败');
        }
    }

    /**
     * 更新|新增品牌.
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'name' => 'required|string|max:50',
                'logo' => 'required|string|url',
                'sort' => 'required|integer',
                'is_show' => 'required|boolean',
            ], [], [
                'id' => '品牌ID',
                'name' => '品牌名称',
                'logo' => '品牌LOGO',
                'sort' => '排序',
                'is_show' => '是否显示',
            ]);
            $tmp_data = Arr::only($validated, ['name', 'logo', 'sort', 'is_show']);

            if ($validated['id'] > 0) {
                $brand = Brand::query()->whereId($validated['id'])->first();

                if (! $brand instanceof Brand) {
                    throw new BusinessException('品牌不存在');
                }

                if (Brand::query()->whereName($validated['name'])->where('id', '<>', $validated['id'])->exists()) {
                    throw new BusinessException('品牌名称已存在');
                }

                if (! $brand->update($tmp_data)) {
                    throw new BusinessException('更新失败');
                }
                admin_operation_log($this->adminUser(), "修改了商品品牌：{$brand->name}[{$brand->id}]", AdminOperationLog::TYPE_UPDATE);

                return $this->success('修改成功');
            }

            if (Brand::query()->whereName($validated['name'])->exists()) {
                throw new BusinessException('品牌名称已存在');
            }
            $brand = Brand::query()->create($tmp_data);
            admin_operation_log($this->adminUser(), "添加了商品品牌：{$brand->name}[{$brand->id}]", AdminOperationLog::TYPE_STORE);

            return $this->success('添加成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 删除品牌.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '品牌ID',
            ]);
            $brand = Brand::query()->whereId($validated['id'])->select(['id', 'name', 'logo', 'sort', 'is_show'])->first();

            if (! $brand instanceof Brand) {
                throw new BusinessException('品牌不存在');
            }

            // 判断商品中是否使用品牌
            if (Goods::query()->whereBrandId($brand->id)->exists()) {
                throw new BusinessException('品牌正在被使用，无法删除');
            }

            if ($brand->delete()) {
                admin_operation_log($this->adminUser(), "删除了商品品牌：{$brand->name}[{$brand->id}]", AdminOperationLog::TYPE_DESTROY);

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
