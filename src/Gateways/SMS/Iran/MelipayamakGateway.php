<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class MelipayamakGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.melipayamak');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::post($this->endpoint, [
                'username' => $this->config['username'],
                'password' => $this->config['password'],
                'to' => $number,
                'text' => $data['message'],
            ]);

            $results[] = $response->json();
        }

        return $results;
    }
}
