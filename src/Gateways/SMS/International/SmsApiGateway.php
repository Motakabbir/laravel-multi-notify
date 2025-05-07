<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class SmsApiGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://api.smsapi.com/sms.do';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.smsapi');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];

        $response = Http::asForm()->post($this->endpoint, [
            'access_token' => $this->config['access_token'],
            'to' => implode(',', $numbers),
            'message' => $data['message'],
            'from' => $this->config['sender'],
            'format' => 'json'
        ]);

        return $response->json();
    }
}
