<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class TrubosmsGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.trubosms.com/api/v2/send-sms';

    protected function getGatewayName(): string
    {
        return 'trubosms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'sender_id' => $this->config['sender_id'],
            'number' => $to,
            'message' => $data['message']
        ];
    }
}
