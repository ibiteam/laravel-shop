<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\Bonus;
use App\Models\Category;
use App\Models\Goods;
use App\Models\UserCoupon;
use Illuminate\Http\Request;

class UserCouponController extends BaseController
{
    public function index()
    {
        $user = $this->user();
        $data = UserCoupon::query()
            ->whereUserId($user?->id)
            ->with('coupon')
            ->paginate(10);

        $data->getCollection()->transform(function (UserCoupon $item) {
            $coupon = $item->coupon;

            return [
                'name' => $coupon->name,
                'money' => $coupon->money,
                'start_time' => $coupon->start_time,
                'end_time' => $coupon->end_time,
                'min_amount' => $coupon->min_amount,
                'type' => $coupon->type,
                'limit_name' => $this->getLimitName($coupon->type, $coupon->type_values),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }

    // 根据限制类型获取限制的品类或者商品名称
    private function getLimitName(int $type, ?string $typeValues)
    {
        $typeValues = explode(',', $typeValues);
        switch ($type) {
            case Bonus::TYPE_GOODS:
                return Goods::whereIn('id', $typeValues)->value('name');
            case Bonus::TYPE_CATEGORY:
                return Category::whereIn('id', $typeValues)->value('name');
            default:
                return '';
        }
    }
}
