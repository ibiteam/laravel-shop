<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Models\Goods;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 专题页.
 */
class SpecialController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|int',
            ], [], [
                'id' => '专题ID',
            ]);

            // 根据ID获取专题页数据 这里先写死
            $special_data = [
                'title' => '标题',
                'banner' => 'banner图',
                'category_id' => 0,
            ];

            $good_list = Goods::query()->show()
                ->select(['no', 'name', 'category_id', 'sub_name', 'label', 'price', 'unit', 'integral', 'image', 'sales_volume', 'created_at'])
                ->when($special_data['category_id'], fn ($query) => $query->whereCategoryId($special_data['category_id']))
                ->orderBy('sort', 'desc')
                ->limit(10)
                ->get();

            $data = [
                'title' => $special_data['title'],
                'banner' => $special_data['banner'],
                'goods_list' => $good_list,
            ];

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取专题页面数据异常~');
        }
    }
}
