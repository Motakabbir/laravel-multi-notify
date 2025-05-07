<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class MobireachGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.mobireach.com.bd/SendTextMessage';

    protected function getGatewayName(): string
    {
        return 'mobireach';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'Username' => $this->config['username'],
            'Password' => $this->config['password'],
            'From' => $this->config['sender_id'],
            'To' => $to,
            'Message' => $data['message']
        ];
    }
}
