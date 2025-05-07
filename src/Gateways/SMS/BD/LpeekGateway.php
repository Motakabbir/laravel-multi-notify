<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class LpeekGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.lpeek.com/sms/v1/send';

    protected function getGatewayName(): string
    {
        return 'lpeek';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_token' => $this->config['api_token'],
            'sender_id' => $this->config['sender_id'],
            'phone' => $to,
            'message' => $data['message']
        ];
    }
}
