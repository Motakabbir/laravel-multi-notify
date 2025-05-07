<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class MetronetGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.metronet.com.bd/api/v1/sendsms';

    protected function getGatewayName(): string
    {
        return 'metronet';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_token' => $this->config['api_token'],
            'sender_id' => $this->config['sender_id'],
            'mobile' => $to,
            'message' => $data['message']
        ];
    }
}
