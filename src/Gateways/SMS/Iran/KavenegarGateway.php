<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class KavenegarGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://api.kavenegar.com/v1';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.kavenegar');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::get("{$this->endpoint}/{$this->config['api_key']}/sms/send.json", [
                'receptor' => $number,
                'message' => $data['message'],
                'sender' => $this->config['sender']
            ]);

            $results[] = $response->json();
        }

        return $results;
    }
}
