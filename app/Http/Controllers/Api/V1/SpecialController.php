<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Models\Goods;
use App\Models\Router;
use App\Services\AppAdService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 专题页.
 */
class SpecialController extends BaseController
{
    public function index(Request $request, AppAdService $app_ad_service)
    {
        try {
            $validated = $request->validate([
                'alias' => 'required|string',
                'cat_id' => 'nullable|string',
            ], [], [
                'alias' => '别名',
                'cat_id' => '分类ID',
            ]);

            $alias = $validated['alias'];
            $cat_id = $validated['cat_id'] ?? 0;

            // 获取title
            $router = Router::query()->whereAlias($alias)->first();
            $title = $router ? $router->name : '';

            // 根据别名获取专题页BANNER
            $banner_list = $app_ad_service->getAds(Router::$AdPath[$router->alias]);

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
