<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Requests\Manage\UserAddressRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\User;
use App\Models\UserAddress;
use App\Rules\PhoneRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserAddressController extends BaseController
{
    public function index(Request $request)
    {
        $user_id = (int)$request->get('user_id', 0);
        $list = UserAddress::query()->latest()
            ->with('regionProvince', 'regionCity', 'regionDistrict')
            ->when($recipient_name = $request->get('recipient_name'), fn (Builder $query) => $query->whereLike('recipient_name', "%{$recipient_name}%"))
            ->when($user_id, fn (Builder $query) => $query->whereUserId($user_id))
            ->orderByDesc('id')
            ->paginate((int) $request->get('number', 10));
        $list->getCollection()->transform(function (UserAddress $user_address) {
            $user_address->area = [$user_address->province, $user_address->city, $user_address->district];
            $user_address->province_name = $user_address->regionProvince?->name;
            $user_address->city_name = $user_address->regionCity?->name;
            $user_address->district_name = $user_address->regionDistrict?->name;

            return $user_address;
        });

        return $this->success(new CommonResourceCollection($list));
    }

    public function update(UserAddressRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $operation = '添加';
            $operation_type = AdminOperationLog::TYPE_STORE;
            if ($id = $validated['id']) {
                $user_address = UserAddress::query()->find($id);

                if (! $user_address instanceof UserAddress) {
                    throw new BusinessException('地址不存在');
                }

                $operation = '修改';
                $operation_type = AdminOperationLog::TYPE_UPDATE;
            } else {
                $user_address = new UserAddress();
            }
            $user_address->id = $id;
            $user_address->user_id = $validated['user_id'];
            $user_address->address_detail = $validated['address_detail'];
            $user_address->recipient_name = $validated['recipient_name'];
            $user_address->recipient_phone = $validated['recipient_phone'];
            $user_address->province = $validated['province'];
            $user_address->city = $validated['city'];
            $user_address->district = $validated['district'];
            if (! $user_address->save()) {
                throw new BusinessException('保存失败');
            }
            // 记录日志
            admin_operation_log($this->adminUser(), "{$operation}了地址:[{$user_address->id}]", $operation_type);

            return $this->success('保存成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('保存失败');
        }
    }
}
