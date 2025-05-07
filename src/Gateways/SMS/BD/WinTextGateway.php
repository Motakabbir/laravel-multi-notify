<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class WinTextGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.wintext.com.bd/api/v3/sendsms';

    protected function getGatewayName(): string
    {
        return 'wintext';
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
