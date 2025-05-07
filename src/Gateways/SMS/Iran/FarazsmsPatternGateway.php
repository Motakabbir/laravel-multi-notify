<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

use Illuminate\Support\Facades\Http;
use LaravelMultiNotify\Contracts\NotificationGateway;

class FarazsmsPatternGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://ippanel.com/patterns/pattern';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.farazsms_pattern');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post($this->endpoint, [
                'uname' => $this->config['username'],
                'pass' => $this->config['password'],
                'pattern_code' => $this->config['pattern_code'],
                'to' => $number,
                'input_data' => $data['input_data'] ?? [], // Pattern variables
            ]);

            $results[] = $response->json();
        }

        return $results;
    }
}
