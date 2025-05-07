<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class MDLGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.mdl.com.bd/api/v1/send-sms';

    protected function getGatewayName(): string
    {
        return 'mdl';
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
