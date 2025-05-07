<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class GlobeLabsGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://devapi.globelabs.com.ph/smsmessaging/v1/outbound';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.globe_labs');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("{$this->endpoint}/{$this->config['sender_address']}/requests", [
                'access_token' => $this->config['access_token'],
                'address' => $number,
                'message' => $data['message'],
                'senderAddress' => $this->config['sender_address']
            ]);

            $results[] = $response->json();
        }

        return $results;
    }
}
