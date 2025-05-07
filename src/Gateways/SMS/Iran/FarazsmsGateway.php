<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

class FarazsmsGateway extends BaseBDGateway
{
    protected $endpoint = 'https://ippanel.com/api/select';

    protected function getGatewayName(): string
    {
        return 'farazsms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'op' => 'send',
            'uname' => $this->config['username'],
            'pass' => $this->config['password'],
            'from' => $this->config['from'],
            'to' => $to,
            'message' => $data['message']
        ];
    }
}
