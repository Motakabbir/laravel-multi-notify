<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class DurjoySoftGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.durjoysoft.com/api/v2/send-sms';

    protected function getGatewayName(): string
    {
        return 'durjoysoft';
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
