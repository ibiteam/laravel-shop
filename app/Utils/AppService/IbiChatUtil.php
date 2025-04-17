<?php

declare(strict_types=1);

namespace App\Utils\AppService;

use App\Models\AppServiceConfig;

class IbiChatUtil extends BaseUtil
{
    private array $settings = [
        'host' => '',
        'platform_id' => '',
        'platform_secret' => '',
    ];

    public function __construct(int $user_id)
    {
        parent::__construct($user_id);
        $this->setSettings();
    }

    public function setSettings(): void
    {
        $this->settings = array_merge($this->settings, $this->config->config);
    }

    protected static function getAlias(): string
    {
        return AppServiceConfig::IBI_CHAT;
    }
}
