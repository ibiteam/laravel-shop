<?php

namespace App\Http\Controllers\Manage;

use App\Enums\PaymentEnum;
use App\Exceptions\BusinessException;
use App\Http\Requests\Manage\PaymentMethodRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PaymentController extends BaseController
{
    /**
     * 支付方式列表.
     */
    public function index(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $is_enabled = $request->get('is_enabled', null);
        $per_page = (int) $request->get('per_page', 10);
        $list = Payment::query()
            ->latest()
            ->when($name, fn (Builder $query) => $query->whereLike('name', "%{$name}%"))
            ->when(! is_null($is_enabled), fn (Builder $query) => $query->whereIsEnabled($is_enabled))
            ->paginate($per_page);

        return $this->success(new CommonResourceCollection($list));
    }

    /**
     * 支付方式更新.
     */
    public function update(PaymentMethodRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $payment = Payment::query()->whereId($validated['id'])->first();

            if (! $payment instanceof Payment) {
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

            if ($payment->alias == PaymentEnum::WECHAT->value) {
                $update_data['config'] = $this->checkWechatConfig($validated['config']);
            }

            if (! $payment->update($update_data)) {
                throw new BusinessException('更新失败！');
            }

            admin_operation_log( "更新了支付方式:{$payment->name}[{$payment->alias}]", AdminOperationLog::TYPE_UPDATE);

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
        $validated = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
            'field' => 'required|string|in:is_enabled,is_recommend',
        ], [], [
            'id' => '支付方式ID',
            'field' => '字段',
            'name' => '名称',
        ]);
        $payment = Payment::findOrFail($validated['id']);
        $field = (string) $validated['field'];
        $value = ! $payment->$field;
        $message = '修改了支付方式id='.$payment->id.'的'.$validated['name'].'['.$field.']变更为'.json_encode($value, JSON_THROW_ON_ERROR);
        $payment->$field = $value;

        if (! $payment->save()) {
            return $this->error('修改失败');
        }
        admin_operation_log( $message, AdminOperationLog::TYPE_UPDATE);

        return $this->success('修改成功');
    }

    /**
     * 微信支付配置校验.
     *
     * @throws BusinessException
     */
    private function checkWechatConfig(array $config): array
    {
        $validator = Validator::make($config, [
            'mch_id' => 'required|string',
            'secret_key' => 'required|string',
            'v2_secret_key' => 'required|string',
            'private_key' => 'required|string',
            'certificate' => 'required|string',
            'app_wechat_pay_app_id' => 'nullable|string',
        ], [], [
            'mic_id' => '商户号',
            'secret_key' => '商户密钥',
            'v2_secret_key' => '商户密钥（V2）',
            'private_key' => '商户私钥',
            'certificate' => '商户证书',
            'app_wechat_pay_app_id' => '微信支付应用程序(App)APPID',
        ]);

        if ($validator->fails()) {
            throw new BusinessException($validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;

        return $validator->validated();
    }
}
