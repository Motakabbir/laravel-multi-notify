<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class AjuraTechGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.ajuratech.com/send-sms';

    protected function getGatewayName(): string
    {
        return 'ajuratech';
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
