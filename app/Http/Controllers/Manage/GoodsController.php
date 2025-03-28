<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\GoodsStoreRequest;
use App\Services\Goods\GoodsService;
use Illuminate\Validation\ValidationException;

class GoodsController extends BaseController
{
    public function store(GoodsStoreRequest $request, GoodsService $goods_service)
    {
        $validated = $request->validated();

        try {
            $goods_service->storeOrUpdate($this->adminUser(), $request->validated(), $validated['goods_sn']);

            return $this->success('操作成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (\Throwable $throwable) {
            return $this->error('添加失败'.$throwable->getMessage());
        }
    }
}
