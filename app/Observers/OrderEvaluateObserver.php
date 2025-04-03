<?php

namespace App\Observers;

use App\Models\OrderEvaluate;
use App\Models\OrderEvaluateCount;

class OrderEvaluateObserver
{
    /**
     * Handle the OrderEvaluate "updated" event.
     */
    public function updated(OrderEvaluate $order_evaluate): void
    {
        if ($order_evaluate->isDirty('status') && $order_evaluate->status === OrderEvaluate::STATUS_SUCCESS) {
            $order_evaluate_count = OrderEvaluateCount::query()->firstOrCreate(
                ['goods_id' => $order_evaluate->goods_id],
                [
                    'total' => 0,
                    'has_image_total' => 0,
                    'rank_total' => 0,
                    'goods_rank_total' => 0,
                    'price_rank_total' => 0,
                    'bus_rank_total' => 0,
                    'delivery_rank_total' => 0,
                    'service_rank_total' => 0,
                ]
            );
            $order_evaluate_count->total += 1;

            if (! empty($order_evaluate->images)) {
                $order_evaluate_count->has_image_total += 1;
            }

            $rank_fields = [
                'rank' => 'rank_total',
                'goods_rank' => 'goods_rank_total',
                'price_rank' => 'price_rank_total',
                'bus_rank' => 'bus_rank_total',
                'delivery_rank' => 'delivery_rank_total',
                'service_rank' => 'service_rank_total',
            ];

            foreach ($rank_fields as $field => $counter) {
                if ($order_evaluate->$field > 4) {
                    $order_evaluate_count->$counter += 1;
                }
            }

            $order_evaluate_count->save();
        }
    }
}
