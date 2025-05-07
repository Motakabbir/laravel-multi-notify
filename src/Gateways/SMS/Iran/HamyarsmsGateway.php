<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class HamyarsmsGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://rest.hamyarsms.com/api/v1/messages';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.hamyarsms');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];

        $response = Http::withHeaders([
            'X-API-KEY' => $this->config['api_key']
        ])->post($this->endpoint, [
            'mobile_numbers' => $numbers,
            'message' => $data['message'],
            'send_at' => now()->format('Y-m-d H:i:s')
        ]);

        return $response->json();
    }
}
