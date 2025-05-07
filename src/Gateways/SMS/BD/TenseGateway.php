<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class TenseGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.tensesms.com/sms/api/send';

    protected function getGatewayName(): string
    {
        return 'tense';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'sender_id' => $this->config['sender_id'],
            'to' => $to,
            'message' => $data['message']
        ];
    }
}
