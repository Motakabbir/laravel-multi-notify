<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class SmsBangladeshGateway extends BaseBDGateway
{
    protected $endpoint = 'http://api.smsbangladesh.com/sms-api/sendsms';

    protected function getGatewayName(): string
    {
        return 'smsbangladesh';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'senderid' => $this->config['sender_id'],
            'recipient' => $to,
            'message' => $data['message']
        ];
    }
}
