<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Dao\GoodsSkuTemplateDao;
use App\Models\AdminOperationLog;
use App\Models\GoodsSkuTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GoodsSkuTemplateController extends BaseController
{
    /**
     * 下拉商品规格模板列表.
     */
    public function smallIndex(Request $request, GoodsSkuTemplateDao $goods_sku_template_dao): JsonResponse
    {
        return $this->success($goods_sku_template_dao->list());
    }

    /**
     * 添加商品规格模板
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'values' => 'required|array',
                'values.*.name' => 'required|string',
                'values.*.values' => 'required|array',
                'values.*.values.*.name' => 'required|string',
            ], [], [
                'name' => '名称',
                'values' => '商品规格',
                'values.*.name' => '参数名称',
                'values.*.values' => '参数值',
                'values.*.values.*.name' => '参数名称',
            ]);
            $goods_sku_template = GoodsSkuTemplate::query()->create([
                'name' => $validated['name'],
                'values' => $validated['values'],
            ]);
            admin_operation_log( "添加了商品规格模板：{$goods_sku_template->name}[{$goods_sku_template->id}]", AdminOperationLog::TYPE_STORE);

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
     * 修改商品规格模板
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'name' => 'nullable|string',
                'values' => 'nullable|array',
                'values.*.name' => 'required|string',
                'values.*.values' => 'required|array',
                'values.*.values.*.name' => 'required|string',
            ], [], [
                'id' => '商品规格模板ID',
                'name' => '名称',
                'values' => '商品规格',
                'values.*.name' => '参数名称',
                'values.*.values' => '参数值',
                'values.*.values.*.name' => '参数名称',
            ]);

            $goods_sku_template = GoodsSkuTemplate::query()->whereId($validated['id'])->first();

            if (! $goods_sku_template instanceof GoodsSkuTemplate) {
                throw new BusinessException('商品规格模板不存在');
            }
            $update_data = [];

            if (isset($validated['name']) && $validated['name']) {
                $update_data['name'] = $validated['name'];
            }

            if (! empty($validated['values'] ?? [])) {
                $update_data['values'] = $validated['values'];
            }

            if (! $goods_sku_template->update($update_data)) {
                throw new BusinessException('修改失败');
            }

            admin_operation_log( "修改了商品规格模板：{$goods_sku_template->name}[{$goods_sku_template->id}]", AdminOperationLog::TYPE_UPDATE);

            return $this->success('修改成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 删除商品规格模板
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '商品规格模板ID',
            ]);
            $goods_sku_template = GoodsSkuTemplate::query()->whereId($validated['id'])->first();

            if (! $goods_sku_template instanceof GoodsSkuTemplate) {
                throw new BusinessException('商品规格模板不存在');
            }

            if (! $goods_sku_template->delete()) {
                throw new BusinessException('删除失败');
            }
            admin_operation_log( "删除了商品规格模板：{$goods_sku_template->name}[{$goods_sku_template->id}]", AdminOperationLog::TYPE_DESTROY);

            return $this->success('删除成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
