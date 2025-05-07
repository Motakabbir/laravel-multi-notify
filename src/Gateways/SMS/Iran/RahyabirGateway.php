<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

class RahyabirGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.rahyabir.com/api/v1/sms/send';

    protected function getGatewayName(): string
    {
        return 'rahyabir';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'to' => $to,
            'message' => $data['message']
        ];
    }
}
