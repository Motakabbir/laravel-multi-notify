<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class SmsNocGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.smsnoc.com/api/v2/send';

    protected function getGatewayName(): string
    {
        return 'smsnoc';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'sender_id' => $this->config['sender_id'],
            'to' => $to,
            'message' => $data['message']
        ];
    }
}
