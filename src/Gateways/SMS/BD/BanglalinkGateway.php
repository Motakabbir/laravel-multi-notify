<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class BanglalinkGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.banglalink.net/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'banglalink';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'sender_id' => $this->config['sender_id'],
            'msisdn' => $to,
            'message' => $data['message']
        ];
    }
}
