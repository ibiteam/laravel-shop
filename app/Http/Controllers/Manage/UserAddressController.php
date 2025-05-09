<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Requests\Manage\IndexRequest;
use App\Http\Requests\Manage\UserAddressRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAddressController extends BaseController
{
    public function index(IndexRequest $request)
    {
        $user_id = (int) $request->get('user_id', 0);
        $list = UserAddress::query()->latest()
            ->with('regionProvince', 'regionCity', 'regionDistrict')
            ->when($consignee = $request->get('consignee'), fn (Builder $query) => $query->whereLike('consignee', "%{$consignee}%"))
            ->when($user_id, fn (Builder $query) => $query->whereUserId($user_id))
            ->orderByDesc('id')
            ->paginate($request->per_page);
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
                $user_address = new UserAddress;
            }
            $user_address->id = $id;
            $user_address->user_id = $validated['user_id'];
            $user_address->address_detail = $validated['address_detail'];
            $user_address->consignee = $validated['consignee'];
            $user_address->phone = $validated['phone'];
            $user_address->province = $validated['province'];
            $user_address->city = $validated['city'];
            $user_address->district = $validated['district'];

            if (! $user_address->save()) {
                throw new BusinessException('保存失败');
            }
            // 记录日志
            admin_operation_log( "{$operation}了地址:[{$user_address->id}]", $operation_type);

            return $this->success('保存成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('保存失败');
        }
    }
}
