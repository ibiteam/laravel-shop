<?php

namespace App\Http\Controllers\Manage;

use App\Enums\PaymentMethodEnum;
use App\Exceptions\BusinessException;
use App\Http\Requests\Manage\PaymentMethodRequest;
use App\Http\Resources\CommonResource;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends BaseController
{
    /**
     * 支付方式列表.
     */
    public function index(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $is_enabled = $request->get('is_enabled', null);
        $number = (int) $request->get('number', 10);

        $list = PaymentMethod::query()
            ->latest()
            ->when($name, fn (Builder $query) => $query->whereLike('name', "%{$name}%"))
            ->when(! is_null($is_enabled), fn (Builder $query) => $query->whereIsEnabled($is_enabled))
            ->select(['id', 'name', 'alias', 'is_enabled', 'icon', 'description', 'limit', 'is_recommend', 'sort', 'created_at', 'updated_at'])
            ->paginate($number);

        return $this->success(new CommonResourceCollection($list));
    }

    /**
     * 支付方式编辑回显.
     */
    public function edit(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '支付方式ID',
            ]);
            $payment_method = PaymentMethod::query()
                ->whereId($validated['id'])
                ->select(['id', 'name', 'alias', 'is_enabled', 'icon', 'description', 'limit', 'is_recommend', 'sort', 'config'])
                ->first();

            if (! $payment_method instanceof PaymentMethod) {
                throw new BusinessException('支付方式不存在');
            }

            return $this->success(CommonResource::make($payment_method));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 支付方式更新.
     */
    public function update(PaymentMethodRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $payment_method = PaymentMethod::query()->whereId($validated['id'])->first();

            if (! $payment_method instanceof PaymentMethod) {
                throw new BusinessException('支付方式不存在');
            }
            $update_data = [
                'name' => $validated['name'],
                'is_enabled' => $validated['is_enabled'],
                'icon' => $validated['icon'],
                'description' => $validated['description'] ?? '',
                'config' => [],
                'limit' => $validated['limit'],
                'is_recommend' => $validated['is_recommend'],
                'sort' => $validated['sort'],
            ];

            if ($payment_method->alias == PaymentMethodEnum::WECHAT->value) {
                $update_data['config'] = $this->checkWechatConfig($validated['config']);
            }

            if (! $payment_method->update($update_data)) {
                throw new BusinessException('更新失败！');
            }

            admin_operation_log($this->adminUser(), "更新了支付方式:{$payment_method->name}[{$payment_method->alias}]", AdminOperationLog::TYPE_UPDATE);

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
     * 支付方式修改字段.
     */
    public function changeField(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'field' => 'required|string|in:,is_enabled,is_recommend',
            ], [], [
                'id' => '支付方式ID',
                'field' => '字段',
            ]);
            $payment_method = PaymentMethod::query()->whereId($validated['id'])->first();

            if (! $payment_method instanceof PaymentMethod) {
                throw new BusinessException('支付方式不存在');
            }

            switch ($validated['field']) {
                case 'is_enabled':
                    $data = ['is_enabled' => ! $payment_method->is_enabled];
                    $tmp_message = '将是否启用由'.($payment_method->is_enabled ? '启用变更为禁用' : '禁用变更为启用');

                    break;

                case 'is_recommend':
                    $data = ['is_recommend' => ! $payment_method->is_recommend];
                    $tmp_message = '将是否推荐由'.($payment_method->is_recommend ? '推荐变更为不推荐' : '不推荐变更为推荐');

                    break;

                default:
                    throw new BusinessException('字段错误！');
            }

            if (! $payment_method->update($data)) {
                throw new BusinessException('修改失败！');
            }
            admin_operation_log($this->adminUser(), "修改了支付方式:{$payment_method->name}[{$payment_method->alias}]；{$tmp_message}", AdminOperationLog::TYPE_UPDATE);

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
     * 微信支付配置校验.
     *
     * @throws BusinessException
     */
    private function checkWechatConfig(array $config): array
    {
        $validator = Validator::make($config, [
            'mic_id' => 'required|string',
            'secret_key' => 'required|string',
            'v2_secret_key' => 'required|string',
            'private_key' => 'required|string',
            'certificate' => 'required|string',
        ], [], [
            'mic_id' => '商户号',
            'secret_key' => '商户密钥',
            'v2_secret_key' => '商户密钥（V2）',
            'private_key' => '商户私钥',
            'certificate' => '商户证书',
        ]);

        if ($validator->fails()) {
            throw new BusinessException($validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;

        return $validator->validated();
    }
}
