<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class InfobipGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.infobip.com/sms/2/text/advanced';

    protected function getGatewayName(): string
    {
        return 'infobip';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'messages' => [
                [
                    'from' => $this->config['from'],
                    'destinations' => [['to' => $to]],
                    'text' => $data['message']
                ]
            ]
        ];
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'App ' . $this->config['api_key'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }
}
