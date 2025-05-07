<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class BulksmsBDGateway extends BaseBDGateway
{
    protected $endpoint = 'https://bulksmsbd.net/api/smsapi';

    protected function getGatewayName(): string
    {
        return 'bulksmsbd';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'senderid' => $this->config['sender_id'],
            'number' => $to,
            'message' => $data['message']
        ];
    }
}
