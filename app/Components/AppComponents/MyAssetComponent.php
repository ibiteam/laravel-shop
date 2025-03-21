<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Http\Daos\UserBonusDao;
use App\Http\Daos\UserCouponDao;
use App\Models\AppWebsiteDecorationItem;
use App\Models\IntegralUser;
use App\Models\Router;
use App\Services\MobileRouterService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

/**
 * 资产中心.
 */
class MyAssetComponent extends PageComponent
{
    public function icon(): array
    {
        return [
        ];
    }

    /**
     * init left and right form data.
     * {@inheritDoc}
     */
    public function parameter(): array
    {
        return [
            'id' => '',
            'name' => '资产中心',
            'component_name' => $this->getComponentName(),
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ONE,
            'sort' => Constant::ZERO,
            'content' => [
                'basic_setting' => [
                    'name' => '我的资产',
                    'url' => [
                        'alias' => Router::MY_ASSET,
                        'value' => '',
                    ],
                ],
                'menu' => [
                    'coupon_show' => 1,
                    'bonus_show' => 1,
                    'duo_coin_show' => 1,
                    'integral_show' => 1,
                ],
            ],
        ];
    }

    /**
     * obtain display data
     * {@inheritDoc}
     */
    public function getContent(array $data): array
    {
        $content = $data['content'] ?? [];
        $user = $data['user'] ?? null;
        $source = $data['source'] ?? null;
        $url_alias = $content['basic_setting']['url']['alias'] ?? '';
        $url = '';
        if ($url_alias) {
            $mobileRouterService = app(MobileRouterService::class);
            $url = $content['basic_setting']['url']['alias'] = $mobileRouterService->handleUrl($url_alias,'',$source);
        }
        $user_id = $user->user_id ?? 0;
        $bonus = $user_id ? (app(UserBonusDao::class)->bonusCount($user_id)['canuse_count'] ?? 0) : 0;
        $coupon = $user_id ? app(UserCouponDao::class)->couponNum($user_id) : 0;
        $pay_points = $user->pay_points ?? 0;
        $integral = $this->getIntegration($user_id);
        return [
            'component_name' => $this->getComponentName(),
            'name' => $content['basic_setting']['name'] ?? '',
            'asset_data' => [
                'url' => $url,
                'coupon_show' => ($content['menu']['coupon_show'] ??'')?true:false,
                'bonus_show' => ($content['menu']['bonus_show'] ??'')?true:false,
                'duo_coin_show' => ($content['menu']['duo_coin_show'] ??'')?true:false,
                'integral_show' => ($content['menu']['integral_show'] ??'')?true:false,
                'bonus' => format_number($bonus),
                'coupon' => format_number($coupon),
                'pay_points' => format_number($pay_points),
                'integral' => format_number($integral),
            ],
        ];
    }

    /**
     *  content_data 用来展示画布上的数据
     * {@inheritDoc}
     */
    public function display(array $data = []): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }

        return [
            'id' => $data['id'] ?? 0,
            'name' => $data['name'] ?? '',
            'component_name' => $data['component_name'] ?? '',
            'is_show' => $data['is_show'] ?? 1,
            'is_fixed_assembly' => $data['is_fixed_assembly'] ?? 0,
            'sort' => $data['sort'] ?? 0,
            'content' => $data['content'] ?? null,
            'data' => $this->getContent($data),
        ];
    }

    /**
     * verify request parameters
     * {@inheritDoc}
     */
    public function validate(array $data): array
    {
        $is_show_validate_string = Constant::ONE.','.Constant::ZERO;

        $validator = Validator::make($data, [
            'id' => 'present|nullable|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppWebsiteDecorationItem::COMPONENT_NAME_MY_ASSET,
            'is_show' => 'required|integer|in:'.$is_show_validate_string,
            'content' => 'required',
            'content.basic_setting' => 'required',
            'content.basic_setting.name' => 'required|max:10',
            'content.basic_setting.url' => 'required',
            'content.basic_setting.url.alias' => 'required',
            'content.basic_setting.url.value' => 'present|nullable',
            'content.menu' => 'required',
            'content.menu.coupon_show' => 'present|nullable|integer',
            'content.menu.bonus_show' => 'present|nullable|integer',
            'content.menu.duo_coin_show' => 'present|nullable|integer',
            'content.menu.integral_show' => 'present|nullable|integer',
        ],$this->message());

        if ($validator->fails()) {
            throw new BusinessException($this->getName().$validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $validate = $validator->validated();

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'component_name' => $data['component_name'],
            'is_show' => $data['is_show'],
            'is_fixed_assembly' => Constant::ZERO,
            'sort' => (int) ($data['sort']??0),
            'content' => [
                'basic_setting' => [  //基础设置
                    'name' => $validate['content']['basic_setting']['name'],
                    'url' => [ //url链接相关
                        'alias' => $validate['content']['basic_setting']['url']['alias'],
                        'value' => $validate['content']['basic_setting']['url']['value'],
                    ],
                ],
                'menu' => [  //资产菜单
                    'coupon_show' => $validate['content']['menu']['coupon_show'] ? 1 : 0,
                    'bonus_show' => $validate['content']['menu']['bonus_show'] ? 1 : 0,
                    'duo_coin_show' => $validate['content']['menu']['duo_coin_show'] ? 1 : 0,
                    'integral_show' => $validate['content']['menu']['integral_show'] ? 1 : 0,
                ],
            ],
        ];
    }

    /**
     * 用户总积分.
     *
     * @return int|mixed
     *
     * @throws \App\Exceptions\BusinessException
     */
    private function getIntegration($user_id)
    {
        //获取的积分
        $all = $user_id?(IntegralUser::whereUserId($user_id)->value('number') ?? 0):0;
        if ($all < 0) {
            $all = 0;
        }

        return $all;
    }

    private function message()
    {
        return [
            'id.present' => '板块ID 未设置',
            'id.integer' => '板块ID 格式不正确',
            'id.exists' => '板块ID不存在，请刷新重试',
            'name.required' => '请设置板块名称',
            'name.max' => '板块名称不能超过:max个字符',
            'component_name.required' => '未设置组件别名，请刷新页面后重试',
            'component_name.in' => '组件别名参数不正确，请刷新页面后重试',
            'is_show.required' => '请设置板块是否展示',
            'is_show.integer' => '板块是否展示参数格式不正确',
            'is_show.in' => '板块是否展示参数格式不正确',
            'content.required' => '请设置板块对应数据',
            'content.basic_setting.required' => '请设置基础设置对应数据',
            'content.basic_setting.name.required' => '请输入板块名称',
            'content.basic_setting.name.max' => '板块名称不能超过 :max 个字符',
            'content.basic_setting.url.required' => '未设置url链接参数',
            'content.basic_setting.url.alias.required' => '未设置url链接别名参数',
            'content.basic_setting.url.value.present' => '未设置url链接value参数',
            'content.menu.required' => '请设置资产菜单对应数据',
            'content.menu.coupon_show.present' => '请设置优惠券是否显示参数',
            'content.menu.coupon_show.integer' => '优惠券是否显示参数格式不正确',
            'content.menu.bonus_show.present' => '请设置红包是否显示参数',
            'content.menu.bonus_show.integer' => '请设置红包是否显示参数格式不正确',
            'content.menu.duo_coin_show.present' => '请设置多多币是否显示参数',
            'content.menu.duo_coin_show.integer' => '请设置多多币是否显示参数格式不正确数',
            'content.menu.integral_show.present' => '请设置积分是否显示参数',
            'content.menu.integral_show.integer' => '请设置积分是否显示参数格式不正确数',
        ];
    }
}
