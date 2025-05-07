<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class Sms4BDGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.sms4bd.com/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'sms4bd';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'sender_id' => $this->config['sender_id'],
            'mobile' => $to,
            'message' => $data['message']
        ];
    }
}
