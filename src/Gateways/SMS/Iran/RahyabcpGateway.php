<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

class RahyabcpGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.rahyabcp.com/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'rahyabcp';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'to' => $to,
            'text' => $data['message']
        ];
    }
}
