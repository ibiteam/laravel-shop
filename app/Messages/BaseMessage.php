<?php

namespace App\Messages;

use App\Models\PhoneMsg;
use Overtrue\EasySms\Message;

class BaseMessage extends Message
{
    protected array $info;

    protected int $send_type = PhoneMsg::PHONE_NOTICE;

    public function getInfo(): array
    {
        return $this->info;
    }

    public function setInfo(array $info): void
    {
        $this->info = $info;
    }

    public function getSendType(): int
    {
        return $this->send_type;
    }

    public function setSendType(int $send_type): void
    {
        $this->send_type = $send_type;
    }
}
