<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class SabaPayamakGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'http://api.sabapayamak.com/api/v1/sms/send';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.sabapayamak');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];

        $response = Http::withBasicAuth(
            $this->config['username'],
            $this->config['password']
        )->post($this->endpoint, [
            'to' => $numbers,
            'text' => $data['message']
        ]);

        return $response->json();
    }
}
