<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class SmsirGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://api.sms.ir/v1/send/bulk';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.smsir');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];

        $response = Http::withHeaders([
            'X-API-KEY' => $this->config['api_key']
        ])->post($this->endpoint, [
            'lineNumber' => $this->config['line_number'],
            'messageText' => $data['message'],
            'mobiles' => $numbers
        ]);

        return $response->json();
    }
}
