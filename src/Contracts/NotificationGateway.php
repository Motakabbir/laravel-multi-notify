<?php

namespace LaravelMultiNotify\Contracts;

interface NotificationGateway
{
    /**
     * Send notification through the gateway
     *
     * @param string|array $to
     * @param array $data
     * @return mixed
     */
    public function send($to, array $data);
}
