<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class MimSmsGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.mimsms.com.bd/api/v3/sendsms';

    protected function getGatewayName(): string
    {
        return 'mimsms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'type' => 'text',
            'number' => $to,
            'sender_id' => $this->config['sender_id'],
            'message' => $data['message']
        ];
    }
}
