<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Models\Goods;
use App\Utils\AppServiceConfig\IbiChatUtil;
use App\Utils\Constant;
use App\Utils\Md5Utils;
use Illuminate\Http\Request;

/**
 * 客服.
 */
class ChatController extends BaseController
{

    // 获取客服地址
    public function chatUrl(Request $request)
    {
        $goods_no = $request->get('no','');
        $source_url = $request->get('source_url','');
        $url = '';
//        $platform_is_show = shop_config(ShopConfig::SERVER_IS_SHOW);
        $platform_is_show = true;
        if ($platform_is_show) {
            $user = get_user();
            $ibi_chat = new IbiChatUtil(0);
            $config = $ibi_chat->getConfig()->config;
            $platform_id = $config['platform_id'];
            $platform_platform_secret = $config['platform_secret'];
            $server_platform_url = $config['host'];

            if ($user) {
                $data = [
                    'platform_id' => $platform_id,
                    'user_id' => $user->user_id,
                    'user_name' => $user->user_name,
                    'user_phone' => $user->phone,
                    'user_head_img' => $user->avatar,
                    'source' => get_source()->value,
                    'source_url' => $source_url,
                ];
            } else {
                $data = [
                    'platform_id' => $platform_id,
                    'source' => get_source()->value,
                    'source_url' => $source_url,
                ];
            }

            if ($goods_no && $goods_id = Goods::query()->whereNo($goods_no)->value('id')) {
                $data['goods_id'] = $goods_id;
            }
            $data['seller_id'] = Constant::ZERO;

            $sign_params = Md5Utils::sign($data, $platform_platform_secret);
            $url = $server_platform_url.'?'.http_build_query($sign_params);
        }

        return $this->success(['url' => $url]);
    }

}
