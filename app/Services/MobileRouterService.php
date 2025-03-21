<?php

namespace App\Services;

use App\Http\Dao\RouterDao;
use App\Models\AppWebsiteDecoration;
use App\Models\Router;
use App\Utils\Constant;

class MobileRouterService
{
    public const SOURCE_APP = 'is_app';
    public const SOURCE_MINI = 'is_mini';
    public const SOURCE_H5 = 'is_h5';

    public static $no_need_remote_search = [
        Router::HTTPS,
        Router::SEARCH,
    ];
    private $list; //路由列表

    public function __construct()
	{
		$routers = cache_forever(Router::CACHE_APP_ROUTER_LIST, function () {
			$routers = app(RouterDao::class)->router();
			array_unshift($routers, [
				'name' => 'https',
				'alias' => Router::HTTPS,
			]);
			$routers =  collect($routers)->keyBy('alias');
			return $routers->all();
		});
		$this->list = $routers;
	}

    /**
     * 获取链接数组.
     */
    public function routers(): array
    {
        $data = [];
        foreach ($this->list as $item) {
			if(in_array($item['alias'],RouterDao::$bottom_menu_page)){
				continue;
			}
            $desc = $this->getAliasValueDesc($item['alias']);
            $data[] = [
                'name' => $item['name'],
                'alias' => $item['alias'],
                'desc' => $desc,
                'need_remote_search' => ($desc && !in_array($item['alias'], self::$no_need_remote_search)) ? true : false,
            ];
        }

        return $data;
    }

    /**
     * 处理链接拼接 app_url h5_url mini_url 最终保存模板的时候调用方法返回跳转地址
     * 	$routes = app(MobileRouterService::class)->handleUrl('https','https://test-tooduduh5.ptdplat.com/#/store?seller_id=80'); https
     * 	$routes = app(MobileRouterService::class)->handleUrl('article_detail',1088]);  文章详情.
     *
     * @param ...$data
     *
     * @return array|string[] 返回 app_url h5_url mini_url
     */
    public function handleUrl(...$data): string
    {
        $url = '';
        $alias = $data[0] ?? Router::HTTPS; //路由alias
        $alias_value = $data[1] ?? ''; //alias 下的参数值 ||  https对应地址
        $resource = $data[2] ?? ''; //来源,不传默认app
		switch ($alias) {
            case '':
                return $url;
            case Router::HTTPS:
                return $this->dealIllegalHttpUrl($alias_value);
            default:
                //其它
                $current_alias_data = $this->list[$alias] ?? null;
                if (!$current_alias_data) {
                    return $url;
                }
				if($alias == Router::NO_HOME_CATEGORY){ //分类（二级页面）拿分类页id
					$alias_value = AppWebsiteDecoration::whereAlias(AppWebsiteDecoration::ALIAS_CATEGORY)->value('id')??0;
				}
                $appParam = '';
                $h5Param = '';
                $miniParam = '';
                if ($alias_value) {
					//目前只有一组参数值对
                    foreach ($current_alias_data['params'] as $value) {
                        $appParam .= $value->key.'='.$alias_value;
                        $h5Param .= $value->key.'='.$alias_value;
                        $miniParam .= $value->key.'='.$alias_value;
                    }
                }
                //不同端口是否开启路由，返回最终地址
                $final_app_mini_url = $this->getFinalAppMiniUrl($current_alias_data,$resource);
				$h5_url = $this->dealIllegalHttpUrl($current_alias_data['h5_url']);
				$mini_url = trim($final_app_mini_url['mini_url'],' ')?: $h5_url;
				$app_url = trim($final_app_mini_url['app_url'],' ') ?: $h5_url;

                if (self::SOURCE_APP == $resource) {
                    $url = $appParam ? ($app_url.$this->connectStr($app_url, $appParam).$appParam) : $app_url;
                } elseif (self::SOURCE_H5 == $resource) {
                    $url = $h5Param ? ($h5_url.$this->connectStr($h5_url, $h5Param).$h5Param) : $h5_url;
                } elseif (self::SOURCE_MINI == $resource) {
                    $url = $miniParam ? ($mini_url.$this->connectStr($mini_url, $miniParam).$miniParam) : $mini_url;
                } else {
                    $url = $appParam ? ($app_url.$this->connectStr($app_url, $appParam).$appParam) : $app_url;
                }

                return $url;
        }
    }

