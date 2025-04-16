<?php

namespace App\Http\Controllers\Manage\Settings;

use App\Enums\CacheNameEnum;
use App\Http\Controllers\Manage\BaseController;
use App\Http\Dao\ShopConfigDao;
use App\Models\AdminOperationLog;
use App\Models\ShopConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ShopConfigController extends BaseController
{
    /**
     * 获取配置信息.
     */
    public function index(Request $request, ShopConfigDao $shop_config_dao)
    {
        try {
            $validated = $request->validate([
                'group_name' => 'required|string',
            ], [], [
                'group_name' => '组名',
            ]);

            $group_name = $validated['group_name'] ?? 'site_info';

            return $this->success($shop_config_dao->getConfigByGroupName($group_name));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (\Throwable $throwable) {
            return $this->error('获取配置信息异常~');
        }
    }

    /**
     * 更新配置.
     */
    public function update(Request $request, ShopConfigDao $shop_config_dao)
    {
        $tab_label = $request->get('tab_label');
        $data = $request->all();

        $all_codes = ShopConfig::query()->pluck('code')->toArray();

        foreach (array_keys($data) as $code) {
            if (! in_array($code, $all_codes)) {
                continue;
            }

            ShopConfig::whereCode($code)->update(['value' => json_encode($data[$code] ?? '')]);
        }

        // 删除缓存
        Cache::forget(CacheNameEnum::SHOP_CONFIG_ALL->value);

        // 重新更新缓存
        $shop_config_dao->getAll();

        admin_operation_log($this->adminUser(), '更新了商店设置的【'.$tab_label.'】', AdminOperationLog::TYPE_UPDATE);

        return $this->success('更新成功');
    }
}
