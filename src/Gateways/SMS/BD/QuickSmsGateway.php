<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class QuickSmsGateway extends BaseBDGateway
{
    protected $endpoint = 'https://quicksms.xyz/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'quicksms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'type' => 'text',
            'sender_id' => $this->config['sender_id'],
            'mobile' => $to,
            'message' => $data['message']
        ];
    }
}
