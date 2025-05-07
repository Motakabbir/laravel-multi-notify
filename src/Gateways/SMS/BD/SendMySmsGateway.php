<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class SendMySmsGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.sendmysms.com.bd/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'sendmysms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_token' => $this->config['api_token'],
            'sender_id' => $this->config['sender_id'],
            'recipient' => $to,
            'message' => $data['message']
        ];
    }
}
