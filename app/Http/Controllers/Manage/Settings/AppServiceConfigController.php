<?php

namespace App\Http\Controllers\Manage\Settings;

use App\Http\Controllers\Manage\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\AppServiceConfig;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AppServiceConfigController extends BaseController
{
    public function index(Request $request)
    {
        $name = $request->input('name', '');
        $alias = $request->input('alias', '');
        $is_enable = $request->input('is_enable', '-1');
        $desc = $request->get('desc', '');
        $list = AppServiceConfig::query()->latest()
            ->when($name, fn (Builder $query) => $query->whereLike('name', '%'.$name.'%'))
            ->when($alias, fn (Builder $query) => $query->whereLike('alias', '%'.$alias.'%'))
            ->when($desc, fn (Builder $query) => $query->whereLike('desc',  '%'.$desc.'%'))
            ->when($is_enable != '-1', fn (Builder $query) => $query->whereIsEnable($is_enable))
            ->orderByDesc('id')
            ->paginate((int) $request->get('number', 10));
        $list->getCollection()->transform(function (AppServiceConfig $app_service_config) {
            $app_service_config->error_name = $app_service_config->error_number == 0 ? '无限制' : $app_service_config->error_number;
            $app_service_config->stop_name = $app_service_config->stop_name == 0 ? '无限制' : $app_service_config->stop_name;

            return $app_service_config;
        });

        return $this->success(new CommonResourceCollection($list));
    }

    /**
     * 切换状态
     */
    public function toggleStatus(Request $request)
    {
        $id = $request->get('id') ?: 0;
        $sign = $request->get('sign', '');
        $res = AppServiceConfig::whereId($id)->first();

        if (! $res) {
            return $this->error('配置不存在！');
        }
        $res->$sign = $res->$sign == 1 ? 0 : 1;

        if (! $res->save()) {
            return $this->error('编辑失败！');
        }

        switch ($sign) {
            case 'is_enable':
                if ($res->is_enable == AppServiceConfig::IS_ENABLE) {
                    $msg = '更改三方服务配置状态为启用';
                } else {
                    $msg = '更改三方服务配置状态为不启用';
                }

                // 记录日志
                admin_operation_log( "{$msg},三方服务名:【{$res->name}】", AdminOperationLog::TYPE_UPDATE);
                break;

            case 'is_record':
                if ($res->is_record == AppServiceConfig::IS_RECORD) {
                    $msg = '更改三方服务配置为记录日志';
                } else {
                    $msg = '更改三方服务配置为不记录日志';
                }
                admin_operation_log( "{$msg},三方服务名:【{$res->name}】", AdminOperationLog::TYPE_UPDATE);

                break;
        }

        return $this->success('成功');
    }

    public function update(Request $request)
    {
        $id = $request->get('id', '');
        $name = $request->get('name', '');
        $alias = $request->get('alias', '');
        $config = $request->get('config', []);
        $is_enable = $request->get('is_enable', 1);
        $is_record = $request->get('is_record', 0);
        $error_number = $request->get('error_number');
        $stop_number = $request->get('stop_number');
        $desc = $request->get('desc', '');
        $query = AppServiceConfig::whereId($id)->first();

        if (! $query) {
            return $this->error('数据不存在，请刷新页面重试！');
        }

        $query->name = $name;
        $query->config = json_encode($config);
        $query->is_enable = $is_enable;
        $query->is_record = $is_record;
        $query->error_number = $error_number;
        $query->stop_number = $stop_number;
        $query->desc = $desc;

        if ($query->save()) {
            admin_operation_log( "更新了别名:【{$alias}】的配置", AdminOperationLog::TYPE_UPDATE);

            return $this->success('操作成功');
        }

        return $this->error('操作失败');
    }
}
