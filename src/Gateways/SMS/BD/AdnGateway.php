<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class AdnGateway extends BaseBDGateway
{
    protected $endpoint = 'https://portal.adnsms.com/api/v1/secure/send-sms';

    protected function getGatewayName(): string
    {
        return 'adn';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'api_secret' => $this->config['api_secret'],
            'mobile' => $to,
            'message' => $data['message'],
            'request_type' => 'SINGLE_SMS'
        ];
    }
}
