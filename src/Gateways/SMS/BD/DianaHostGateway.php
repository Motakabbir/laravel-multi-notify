<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class DianaHostGateway extends BaseBDGateway
{
    protected $endpoint = 'https://dianahost.com/api/sms/send';

    protected function getGatewayName(): string
    {
        return 'dianahost';
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
