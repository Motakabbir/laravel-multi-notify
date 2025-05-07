<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class D7networksGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://api.d7networks.com/messages/v1/send';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.d7networks');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config['api_token']
        ])->post($this->endpoint, [
            'messages' => [[
                'channel' => 'sms',
                'recipients' => $numbers,
                'content' => $data['message'],
                'msg_type' => 'text',
                'data_coding' => 'text'
            ]]
        ]);

        return $response->json();
    }
}
