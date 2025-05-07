<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class Twenty4BulkSmsGateway extends BaseBDGateway
{
    protected $endpoint = 'https://24bulksms.com/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'twenty4bulksms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'sender_id' => $this->config['sender_id'],
            'recipient' => $to,
            'message' => $data['message']
        ];
    }
}
