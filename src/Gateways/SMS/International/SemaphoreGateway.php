<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class SemaphoreGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://api.semaphore.co/api/v4/messages';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.semaphore');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::post($this->endpoint, [
                'apikey' => $this->config['api_key'],
                'number' => $number,
                'message' => $data['message'],
                'sendername' => $this->config['sender_name'] ?? ''
            ]);

            $results[] = $response->json();
        }

        return $results;
    }
}
