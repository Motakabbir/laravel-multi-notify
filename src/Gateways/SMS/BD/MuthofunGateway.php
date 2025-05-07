<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class MuthofunGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.muthofun.com/sms/v1/send';

    protected function getGatewayName(): string
    {
        return 'muthofun';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'to' => $to,
            'message' => $data['message']
        ];
    }
}
