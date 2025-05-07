<?php

namespace LaravelMultiNotify\Jobs;

use LaravelMultiNotify\Services\MultiNotifyService;

class SendEmailJob extends BaseNotificationJob
{
    protected $channel = 'email';
    protected function getGatewayInstance(): \LaravelMultiNotify\Contracts\NotificationGateway
    {
        $service = app(MultiNotifyService::class);
        return $service->gateway('email', $this->gateway ?? config('multi-notify.email.default'));
    }
}
