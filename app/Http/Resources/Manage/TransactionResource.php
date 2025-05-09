<?php

namespace App\Http\Resources\Manage;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! $this->resource instanceof Transaction) {
            return [];
        }

        return [
            'id' => $this->resource->id,
            'transaction_no' => $this->resource->transaction_no,
            'parent_transaction_no' => $this->resource->parent?->transaction_no,
            'type' => match ($this->resource->type) {
                Order::class => 'order',
                default => '',
            },
            'type_no' => match ($this->resource->type) {
                Order::class => $this->resource->typeInfo?->order_sn,
                default => '',
            },
            'amount' => $this->resource->amount,
            'transaction_type' => $this->resource->transaction_type,
            'user_name' => $this->resource->user?->user_name,
            'payment_name' => $this->resource->payment?->name,
            'status' => $this->resource->status,
            'created_at' => $this->resource->created_at->format('Y-m-d H:i:s'),
            'paid_at' => $this->resource->paid_at,
            'remark' => $this->resource->remark,
            'can_refund' => $this->resource->can_refund,
        ];
    }
}
