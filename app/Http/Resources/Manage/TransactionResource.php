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
            'type' => match ($this->resource->type) {
                Order::class => 'order',
                default => '',
            },
            'type_no' => match ($this->resource->type) {
                Order::class => $this->resource->typeInfo?->no,
                default => '',
            },
            'amount' => $this->resource->amount,
            'transaction_type' => $this->resource->transaction_type,
            'user_name' => $this->resource->user?->user_name,
            'payment_name' => $this->resource->payment?->name,
            'status' => $this->resource->status,
            'created_at' => $this->resource->created_at->toDateTimeString(),
            'paid_at' => $this->resource->paid_at,
            'remark' => $this->resource->remark,
        ];
    }
}
