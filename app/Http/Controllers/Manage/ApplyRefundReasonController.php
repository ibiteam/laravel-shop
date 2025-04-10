<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\ApplyRefundReason;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// 退款原因
class ApplyRefundReasonController extends BaseController
{
    /**
     * 列表.
     */
    public function index(Request $request)
    {
        $type = $request->get('type');
        $number = (int) $request->get('number', 10);

        $data = ApplyRefundReason::query()
            ->when(! is_null($type), fn ($query) => $query->where('type', '=', $type))
            ->orderByDesc('created_at')->paginate($number);
        $data->getCollection()->transform(function (ApplyRefundReason $apply_refund_reason) {
            return [
                'id' => $apply_refund_reason->id,
                'content' => $apply_refund_reason->content,
                'type' => strval($apply_refund_reason->type),
                'sort' => $apply_refund_reason->sort,
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }

    /**
     * 添加/编辑.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'content' => 'required|string',
                'type' => 'required|integer|in:0,1',
                'sort' => 'required|integer',
            ], [], [
                'id' => '原因ID',
                'content' => '原因',
                'type' => '类型',
                'sort' => '排序',
            ]);

            $validated['id'] = $validated['id'] ?? 0;

            if ($validated['id']) {
                $apply_refund_reason = ApplyRefundReason::whereId($validated['id'])->first();

                if (! $apply_refund_reason) {
                    throw new BusinessException('退款原因不存在');
                }

                if (ApplyRefundReason::where('id', '!=', $validated['id'])->whereContent($validated['content'])->whereType($validated['type'])->first()) {
                    throw new BusinessException('该类型下退款原因已存在');
                }
            } else {
                $apply_refund_reason = new ApplyRefundReason;

                if (ApplyRefundReason::whereContent($validated['content'])->whereType($validated['type'])->first()) {
                    throw new BusinessException('该类型下退款原因已存在');
                }
            }

            $apply_refund_reason->content = $validated['content'];
            $apply_refund_reason->type = intval($validated['type']);
            $apply_refund_reason->sort = $validated['sort'] ?? 0;

            if (! $apply_refund_reason->save()) {
                throw new BusinessException('保存失败');
            }

            if ($validated['id']) {
                $log = "编辑退款原因[id:{$apply_refund_reason->id}]".implode(',', array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($apply_refund_reason->getChanges()), $apply_refund_reason->getChanges()));
                admin_operation_log($this->adminUser(), $log, AdminOperationLog::TYPE_UPDATE);
            } else {
                $log = "新增退款原因[id:{$apply_refund_reason->id}]";
                admin_operation_log($this->adminUser(), $log, AdminOperationLog::TYPE_STORE);
            }

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('退款原因操作异常~');
        }
    }

    /**
     * 删除.
     */
    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '原因ID',
            ]);
            $apply_refund_reason = ApplyRefundReason::query()->whereId($validated['id'])->first();

            if (! $apply_refund_reason instanceof ApplyRefundReason) {
                return $this->error('退款原因不存在');
            }

            if ($apply_refund_reason->delete()) {
                // 记录日志
                admin_operation_log($this->adminUser(), "删除了退款原因:{$apply_refund_reason->content}[{$apply_refund_reason->id}]", AdminOperationLog::TYPE_DESTROY);

                return $this->success('删除成功');
            }

            throw new BusinessException('删除失败');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('退款原因删除异常');
        }
    }
}
