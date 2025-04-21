<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $value instanceof DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value,
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $value instanceof DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value,
        );
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
