<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class AfricellGambiaGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://api.africell.gm/sms/v1/send';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.africell_gambia');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config['api_token']
            ])->post($this->endpoint, [
                'to' => $number,
                'message' => $data['message'],
                'from' => $this->config['sender_id']
            ]);

            $results[] = $response->json();
        }

        return $results;
    }
}
