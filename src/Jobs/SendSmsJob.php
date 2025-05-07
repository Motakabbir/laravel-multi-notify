<?php

namespace LaravelMultiNotify\Jobs;

use LaravelMultiNotify\Services\MultiNotifyService;

class SendSmsJob extends BaseNotificationJob
{
    protected $channel = 'sms';

    protected function getGatewayInstance()
    {
        $service = app(MultiNotifyService::class);
        return $service->gateway('sms', $this->gateway ?? config('multi-notify.sms.default'));
    }
}
