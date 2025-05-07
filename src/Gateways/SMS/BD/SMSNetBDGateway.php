<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class SMSNetBDGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.smsnetbd.com/sms/api/v1/send';

    protected function getGatewayName(): string
    {
        return 'smsnetbd';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'sender_id' => $this->config['sender_id'],
            'mobile' => $to,
            'message' => $data['message']
        ];
    }
}
