<?php

namespace App\Http\Controllers\Seller;

use App\Components\ComponentFactory;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Models\SellerEnter;
use App\Models\SellerEnterConfig;
use App\Models\SellerShop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SellerEnterController extends BaseController
{
    const STATUS_NOT_ENTERED = 1;   // 未入驻
    const STATUS_CHECK_FAILED = 2;  // 未通过审核
    const STATUS_SUCCESS = 3;       // 成功
    const STATUS_NO_SHOP = 4;       // 没有店铺

    /**
     * 入驻状态检测.
     */
    public function check()
    {
        $user = $this->user();

        $seller_enter = SellerEnter::query()->whereUserId($user->id)->first();
        if (!$seller_enter) {
            $status = self::STATUS_NOT_ENTERED;
        } else {
            // 审核状态
            if ($seller_enter->check_status == SellerEnter::CHECK_APPROVED) {
                $shop = $seller_enter->sellerShop ?? '';
                if (!$shop) {
                    $status = self::STATUS_NO_SHOP;
                } else {
                    $status = self::STATUS_SUCCESS;
                }
            } else {
                $status = self::STATUS_CHECK_FAILED;
            }
        }

        return $this->success(['status' => $status]);
    }


    /**
     * 入驻表单信息.
     */
    public function enterConfigs(Request $request)
    {
        $enterConfigs = SellerEnterConfig::query()->whereIsShow(SellerEnterConfig::IS_SHOW_YES)->orderByDesc('sort')->get()
            ->map(function (SellerEnterConfig $sellerEnterConfig) {
                return ComponentFactory::getSellerEnterComponent($sellerEnterConfig->type, $sellerEnterConfig->name)->display($sellerEnterConfig->toArray());
            })->toArray();

        return $this->success(['enter_configs' => $enterConfigs]);
    }

    /**
     * 提交入驻信息.
     */
    public function store(Request $request)
    {
        $user = $this->user();

        try {
            $validated = $request->validate([
                'id' => 'nullable|int',
                'enter_info' => 'required|array',
                'enter_info.*.config_id' => 'required|int',
                'enter_info.*.type' => 'required|string',
                'enter_info.*.name' => 'required|string',
                'enter_info.*.value' => 'nullable|string',
            ], [], [
                'id' => '商家ID',
                'enter_info' => '入驻信息',
            ]);

            $id = $validated['id'];
            $enter_info = $validated['enter_info'];

            foreach ($enter_info as $enter_info_item) {
                $config = SellerEnterConfig::query()->whereId($enter_info_item['config_id'])->first();
                if (!$config) {
                    throw new BusinessException('入驻信息配置不存在');
                }
                if ($config->is_need == SellerEnterConfig::IS_NEED_YES && !$enter_info_item['value']) {
                    throw new BusinessException('必须填写' . $config->name);
                }
            }


            if ($id) {
                // 编辑
                $seller_enter = SellerEnter::query()->whereId($id)->whereUserId($user->id)->first();
                if (!$seller_enter) {
                    throw new BusinessException('入驻信息不存在');
                }
                if ($seller_enter->check_status == SellerEnter::CHECK_APPROVED) {
                    throw new BusinessException('审核已通过，不需要编辑');
                }

                $seller_enter->enter_info = $enter_info;
                if (!$seller_enter->save()) {
                    throw new BusinessException('编辑失败');
                }

            } else {
                // 新增
                $seller_enter = SellerEnter::query()->whereUserId($user->id)->first();
                if ($seller_enter) {
                    throw new BusinessException('不允许重复申请入驻');
                }

                $seller_enter = SellerEnter::query()->create([
                    'user_id' => $user->id,
                    'enter_info' => $enter_info,
                    'source' => get_source(),
                ]);

                if (!$seller_enter) {
                    throw new BusinessException('新增失败');
                }
            }

            // todo 发送商家入驻通知

            return $this->success('操作成功');

        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('提交入驻信息异常');
        }
    }

    /**
     * 创建店铺信息.
     */
    public function shopCreate(Request $request)
    {
        $user = $this->user();

        try {
            $validated = $request->validate([
                'seller_id' => 'required|int',
                'name' => 'required|string',
                'logo' => 'required|string',
                'title' => 'required|string',
                'keyword' => 'required|string',
                'description' => 'required|string',
                'country' => 'required|int',
                'province' => 'required|int',
                'city' => 'required|int',
                'address' => 'required|string',
                'ship_address' => 'required|string',
                'main_cate' => 'required|string',
                'kf_phone' => 'required|string',
                'leader_name' => 'nullable|string',
                'leader_position' => 'nullable|string',
                'leader_phone' => 'nullable|string',
                'leader_email' => 'nullable|string',
            ], [], [
                'seller_id' => '商家ID',
                'name' => '店铺名称',
                'logo' => '店铺logo',
                'title' => '店铺标题',
                'keyword' => '店铺关键字',
                'description' => '店铺描述',
                'country' => '国家',
                'province' => '省份',
                'city' => '城市',
                'address' => '店铺地址',
                'ship_address' => '发货地址',
                'main_cate' => '主营类目',
                'kf_phone' => '客服电话',
                'leader_name' => '负责人姓名',
                'leader_position' => '负责人职位',
                'leader_phone' => '负责人电话',
                'leader_email' => '负责人邮箱',
            ]);

            $seller_enter = SellerEnter::query()->with('sellerShop')
                ->whereId($validated['seller_id'])->whereUserId($user->id)
                ->first();
            if (!$seller_enter) {
                throw new BusinessException('入驻信息不存在');
            }

            if (!empty($seller_enter->sellerShop)) {
                throw new BusinessException('店铺信息已存在');
            }

            if(SellerShop::whereName($validated['name'])->first()){
                throw new BusinessException('店铺名称已被使用');
            }

            if (preg_match('/[^\w\u4e00-\u9fa5]/u', $validated['name'])) {
                throw new BusinessException('店铺名称中不能包含特殊字符');
            }

            DB::beginTransaction();

            try {
                // 创建店铺信息
                SellerShop::create([
                    'seller_id' => $validated['seller_id'],
                    'name' => $validated['name'],
                    'logo' => $validated['logo'],
                    'title' => $validated['title'],
                    'keyword' => $validated['keyword'],
                    'description' => $validated['description'],
                    'country' => $validated['country'],
                    'province' => $validated['province'],
                    'city' => $validated['city'],
                    'address' => $validated['address'],
                    'ship_address' => $validated['ship_address'],
                    'main_cate' => $validated['main_cate'],
                    'kf_phone' => $validated['kf_phone'],
                    'leader_name' => $validated['leader_name'] ?? '',
                    'leader_position' => $validated['leader_position'] ?? '',
                    'leader_phone' => $validated['leader_phone'] ?? '',
                    'leader_email' => $validated['leader_email'] ?? '',
                ]);

                // 更新用户表中的 seller_id
                User::whereId($user->id)->update(['seller_id' => $seller_enter->id]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            return $this->success('操作成功');

        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('更新店铺信息异常');
        }
    }


}
