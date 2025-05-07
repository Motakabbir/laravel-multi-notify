<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class ElitBuzzGateway extends BaseBDGateway
{
    protected $endpoint = 'https://msg.elitbuzz-bd.com/smsapi';

    protected function getGatewayName(): string
    {
        return 'elitbuzz';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'type' => 'text',
            'contacts' => $to,
            'senderid' => $this->config['sender_id'],
            'msg' => $data['message']
        ];
    }
}
