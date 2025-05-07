<?php

namespace LaravelMultiNotify\Jobs;

use LaravelMultiNotify\Services\MultiNotifyService;

class SendSmsJob extends BaseNotificationJob
{
    protected $channel = 'sms';    protected function getGatewayInstance(): \LaravelMultiNotify\Contracts\NotificationGateway
    {
        $service = app(MultiNotifyService::class);
        return $service->gateway('sms', $this->gateway ?? config('multi-notify.sms.default'));
    }
}
