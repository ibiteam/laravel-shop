<?php

namespace App\Services\AccessLog;

use App\Enums\RefererEnum;
use DateTimeInterface;

class AdminAccessLogFormatter
{
    private int $admin_user_id = 0;

    private string $ip = '';

    private string $url = '';

    private RefererEnum $source;

    private string $method;

    private string $referer_url;

    private string $user_agent;

    private string $browser;

    private string $system;

    private string $request_data = '';

    private DateTimeInterface $access_datetime;

    public function __construct()
    {
        $this->access_datetime = now();
        $this->ip = get_request_ip();
        $this->system = get_system();
        $this->browser = get_custom_browser();
        $this->source = RefererEnum::MANAGE;
    }

    public function setAdminUserId(int $admin_user_id): void
    {
        $this->admin_user_id = $admin_user_id;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setMethod(string $method): void
    {
        $this->method = strtoupper($method);
    }

    public function setRefererUrl(string $referer_url): void
    {
        $this->referer_url = $referer_url;
    }

    public function setUserAgent(string $user_agent): void
    {
        $this->user_agent = $user_agent;
    }

    public function setRequestData(array $request_data): void
    {
        $this->request_data = json_encode($request_data, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
    }

    public function toArray(): array
    {
        return [
            'admin_user_id' => $this->admin_user_id,
            'ip' => $this->ip,
            'url' => $this->url,
            'source' => $this->source->value,
            'method' => $this->method,
            'referer_url' => $this->referer_url,
            'user_agent' => $this->user_agent,
            'browser' => $this->browser,
            'system' => $this->system,
            'request_data' => $this->request_data,
            'access_datetime' => $this->access_datetime->format('Y-m-d H:i:s'),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
    }
}
