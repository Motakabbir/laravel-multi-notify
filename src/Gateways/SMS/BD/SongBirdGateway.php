<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

class SongBirdGateway extends BaseBDGateway
{
    protected $endpoint = 'https://api.songbird.com.bd/api/v2/send';

    protected function getGatewayName(): string
    {
        return 'songbird';
    }

    protected function buildPayload($to, array $data): array
    {
        return [
            'api_token' => $this->config['api_token'],
            'sender_id' => $this->config['sender_id'],
            'phone' => $to,
            'message' => $data['message']
        ];
    }
}
