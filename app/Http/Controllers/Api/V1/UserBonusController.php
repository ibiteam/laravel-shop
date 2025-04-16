<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\Bonus;
use App\Models\Category;
use App\Models\Goods;
use App\Models\UserBonus;
use Illuminate\Http\Request;

class UserBonusController extends BaseController
{
    public function index()
    {
        $user = $this->user();
        $data = UserBonus::query()
            ->whereUserId($user?->id)
            ->with('bonus')
            ->paginate(10);

        $data->getCollection()->transform(function (UserBonus $item) {
            $bonus = $item->bonus;

            return [
                'name' => $bonus->name,
                'money' => $bonus->money,
                'use_start_time' => $bonus->use_start_time,
                'use_end_time' => $bonus->use_end_time,
                'min_amount' => $bonus->min_amount,
                'type' => $bonus->type,
                'limit_name' => $this->getLimitName($bonus->type, $bonus->type_values),
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
