<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class OnnoRokomSMSGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.onnorokomsms.com/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'onnorokomsms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'type' => 'text',
            'number' => $to,
            'message' => $data['message']
        ];
    }
}
