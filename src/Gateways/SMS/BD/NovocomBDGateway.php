<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class NovocomBDGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.novocom-bd.com/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'novocombd';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'sender_id' => $this->config['sender_id'],
            'recipient' => $to,
            'message' => $data['message']
        ];
    }
}
