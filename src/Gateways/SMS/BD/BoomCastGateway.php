<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class BoomCastGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.boomcast.com.bd/api/v1/send-sms';

    protected function getGatewayName(): string
    {
        return 'boomcast';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'masking' => $this->config['masking'],
            'mobile_no' => $to,
            'message' => $data['message']
        ];
    }
}
