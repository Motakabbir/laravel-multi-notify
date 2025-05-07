<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class SmartLabSMSGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.smartlabsms.com/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'smartlabsms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_token' => $this->config['api_token'],
            'sender' => $this->config['sender_id'],
            'to' => $to,
            'message' => $data['message']
        ];
    }
}
