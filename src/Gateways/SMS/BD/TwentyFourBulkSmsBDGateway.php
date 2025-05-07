<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class TwentyFourBulkSmsBDGateway extends BaseBDGateway
{
    protected $endpoint = 'https://24bulksmsbd.com/api/bulksms/send';

    protected function getGatewayName(): string
    {
        return 'twentyfourbulksmsbd';
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
