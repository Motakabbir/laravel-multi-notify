<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class SslsmsGateway extends BaseBDGateway
{
    protected $endpoint = 'https://sslsms.api.example.com/api/v3/send-sms';

    protected function getGatewayName(): string
    {
        return 'sslsms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_token' => $this->config['api_token'],
            'sid' => $this->config['sid'],
            'mobile' => $to,
            'sms' => $data['message'],
            'csms_id' => uniqid(),
        ];
    }
}
