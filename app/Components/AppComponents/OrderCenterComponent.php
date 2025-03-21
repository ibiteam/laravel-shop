<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Http\Daos\CollectGoodsDao;
use App\Http\Daos\CollectShopDao;
use App\Http\Daos\OrderInfoDao;
use App\Models\ApplyRefund;
use App\Models\AppWebsiteDecorationItem;
use App\Models\OrderComment;
use App\Models\OrderInfo;
use App\Models\Router;
use App\Services\MobileRouterService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

/**
 * 订单中心.
 */
class OrderCenterComponent extends PageComponent
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
            'name' => '订单中心',
            'component_name' => $this->getComponentName(),
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ONE,
            'sort' => Constant::ZERO,
            'content' => [
                'order_menu' => [
                    'name' => '我的订单',  //板块类型
                    'url' => [
                        'alias' => Router::MY_ORDER,
                        'value' => '',
                    ],
                    'image_items' => [ //自定义板块独有
                        [
                            'image' => 'https://cdn.toodudu.com/uploads/2023/10/25/组 503@3x.png',
                            'order_status' => (string)AppWebsiteDecorationItem::OS_UNCONFIRMED, //待确认
                        ],
                        [
                            'image' => 'https://cdn.toodudu.com/uploads/2023/10/25/组 502@3x.png',
                            'order_status' => (string)AppWebsiteDecorationItem::OS_UNPAYED, //待付款
                        ],
                        [
                            'image' => 'https://cdn.toodudu.com/uploads/2023/10/25/组 504@3x.png',
                            'order_status' => (string)AppWebsiteDecorationItem::OS_UNRECEIVED, //待收货
                        ],
                        [
                            'image' => 'https://cdn.toodudu.com/uploads/2023/10/25/组 505@3x.png',
                            'order_status' => (string)AppWebsiteDecorationItem::OS_UNEVALUATE, //待评价
                        ],
                        [
                            'image' => 'https://cdn.toodudu.com/uploads/2023/10/25/组 506@3x.png',
                            'order_status' => (string)AppWebsiteDecorationItem::OS_AFTERSALES, //退款/售后
                        ],
                    ],
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
		$user_id = $user->user_id ?? 0;
        $mobileRouterService = app(MobileRouterService::class);
        $order_url = $mobileRouterService->handleUrl(Router::NO_HOME_MY_ORDER,'',$source);
        $evaluate_url = $mobileRouterService->handleUrl(Router::MYEVALUATE_LIST,'',$source);
        $after_sales_url = $mobileRouterService->handleUrl(Router::AFTER_SALES,'',$source);
        $image_items = [];
        collect($content['order_menu']['image_items'] ?? [])->map(function ($item) use (&$image_items, $order_url, $evaluate_url, $after_sales_url, $user) {
			$url = '';
            $order_count = 0;
            switch ($item['order_status']) {
                case AppWebsiteDecorationItem::OS_UNCONFIRMED:
                case AppWebsiteDecorationItem::OS_UNPAYED:
                case AppWebsiteDecorationItem::OS_UNSHIPPED:
                case AppWebsiteDecorationItem::OS_UNRECEIVED:
                    if (AppWebsiteDecorationItem::OS_UNCONFIRMED == $item['order_status']) {
						$type = 5;
                        $order_count = $user?app(OrderInfoDao::class)->getOrdersCount(OrderInfo::WCONFIRM, $user->user_id):0;
                    } elseif (AppWebsiteDecorationItem::OS_UNPAYED == $item['order_status']) {
						$type = 1;
                        $order_count = $user?app(OrderInfoDao::class)->getOrdersCount(OrderInfo::WPAY, $user->user_id):0;
                    } elseif (AppWebsiteDecorationItem::OS_UNSHIPPED == $item['order_status']) {
						$type = 4;
                        $order_count =  $user?app(OrderInfoDao::class)->getOrdersCount(OrderInfo::WSHIPPING, $user->user_id):0;
                    } elseif (AppWebsiteDecorationItem::OS_UNRECEIVED == $item['order_status']) {
						$type = 2;
                        $order_count =  $user?app(OrderInfoDao::class)->getOrdersCount(OrderInfo::WSURE, $user->user_id):0;
                    } else {
						$type = '';
                    }
                    $url = $order_url.connectStr($order_url).'order_type='.$type;
                    break;
                case AppWebsiteDecorationItem::OS_UNEVALUATE:
					$url = $evaluate_url;
                    $order_count = $user ? OrderComment::whereUserId($user->user_id)->where('status', OrderComment::NO_EVALUATE)->orderByDesc('created_at')->whereHas('order',function($query){
                        return $query->whereShippingStatus(OrderInfo::SS_RECEIVED);
                    })->count() : 0;

                    break;
                case AppWebsiteDecorationItem::OS_AFTERSALES:
					$url = $after_sales_url;
                    $order_count = $user ? ApplyRefund::whereUserId($user->user_id)->whereStatus(ApplyRefund::STATUS_NOT_PROCESSED)->count() : 0;
                    break;
            }
            $image_items[] = [
                'icon' => $item['image'] ?? '',
                'name' => AppWebsiteDecorationItem::$order_desc[$item['order_status'] ?? ''] ?? '',
                'count' => $order_count, //>=0展示右上角数量
                'url' => $url,
            ];
        })->toArray();

        $goods_num = $this->formatCollectNum(app(CollectGoodsDao::class)->collectNum($user_id));
        $shop_num = $this->formatCollectNum(app(CollectShopDao::class)->collectNum($user_id));

        return [
            'component_name' => $this->getComponentName(),
            'order_data' => [
                'name' => $content['order_menu']['name'] ?? '',
				'url' => $order_url,
				'order_items' => $image_items,
                'collect' => [
                    'goods_num' => $goods_num > 99 ? '99+' : (string) $goods_num,
                    'shop_num' => $shop_num > 99 ? '99+' : (string) $shop_num,
                ],
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
            'sort' => (int)($data['sort'] ?? 0),
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
            'component_name' => 'required|in:'.AppWebsiteDecorationItem::COMPONENT_NAME_ORDER_CENTER,
            'is_show' => 'required|integer|in:'.$is_show_validate_string,
			'content' => 'required',
            'content.order_menu' => 'required',
            'content.order_menu.name' => 'required|max:10',
            'content.order_menu.url' => 'required',
            'content.order_menu.url.alias' => 'required',
            'content.order_menu.url.value' => 'present|nullable',
            'content.order_menu.image_items' => 'required|array|max:5',
            'content.order_menu.image_items.*.image' => 'required',
            'content.order_menu.image_items.*.order_status' => 'required',
        ], $this->message());
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
                'order_menu' => [  //订单菜单
                    'name' => $validate['content']['order_menu']['name'],
                    'url' => [ //url链接相关
                        'alias' => $validate['content']['order_menu']['url']['alias'],
                        'value' => $validate['content']['order_menu']['url']['value'],
                    ],
                    'image_items' => $validate['content']['order_menu']['image_items'],
                ],
            ],
        ];
    }

    private function formatCollectNum($number)
    {
        if ($number > 99) {
            return '99+';
        }

        return (string) $number;
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
            'content.order_menu.required' => '请设置订单菜单对应数据',
            'content.order_menu.name.required' => '请输入板块名称',
            'content.order_menu.name.max' => '板块名称不能超过:max个字符',
            'content.order_menu.url.required' => '未设置url链接参数',
            'content.order_menu.url.alias.required' => '未设置url链接别名参数',
            'content.order_menu.url.value.present' => '未设置url链接value参数',
            'content.order_menu.image_items.required' => '未设置图片',
            'content.order_menu.image_items.array' => '图片参数格式不正确',
            'content.order_menu.image_items.max' => '图片栏目最多:max条数据',
            'content.order_menu.image_items.*.image.required' => '请上传图片',
            'content.order_menu.image_items.*.order_status.required' => '请选择订单状态',
        ];
    }
}
