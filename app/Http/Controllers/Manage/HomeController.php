<?php

namespace App\Http\Controllers\Manage;

use App\Enums\CacheNameEnum;
use App\Enums\RefererEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\AccessRecordDao;
use App\Http\Dao\CollectDao;
use App\Http\Dao\PermissionDao;
use App\Http\Dao\ShopConfigDao;
use App\Models\AccessStatistic;
use App\Models\Collect;
use App\Models\Order;
use App\Models\ShopConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class HomeController extends BaseController
{
    private array $todoList = [];   // 代办列表
    private int $todoCount = 0;     // 代办数量

    /**
     * 初始化配置.
     */
    public function config(PermissionDao $permission_dao, ShopConfigDao $shop_config_dao)
    {
        $admin_user = $this->adminUser();

        // 权限菜单
        $collect_permission_ids = Collect::query()->whereAdminUserId($admin_user->id)->select('permission_id')->get()->keyBy('permission_id')->toArray();
        $menus = $permission_dao->getTreePermissionByAdminUser($admin_user, $collect_permission_ids);

        $shop_config = $shop_config_dao->multipleConfig(
            ShopConfig::MANAGE_COLOR,
            ShopConfig::MOUSE_MOVE_COLOR,
            ShopConfig::SHOP_LOGO,
        );

        $admin_data['id'] = $admin_user['id'];
        $admin_data['user_name'] = $admin_user['user_name'];
        $admin_data['avatar'] = $admin_user['avatar'];

        return $this->success(['admin_user' => $admin_data, 'menus' => $menus, 'shop_config' => $shop_config]);
    }

    /**
     * 首页数据.
     */
    public function dashboard(AccessRecordDao $access_record_dao, CollectDao $collect_dao)
    {
        $admin_user = $this->adminUser();

        // 数量统计 todo
        $number_data['user_number'] = User::query()->count();  // 用户数
        $number_data['order_number'] = Order::query()->count();  // 订单数
        $number_data['total_transaction_value'] = 10000;  // 总交易额

        // 用户数据
        $statistic_time = [date('Y-m-d', strtotime('-1 week')), date('Y-m-d', strtotime('-1 day'))];
        $access_statistics = AccessStatistic::query()->whereBetween('statistic_date', $statistic_time)->orderByDesc('statistic_date')->get()->toArray();
        $accessStatistic = \collect();

        if (count($access_statistics)) {
            array_multisort(array_column($access_statistics, 'statistic_date'), SORT_ASC, $access_statistics);
            $referer_group = collect($access_statistics)->groupBy('referer');
            $accessStatistic['pc'] = $this->getOption($referer_group, 'PC', RefererEnum::PC->value);
            $accessStatistic['h5'] = $this->getOption($referer_group, 'H5', RefererEnum::H5->value);
        }

        // 我的收藏
        $myCollect = $collect_dao->getListByAdminUserId($admin_user->id, 12);

        // 最近访问
        $accessRecord = $access_record_dao->getListByAdminUserId($admin_user->id, 12);

        $data['number_data'] = $number_data;
        $data['access_statistic'] = $accessStatistic;
        $data['todo_list'] = $this->getTodoList();
        $data['my_collect'] = $myCollect;
        $data['access_record'] = $accessRecord;

        return $this->success($data);
    }

    /**
     * 收藏管理.
     */
    public function collectManage(PermissionDao $permission_dao, CollectDao $collect_dao)
    {
        $admin_user = $this->adminUser();

        // 收藏列表
        $collect_permissions = $collect_dao->getListByAdminUserId($admin_user->id);

        // 收藏的权限菜单
        $collect_permission_ids = array_column($collect_permissions, 'id');
        $menus = $permission_dao->fetchAndFormatPermissions($admin_user, config('auth.manage.guard'), $collect_permission_ids);

        return $this->success(['collect_permissions' => $collect_permissions, 'menus' => $permission_dao->buildTree($menus, 'index')]);
    }

    /**
     * 收藏(取消收藏)菜单.
     */
    public function collectMenu(Request $request, CollectDao $collect_dao)
    {
        $admin_user = $this->adminUser();

        try {
            $validated = $request->validate([
                'id' => 'required|integer|min:1',
            ], [], [
                'id' => '菜单ID',
            ]);
            $permission_id = $validated['id'];

            $collect_dao->createOrDelete($admin_user, $permission_id);

            return $this->success('操作成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('收藏菜单异常~'.$throwable->getMessage());
        }
    }

    /**
     * 清除缓存.
     */
    public function clearCache()
    {
        $admin_user = $this->adminUser();

        Cache::forget(CacheNameEnum::SHOP_CONFIG_ALL->value);

        Cache::tags(config('auth.manage.guard').'_permission_menus')->forget('permission_menus_'.$admin_user->id);

        return $this->success('清除成功');
    }

    private function getOption($referer_group, $name, $type)
    {
        $data = [
            'name' => '',
            'statistic_date' => [],
            'uv_number' => [],
        ];

        if (! isset($referer_group[$type])) {
            return $data;
        }
        $data['name'] = $name;
        $data['statistic_date'] = $referer_group[$type]->pluck('statistic_date');
        $data['uv_number'] = $referer_group[$type]->pluck('uv_number');

        return $data;
    }

    /**
     * 获取代办任务
     */
    private function getTodoList(): array
    {
        $this->todoList = [];

        try {
            $permission_codes = $this->adminUser()->getAllPermissions()->pluck('name')->flip();
        } catch (\Exception $e) {
            $permission_codes = [];
        }

        // todo 替换数据
        $i = 0;

        if (isset($permission_codes['manage.shop_config.index'])) {
            $order_comment_good_number = 100;
            $this->buildTodoList($i, '会员', '订单评论', $order_comment_good_number, 'manage.user.index', 'Comment');
        }

        if (isset($permission_codes['manage.shop_config.index'])) {
            $consult_number = 2;
            $this->buildTodoList($i, '会员', '购买咨询', $consult_number, 'manage.user.index', 'ChatDotSquare');
        }

        if (isset($permission_codes['manage.goods.index'])) {
            $order_pending_number = 10;
            $this->buildTodoList($i, '订单', '待付款', $order_pending_number, 'manage.goods.index', 'Notification');
        }

        if (isset($permission_codes['manage.goods.index'])) {
            $order_sending_number = 9;
            $this->buildTodoList($i, '订单', '待发货', $order_sending_number, 'manage.goods.index', 'Notification');
        }

        if (isset($permission_codes['manage.goods.index'])) {
            $order_accepting_number = 8;
            $this->buildTodoList($i, '订单', '待收货', $order_accepting_number, 'manage.goods.index', 'Notification');
        }

        if (isset($permission_codes['manage.goods.index'])) {
            $order_backing_number = 0;
            $this->buildTodoList($i, '订单', '退款申请', $order_backing_number, 'manage.goods.index', 'Notification');
        }

        return array_values($this->todoList);
    }

    /**
     * 构建待办列表.
     */
    private function buildTodoList(int &$i, string $groupName, string $title, int $count, string $name, string $icon = ''): void
    {
        $px = 60;

        if ($count < 0) {
            return;
        }

        if (! isset($this->todoList[$groupName])) {
            $this->todoList[$groupName] = [
                'group_name' => $groupName,
                'list' => [],
            ];
        }
        $this->todoList[$groupName]['list'][] = [
            'title' => $title,
            'count' => min($count, 99),
            'icon' => $icon,
            'name' => $name,  // 路由 前端页面名称
            'backgroundPosition' => $i * $px,
        ];
        $i++;
        $this->todoCount += $count;
    }
}
