<?php

namespace LaravelMultiNotify\Jobs;

use LaravelMultiNotify\Services\MultiNotifyService;

class SendPushJob extends BaseNotificationJob
{
    protected $channel = 'push';    protected function getGatewayInstance(): \LaravelMultiNotify\Contracts\NotificationGateway
    {
        $service = app(MultiNotifyService::class);
        return $service->gateway('push', $this->gateway ?? config('multi-notify.push.default'));
    }
}
