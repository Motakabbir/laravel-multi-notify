<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class SMSNet24Gateway extends BaseBDGateway
{
    protected $endpoint = 'https://24smsnet.com/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'smsnet24';
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
