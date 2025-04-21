<?php

namespace App\Messages;

use App\Enums\PhoneMsgTypeEnum;
use Overtrue\EasySms\Message;

class BaseMessage extends Message
{
    protected array $info;

    protected PhoneMsgTypeEnum $enum;

    public function getInfo(): array
    {
        return $this->info;
    }

    public function setInfo(array $info): void
    {
        $this->info = $info;
    }

    public function getEnum(): PhoneMsgTypeEnum
    {
        return $this->enum;
    }

    public function setEnum(PhoneMsgTypeEnum $enum): void
    {
        $this->enum = $enum;
    }
}
