<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class RedmoITSmsGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.redmoit.com/api/v1/send-message';

    protected function getGatewayName(): string
    {
        return 'redmoitsms';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_token' => $this->config['api_token'],
            'sender_id' => $this->config['sender_id'],
            'recipient' => $to,
            'message' => $data['message']
        ];
    }
}
