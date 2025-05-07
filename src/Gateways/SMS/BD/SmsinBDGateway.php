<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class SmsinBDGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.smsinbd.com/api/v1/send-sms';

    protected function getGatewayName(): string
    {
        return 'smsinbd';
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
