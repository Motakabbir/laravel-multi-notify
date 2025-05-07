<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class GrameenphoneGateway extends BaseBDGateway
{
    protected $endpoint = 'https://gp.api.example.com/sms/v1/send';

    protected function getGatewayName(): string
    {
        return 'grameenphone';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'msisdn' => $to,
            'message' => $data['message'],
            'cli' => $this->config['sender_id'] ?? '',
        ];
    }
}
