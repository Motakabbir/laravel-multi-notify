<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class DianaSMSGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.dianasms.com/api/v3/send';

    protected function getGatewayName(): string
    {
        return 'dianasms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'sender_id' => $this->config['sender_id'],
            'mobile' => $to,
            'sms' => $data['message']
        ];
    }
}
