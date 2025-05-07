<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class ViatechGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.viatech.com.bd/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'viatech';
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
