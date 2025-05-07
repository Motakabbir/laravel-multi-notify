<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class LinkMobilityGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://europe.link-mobility.com/sms/v1/send';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.link_mobility');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config['api_key']
            ])->post($this->endpoint, [
                'source' => $this->config['sender'],
                'destination' => $number,
                'message' => $data['message']
            ]);

            $results[] = $response->json();
        }

        return $results;
    }
}