    /**
     * 根据不同系统|不同来源。查看是否开启，返回对应的地址，未开启返回空
     * @param $system
     * @param Router $router
     * @return array
     */
    private function getFinalAppMiniUrl($router=[],$resource='')
    {
        $app_url =  '';
        $mini_url =  '';
        if($resource == self::SOURCE_APP){
            if(is_ios_request() && $router['ios_is_open'] == Constant::ONE){
                $app_url =  $router['app_url'];
            }elseif(is_android_request() && $router['android_is_open'] == Constant::ONE){
                $app_url = $router['app_url'];
            }elseif(is_harmony_request() && $router['harmony_is_open'] == Constant::ONE){
                $app_url =  $router['harmony_url'];
            }
        }elseif($resource == self::SOURCE_MINI){
            if($router['mini_is_open'] == Constant::ONE){
                $mini_url =  $router['mini_url'];
            }
        }
        return [
            'app_url' =>$app_url,
            'mini_url' =>$mini_url
        ];
    }

    public function connectStr($url, $appParam)
    {
        if (!$appParam) {
            return '';
        }

        return connectStr($url);
    }

    public function getAliasValueDesc($alias,$is_void=false)
    {
        $double_ten_browse_msg = '请输入模板ID/名称';
        $presell_msg = '请输入预售活动ID/名称';
        if($is_void){
            if( in_array($alias,[Router::DOUBLE_TEN_BROWSE,Router::HOME_DOUBLE_TEN_BROWSE])){
                $double_ten_browse_msg = '活动不存在/活动时间已结束';
            }
            if( in_array($alias,[Router::PRESELL_INDEX])){
                $presell_msg = '预售活动不存在/活动时间已结束';
            }
        }
        $data = [
            Router::HTTPS => '请输入链接地址',
            Router::NO_HOME_PUBLIC_PAGE => '请输入页面ID/名称',
            Router::ARTICLE_INDEX => '请输入文章分类ID/名称',
            Router::ARTICLE_DETAIL => '请输入文章ID/名称',
            Router::ZIXUN_ARTICLE_INDEX => '请输入分类ID/名称',
            Router::ZIXUN_ARTICLE_DETAIL => '请输入文章ID/名称',
            Router::GOODS => '请输入商品ID/名称',
            Router::STORE => '请输入店铺ID/名称',
            Router::LIVE_SHOW => '请输入别名/主播名称',
            Router::LIVE_RECORD => '请输入回放ID/标题',
            Router::LIVE_NOTICE => '请输入别名/主播名称',
            Router::SHIP_INDEX => '请输入订单ID',
            Router::MYEVALUATE_PUBLISH => '请输入订单ID',
            Router::DOUBLE_TEN_BROWSE => $double_ten_browse_msg,
            Router::HOME_DOUBLE_TEN_BROWSE => $double_ten_browse_msg,
            Router::PRESELL_INDEX => $presell_msg,
            Router::PRESELL_DETAIL => '请输入活动商品ID',
            Router::PROBATION_INDEX => '请输入活动ID/名称', //多多试用列表
            Router::PROBATION_GOODS => '请输入活动商品ID', //多多试用商品详情
            Router::AUCTION_DETAIL => '请输入活动商品ID', //竞拍详情
            Router::INTEGRAL_GOODS => '请输入积分商品ID/名称',
            Router::NEW_YEAR_WELFARE => '请输入新年红包ID/名称',
            Router::ARTICLE_INDUSTRY_SUMMIT => '请输入分类ID/名称',
            Router::INVITE_TEMPLATE => '请输入邀约模板ID/名称',
            Router::SEARCH => '请输入搜索关键字',
            Router::QUESTIONNAIRE_SURVEY => '请输入邀约模板ID/名称',
            Router::INDUSTRY => '请输入产业链专题ID/名称',
            Router::CATEGORY_PUBLIC => '请输入分类页面ID/名称',
            Router::SPECIAL => '请输入分类ID',
            Router::NEW_YEAR_MARKING => '请输入新年营销ID/名称',
            Router::YI_QI_XIU => '输入标题(空格获取全部)',
        ];

        return $data[$alias] ?? '';
    }

    /**
     * @param ...$data
     *
     * @throws \Exception
     */

