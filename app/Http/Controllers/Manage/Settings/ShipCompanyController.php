<?php

namespace App\Http\Controllers\Manage\Settings;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Manage\BaseController;
use App\Http\Resources\CommonResource;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\ShipCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class ShipCompanyController extends BaseController
{
    /**
     * 快递公司列表.
     */
    public function index(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $number = (int) $request->get('number', 10);
        $list = ShipCompany::query()
            ->latest()
            ->when($name, fn (Builder $query) => $query->whereLike('name', "%$name%"))
            ->paginate($number);

        return $this->success(new CommonResourceCollection($list));
    }

    /**
     * 编辑快递公司.
     */
    public function edit(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => 'ID',
            ]);
            $ship_company = ShipCompany::query()->whereId($validated['id'])->first();

            if (! $ship_company instanceof ShipCompany) {
                throw new BusinessException('快递公司不存在');
            }

            return $this->success(CommonResource::make($ship_company));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 更新快递公司.
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
                'status' => 'required|boolean',
            ], [], [
                'id' => 'ID',
                'name' => '名称',
                'code' => '别名',
                'status' => '状态',
            ]);
            $store_data = Arr::only($validated, ['name', 'code', 'status']);

            if ($validated['id'] > 0) {
                $ship_company = ShipCompany::query()->whereId($validated['id'])->first();

                if (! $ship_company instanceof ShipCompany) {
                    throw new BusinessException('快递公司不存在');
                }

                if ($store_data['code'] !== $ship_company->code && ShipCompany::query()->whereCode($store_data['code'])->exists()) {
                    throw new BusinessException('快递公司别名已存在');
                }

                if ($store_data['name'] !== $ship_company->name && ShipCompany::query()->whereName($store_data['name'])->exists()) {
                    throw new BusinessException('快递公司名称已存在');
                }

                if (! $ship_company->update($store_data)) {
                    throw new BusinessException('快递公司更新失败');
                }

                admin_operation_log($this->adminUser(), "更新了快递公司：{$ship_company->name}[$ship_company->id]");

                return $this->success('快递公司更新成功');
            }

            if (ShipCompany::query()->whereCode($store_data['code'])->exists()) {
                throw new BusinessException('快递公司别名已存在');
            }

            if (ShipCompany::query()->whereName($store_data['name'])->exists()) {
                throw new BusinessException('快递公司名称已存在');
            }
            $ship_company = ShipCompany::query()->create($store_data);

            admin_operation_log($this->adminUser(), "创建了快递公司：{$ship_company->name}[$ship_company->id]");

            return $this->success('快递公司创建成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 支付方式修改字段.
     */
    public function changeStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => 'ID',
            ]);
            $ship_company = ShipCompany::query()->whereId($validated['id'])->first();

            if (! $ship_company instanceof ShipCompany) {
                throw new BusinessException('快递公司不存在');
            }

            $tmp_message = $ship_company->status ? '由启用变更为禁用' : '由禁用变更为启用';

            if (! $ship_company->update(['status' => ! $ship_company->status])) {
                throw new BusinessException('修改失败！');
            }
            admin_operation_log($this->adminUser(), "修改了支付方式:{$ship_company->name}[{$ship_company->id}]；{$tmp_message}", AdminOperationLog::TYPE_UPDATE);

            return $this->success('修改成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
