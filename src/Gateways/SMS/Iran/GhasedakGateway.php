<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class GhasedakGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://api.ghasedak.me/v2/sms/send/simple';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.ghasedak');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];

        $response = Http::withHeaders([
            'apikey' => $this->config['api_key']
        ])->post($this->endpoint, [
            'message' => $data['message'],
            'receptor' => implode(',', $numbers),
            'linenumber' => $this->config['line_number'] ?? ''
        ]);

        return $response->json();
    }
}