    /**
     * @param string $alias       路由alias
     * @param string $alias_value alias 下的参数值 ||  https对应地址
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function viodData($alias = '', $alias_value = '')
    {
        //有别名 && 必须有值
        $alias_value_desc = $this->getAliasValueDesc($alias,true);
        if ($alias && $alias_value_desc) {
            if (!$alias_value) {
                throw new \Exception('URL链接数据不能为空，'.$alias_value_desc.'。');
            }

			if($alias == Router::HTTPS && !$this->dealIllegalHttpUrl($alias_value)){
				throw new \Exception('https类型时，链接有误');
			}
			//除https外，其它关键字长度限制10
//			if($alias != Router::HTTPS){
//				if(mb_strlen($alias_value)>10){
//					throw new \Exception('url链接对应的值长度不能超过10个字符。');
//				}
//			}
            //检测值是否存在
            if (!in_array($alias, self::$no_need_remote_search) &&  $alias !== Router::YI_QI_XIU) {
                $option = $this->getOption($alias, $alias_value, true);
                if (!$option) {
					throw new \Exception('URL链接数据不存在，'.$alias_value_desc.'。');
                }
            }
        }

        return true;
    }

    /**
     * @param string $alias       路由alias
     * @param string $alias_value alias 下的参数值 ||  https对应地址
     * @param false  $is_edit     true 返回选中的option
     *
     * @return array|void
     */
    public function getOption($alias = '', $alias_value = '', $is_edit = false)
    {
        if (Router::SEARCH == $alias) {
            return [];
        }
        $remoteSearchService = new RomoteSearchService();
        switch ($alias) {
            case Router::NO_HOME_PUBLIC_PAGE:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_APP_WEBSITE_PUBLIC, $alias_value, $is_edit);
            case Router::ARTICLE_INDEX:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_ARTICLE_CAT, $alias_value, $is_edit);
            case Router::ARTICLE_DETAIL:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_ARTICLE, $alias_value, $is_edit);
            case Router::ZIXUN_ARTICLE_INDEX:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_INFO_ARTICLE_CAT, $alias_value, $is_edit);
            case Router::ZIXUN_ARTICLE_DETAIL:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_INFO_ARTICLE, $alias_value, $is_edit);
            case Router::GOODS:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_GOODS, $alias_value, $is_edit);
            case Router::STORE:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_SHOP, $alias_value, $is_edit);
            case Router::LIVE_SHOW:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_LIVE_SHOW, $alias_value, $is_edit);
			case Router::LIVE_NOTICE:
				return $remoteSearchService->getOption(RomoteSearchService::TYPE_LIVE_NOTICE, $alias_value, $is_edit);
			case Router::LIVE_RECORD:
				return $remoteSearchService->getOption(RomoteSearchService::TYPE_LIVE_RECORD, $alias_value, $is_edit);
            case Router::DOUBLE_TEN_BROWSE:
            case Router::HOME_DOUBLE_TEN_BROWSE:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_ACTIVITY_TEMPLATE, $alias_value, $is_edit);
            case Router::PRESELL_INDEX:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_ACT_PRESELL, $alias_value, $is_edit);
            case Router::PRESELL_DETAIL:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_ACT_PRESELL_GOODS, $alias_value, $is_edit);
            case Router::PROBATION_INDEX:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_ACT_DD_TRY, $alias_value, $is_edit);
            case Router::PROBATION_GOODS:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_ACT_DD_TRY_GOODS, $alias_value, $is_edit);
            case Router::AUCTION_DETAIL:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_ACT_AUCTION_GOODS, $alias_value, $is_edit);
            case Router::INTEGRAL_GOODS:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_INTEGRAL_GOODS, $alias_value, $is_edit);
            case Router::INVITE_TEMPLATE:
            case Router::QUESTIONNAIRE_SURVEY:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_INVITE_TEMPLATE, $alias_value, $is_edit);
            case Router::INDUSTRY:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_APP_WEBSITE_INDUSTRIAL, $alias_value, $is_edit);
            case Router::CATEGORY_PUBLIC:
				return $remoteSearchService->getOption(RomoteSearchService::TYPE_APP_WEBSITE_CATEGORY, $alias_value, $is_edit);
            case Router::NEW_YEAR_WELFARE:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_NEW_RED_PACKET, $alias_value, $is_edit);
            case Router::ARTICLE_INDUSTRY_SUMMIT:
                return $remoteSearchService->getOption(RomoteSearchService::TYPE_ARTICLE_CAT, $alias_value, $is_edit);
			case Router::SPECIAL:
				return $remoteSearchService->getOption(RomoteSearchService::TYPE_APP_CATEGORY, $alias_value, $is_edit);
            case Router::NEW_YEAR_MARKING:
                return $remoteSearchService->getOption(RomoteSearchService::NEW_YEAR_MARKING, $alias_value, $is_edit);
			case Router::YI_QI_XIU:
				return $remoteSearchService->getOption(RomoteSearchService::YI_QI_XIU, $alias_value, $is_edit);

		}

        return [];
    }

	/*
	 * 处理非法http数据
	 */
    private function dealIllegalHttpUrl($url)
    {
        if (false === strpos($url, 'https://') && false === strpos($url, 'http://')) {
            return '';
        }
        return $url;
    }
}
