<?php

namespace App\Http\Controllers\Home;

use App\Components\ComponentFactory;
use App\Exceptions\BusinessException;
use App\Models\SellerEnter;
use App\Models\SellerEnterConfig;
use App\Models\SellerShop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SellerEnterController extends BaseController
{
    const ENTER_STATUS_NOT_SUBMIT = 1;      // 入驻信息未提交
    const ENTER_STATUS_CHECK_ONGOING = 2;   // 入驻信息审核中
    const ENTER_STATUS_CHECK_FAILED = 3;    // 入驻信息审核未通过
    const ENTER_STATUS_CHECK_APPROVED = 4;  // 入驻信息审核通过，没有配置店铺
    const ENTER_STATUS_FINISH_ALL = 5;      // 入驻信息审核通过，配置完店铺

    /**
     * 入驻状态检测.
     */
    public function check()
    {
        $user = $this->user();

        $seller_enter = SellerEnter::select(['id', 'check_status', 'check_desc'])
            ->with(['sellerShop'])->whereUserId($user->id)->first();

        $enter_status = self::ENTER_STATUS_NOT_SUBMIT;

        if ($seller_enter) {
            // 审核状态
            if ($seller_enter->check_status == SellerEnter::CHECK_NOT_YET) {
                $enter_status = self::ENTER_STATUS_CHECK_ONGOING;
            } elseif ($seller_enter->check_status == SellerEnter::CHECK_NOT_APPROVED) {
                $enter_status = self::ENTER_STATUS_CHECK_FAILED;
            } else {
                $enter_status = self::ENTER_STATUS_CHECK_APPROVED;

                if ($seller_enter->sellerShop ?? '') {
                    $enter_status = self::ENTER_STATUS_FINISH_ALL;
                }
            }
        }

        $data['seller_enter'] = $seller_enter ? $seller_enter->toArray() : [];
        $data['enter_status'] = $enter_status;

        return $this->success($data);
    }

    /**
     * 入驻表单信息.
     */
    public function enterConfigs(Request $request)
    {
        $user = $this->user();

        try {
            $validated = $request->validate([
                'id' => 'nullable|int',
            ], [], [
                'id' => '商家ID',
            ]);

            $id = $validated['id'] ?? 0;

            $enterConfigs = SellerEnterConfig::query()->whereIsShow(SellerEnterConfig::IS_SHOW_YES)
                ->orderByDesc('sort')->get()->map(function (SellerEnterConfig $sellerEnterConfig) {
                    $display_data = ComponentFactory::getSellerEnterComponent($sellerEnterConfig->type, $sellerEnterConfig->name)->display($sellerEnterConfig->toArray());
                    $display_data['value'] = '';

                    return $display_data;
                });

            if ($id) {
                // 编辑
                $seller_enter = SellerEnter::query()->whereId($id)->whereUserId($user->id)->first();

                if (! $seller_enter) {
                    throw new BusinessException('入驻信息不存在');
                }

                if ($seller_enter->enter_info) {
                    $enterInfoMap = collect($seller_enter->enter_info)->keyBy('id'); // 缓存配置 ID 映射关系
                    $enterConfigs = $enterConfigs->map(function ($enterConfig) use ($enterInfoMap) {
                        if (isset($enterInfoMap[$enterConfig['id']])) {
                            $enterConfig['value'] = $enterInfoMap[$enterConfig['id']]['value'] ?? '';
                        } else {
                            $enterConfig['value'] = '';
                        }

                        return $enterConfig;
                    });
                }
            }

            return $this->success(['id' => $id, 'enter_configs' => $enterConfigs->toArray()]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取入驻表单信息异常');
        }
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
                'enter_info.*.id' => 'required|int',
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
                $config = SellerEnterConfig::query()->whereId($enter_info_item['id'])->first();

                if (! $config) {
                    throw new BusinessException('入驻信息配置不存在');
                }

                if ($config->is_need == SellerEnterConfig::IS_NEED_YES && ! $enter_info_item['value']) {
                    throw new BusinessException('必须填写'.$config->name);
                }
            }

            if ($id) {
                // 编辑
                $seller_enter = SellerEnter::query()->whereId($id)->whereUserId($user->id)->first();

                if (! $seller_enter) {
                    throw new BusinessException('入驻信息不存在');
                }

                if ($seller_enter->check_status == SellerEnter::CHECK_APPROVED) {
                    throw new BusinessException('审核已通过，不需要编辑');
                }

                $seller_enter->enter_info = $enter_info;

                if (! $seller_enter->save()) {
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

                if (! $seller_enter) {
                    throw new BusinessException('新增失败');
                }
            }

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
                'title' => 'nullable|string',
                'keyword' => 'nullable|string',
                'description' => 'nullable|string',
                'country' => 'required|int',
                'province' => 'required|int',
                'city' => 'required|int',
                'address' => 'required|string',
                'ship_address' => 'required|string',
                'main_cate' => 'required|string',
                'kf_phone' => 'required|string',
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
            ]);

            $seller_enter = SellerEnter::query()->with('sellerShop')->whereId($validated['seller_id'])->whereUserId($user->id)->first();
            if (! $seller_enter) {
                throw new BusinessException('入驻信息不存在');
            }

            if (! empty($seller_enter->sellerShop)) {
                throw new BusinessException('店铺信息已存在');
            }

            if (SellerShop::whereName($validated['name'])->first()) {
                throw new BusinessException('店铺名称已被使用');
            }

            if (preg_match('/([[:space:]]|[[:punct:]])+/U', $validated['name'])) {
                throw new BusinessException('店铺名称中不能包含特殊字符');
            }

            DB::beginTransaction();

            try {
                // 创建店铺信息
                SellerShop::create([
                    'seller_id' => $validated['seller_id'],
                    'name' => $validated['name'],
                    'logo' => $validated['logo'],
                    'title' => $validated['title'] ?? '',
                    'keyword' => $validated['keyword'] ?? '',
                    'description' => $validated['description'] ?? '',
                    'country' => $validated['country'],
                    'province' => $validated['province'],
                    'city' => $validated['city'],
                    'address' => $validated['address'],
                    'ship_address' => $validated['ship_address'],
                    'main_cate' => $validated['main_cate'],
                    'kf_phone' => $validated['kf_phone'],
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
            return $this->error('创建店铺信息异常');
        }
    }
}
