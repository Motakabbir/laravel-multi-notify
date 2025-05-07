<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class ZamanITGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.zamanit.com.bd/api/v2/send';

    protected function getGatewayName(): string
    {
        return 'zamanit';
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
