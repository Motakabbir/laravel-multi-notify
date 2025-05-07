<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class TextlocalGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://api.textlocal.in/send/';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.textlocal');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];

        $response = Http::post($this->endpoint, [
            'apikey' => $this->config['apikey'],
            'numbers' => implode(',', $numbers),
            'message' => $data['message'],
            'sender' => $this->config['sender']
        ]);

        return $response->json();
    }
}
