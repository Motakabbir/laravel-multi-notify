<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class AlphaGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.alphasms.com.bd/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'alpha';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'sender_id' => $this->config['sender_id'],
            'to' => $to,
            'message' => $data['message']
        ];
    }
}
