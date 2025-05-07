<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class EsmsGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.esms.com.bd/smsapi';

    protected function getGatewayName(): string
    {
        return 'esms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'type' => 'text',
            'senderid' => $this->config['sender_id'],
            'mobile' => $to,
            'message' => $data['message']
        ];
    }
}
