<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Dao\AccessRecordDao;
use App\Http\Dao\PermissionDao;
use App\Models\Collect;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HomeController extends BaseController
{
    /**
     * 获取菜单
     */
    public function menus(Request $request, PermissionDao $permission_dao)
    {
        $admin_user = $this->adminUser();

        // 收藏的菜单ID
        $collect_permission_ids = Collect::query()->whereAdminUserId($admin_user->id)->select('permission_id')->get()->keyBy('permission_id')->toArray();

        $menus = $permission_dao->getTreePermissionByAdminUser($admin_user, $collect_permission_ids);
        return $this->success($menus);
    }

    /**
     * 首页数据
     */
    public function dashboard(AccessRecordDao $access_record_dao)
    {
        $admin_user = $this->adminUser();

        // 我的收藏
        $myCollect = Collect::query()->with('permission:id,name,display_name,icon')
            ->whereAdminUserId($admin_user->id)->orderByDesc('updated_at')
            ->limit(12)->get()->map(function (Collect $item) {
                return [
                    'id' => $item->permission_id,
                    'title' => $item->permission->display_name,
                    'name' => $item->permission->name,
                    'icon' => $item->permission->icon,
                ];
            });

        // 最近访问
        $accessRecord = $access_record_dao->getListByAdminUserId($admin_user->id);

        // 用户数据
        // $statistic_time = [date('Y-m-d', strtotime('-1 week')), date('Y-m-d', strtotime('-1 day'))];
        // $access_statistics = AccessStatistic::query()->whereBetween('statistic_date', $statistic_time)->orderByDesc('statistic_date')->get()->toArray();
        // $access_statistic = \collect();
        //
        // if (count($access_statistics)) {
        //     array_multisort(array_column($access_statistics, 'statistic_date'), SORT_ASC, $access_statistics);
        //     $res = collect($access_statistics)->groupBy('referer');
        //     $access_statistic['pc'] = $this->getOption($res, 'PC', 'pc');
        //     $access_statistic['h5'] = $this->getOption($res, 'Admin', 'h5');
        // }

        $data['access_statistic'] = [];
        $data['my_collect'] = $myCollect;
        $data['access_record'] = $accessRecord;

        return $this->success($data);
    }

    private function getOption($res, $name, $type)
    {
        $data = [
            'name' => '',
            'statistic_date' => [],
            'uv_number' => [],
        ];
        if (!isset($res[$type])) {
            return $data;
        }
        $data['name'] = $name;
        $data['statistic_date'] = $res[$type]->pluck('statistic_date');
        $data['uv_number'] = $res[$type]->pluck('uv_number');
        return $data;
    }

    /**
     * 收藏菜单
     */
    public function collectMenu(Request $request)
    {
        $admin_user = $this->adminUser();

        try {
            $validated = $request->validate([
                'id' => 'required|integer|min:1',
            ], [], [
                'id' => '菜单ID',
            ]);
            $menu_id = $validated['id'];
            $menu = Permission::query()->whereId($menu_id)->first();
            if (!$menu) {
                throw new BusinessException('菜单不存在');
            }

            $permission_codes = $admin_user->getAllPermissions()->pluck('name')->flip();
            if (!isset($permission_codes[$menu->name])) {
                throw new BusinessException('没有权限');
            }

            $collect = Collect::query()->whereAdminUserId($admin_user->id)->wherePermissionId($menu_id)->first();
            if ($collect) {
                $collect->delete();
                return $this->success('取消收藏成功');
            } else {
                Collect::create([
                    'admin_user_id' => $admin_user->id,
                    'permission_id' => $menu_id
                ]);
                return $this->success('收藏成功');
            }

        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('收藏菜单异常~' . $throwable->getMessage());
        }
    }
}
