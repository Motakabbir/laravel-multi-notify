<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class BDBulkSMSGateway extends BaseBDGateway
{
    protected $endpoint = 'http://api.bdbulksms.net/api/v1/send-sms';

    protected function getGatewayName(): string
    {
        return 'bdbulksms';
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
