<?php

namespace App\Http\Controllers\Manage;

use App\Http\Dao\AdminOperationLogDao;
use App\Models\AdminOperationLog;
use App\Models\SellerEnter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 商家入驻列表
 */
class SellerEnterController extends BaseController
{

    public function index(Request $request)
    {
        if ($request->expectsJson()) {

            $user_name = $request->get('user_name');
            $admin_user_name = $request->get('admin_user_name');
            $check_status = $request->get('check_status');
            $start_time = $request->get('start_time');
            $end_time = $request->get('end_time');
            $number = (int) $request->get('number', '10');

            $query = SellerEnter::query()->orderByDesc('id')->with(['user:id,user_name', 'adminUser:id,user_name']);

            if ($user_name) {
                $query->whereHas('user', function ($created_query) use ($user_name) {
                    return $created_query->where('user_name', 'LIKE', "%{$user_name}%");
                });
            }

            if ($admin_user_name) {
                $query->whereHas('adminUser', function ($created_query) use ($admin_user_name) {
                    return $created_query->where('user_name', 'LIKE', "%{$admin_user_name}%");
                });
            }

            if ($check_status > -1) {
                $query->where('check_status', '=', $check_status);
            }

            if ($start_time) {
                $query->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($start_time)));
            }

            if ($end_time) {
                $query->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($end_time)));
            }

            $data = $query->paginate($number);

            $data->getCollection()->transform(function (SellerEnter $seller_enter) {
                return [
                    'id' => $seller_enter->id,
                    'user_name' => $seller_enter->user?->user_name,
                    'source' => $seller_enter->source,
                    'check_status' => $seller_enter->check_status,
                    'check_desc' => $seller_enter->check_desc,
                    'remark' => $seller_enter->remark,
                    'admin_user_name' => $seller_enter->adminUser?->user_name,
                    'created_at' => $seller_enter->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $seller_enter->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            return $this->success($data->toArray());
        }

        return view('manage.seller_enter.index');
    }

    /**
     * 查看.
     */
    public function show(Request $request)
    {
        $id = $request->get('id');

        $seller_enter = SellerEnter::whereId($id)->first();
        if (! $seller_enter) {
            return $this->error('入驻商家信息不存在');
        }

        $data['enter_info'] = $seller_enter->enter_info;
        $data['check_logs'] = app(AdminOperationLogDao::class)->getSellerEnterCheckByLog($seller_enter->id);

        return $this->success($data);
    }

    /**
     * 审核.
     */
    public function checkStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'check_status' => 'required|integer|in:1,2',
                'check_desc' => 'nullable|string'
            ]);

            $id = $validated['id'];
            $check_status = $validated['check_status'];
            $check_desc = $validated['check_desc'] ?? '';

            $seller_enter = SellerEnter::whereId($id)->first();
            if (! $seller_enter) {
                return $this->error('入驻商家信息不存在');
            }

            if ($seller_enter->check_status == $check_status) {
                return $this->success('数据无修改');
            }

            if($check_status == SellerEnter::CHECK_NOT_APPROVED && !$check_desc){
                return $this->success('审核不通过需要填写驳回原因');
            }

            $seller_enter->check_status = $check_status;
            $seller_enter->check_desc = $check_status == SellerEnter::CHECK_APPROVED ? '' : $check_desc;;
            $seller_enter->admin_user_id = $this->adminUser()->id ?? 0;

            if ($seller_enter->save()) {

                $log_info = $check_status == SellerEnter::CHECK_APPROVED ? '审核通过' : '审核驳回:'.$check_desc;
                admin_operation_log($this->adminUser(), $log_info, AdminOperationLog::SELLER_ENTER_CHECK, (new SellerEnter())->getTable(), $seller_enter->id);

                return $this->success('审核成功');
            }

            return $this->error('审核失败！');

        } catch (ValidationException $validationException) {
            return $this->error($validationException->validator->errors()->first());
        } catch (\Exception $exception) {
            return $this->error('审核异常');
        }
    }


    /**
     * 编辑字段.
     */
    public function updateField(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'field' => 'required|string|in:remark,sort', // 定义允许更新的字段白名单
                'field_value' => 'required'
            ]);

            $id = $validated['id'];
            $field = $validated['field'];
            $field_value = $validated['field_value'];

            $seller_enter = SellerEnter::whereId($id)->first();
            if (!$seller_enter) {
                return $this->error('入驻商家信息不存在');
            }

            if ($seller_enter->$field == $field_value) {
                return $this->success('数据无修改');
            }

            // 更新字段值
            $seller_enter->$field = $field_value;
            if ($seller_enter->save()) {

                $get_changes = $seller_enter->getChanges();
                if ($get_changes) {
                    $log_info = "修改了商家入驻列表的[id:{$seller_enter->id}]：" . implode(',', array_map(function ($k, $v) {
                            return sprintf('%s=`%s`', $k, $v);
                        }, array_keys($get_changes), $get_changes));

                    admin_operation_log($this->adminUser(), $log_info);
                }

                return $this->success('编辑成功');
            }

            return $this->error('编辑失败');
        } catch (ValidationException $validationException) {
            return $this->error($validationException->validator->errors()->first());
        } catch (\Exception $exception) {
            return $this->error('编辑字段异常');
        }
    }


}
