<?php

namespace App\Traits;

use DateTimeInterface;

trait DatetimeTrait
{
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
