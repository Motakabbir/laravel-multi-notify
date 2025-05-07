<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class LSimGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://api.lsim.ir/v1/sms/send';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.lsim');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config['api_key']
        ])->post($this->endpoint, [
            'recipients' => $numbers,
            'message' => $data['message']
        ]);

        return $response->json();
    }
}
