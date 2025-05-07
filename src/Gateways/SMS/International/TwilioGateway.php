<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class TwilioGateway implements NotificationGateway
{
    protected $client;
    protected $config;

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.twilio');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::withBasicAuth(
                $this->config['sid'],
                $this->config['token']
            )->asForm()->post(
                "https://api.twilio.com/2010-04-01/Accounts/{$this->config['sid']}/Messages.json",
                [
                    'To' => $number,
                    'From' => $this->config['from'],
                    'Body' => $data['message']
                ]
            );

            $results[] = $response->json();
        }

        return $results;
    }
}
