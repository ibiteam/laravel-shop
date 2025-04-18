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
                'alias' => 'required|string',
                'title' => 'nullable|string',
                'cat_id' => 'nullable|string',
            ], [], [
                'alias' => '别名',
                'title' => '标题',
                'cat_id' => '分类ID',
            ]);

            // todo 根据别名获取专题页BANNER 这里先写死
            $banner_list = [
                'https://cdn.toodudu.com/2024/04/12/0bAiUiXtfMxIMXIqsdStKYp7aeabL8IjUNt8eHNa.jpg',
                'https://cdn.toodudu.com/2021/03/24/i2DGzLEd3UrgZQYVqTl9xaqvsQIrvvFixl6aErJl.png',
                'https://cdn.toodudu.com/1582c5ff87961214faad7bf3cee76a95.jpg',
            ];

            $title = $validated['title'] ?? '';
            $cat_id = $validated['cat_id'] ?? 0;

            // 获取商品数据
            $good_list = Goods::query()->show()
                ->select(['no', 'name', 'category_id', 'sub_name', 'label', 'price', 'unit', 'integral', 'image', 'sales_volume', 'created_at'])
                ->when($cat_id, fn ($query) => $query->whereCategoryId($cat_id))
                ->orderBy('sort', 'desc')
                ->limit(10)
                ->get();

            $data = [
                'title' => $title,
                'banner_list' => $banner_list,
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
