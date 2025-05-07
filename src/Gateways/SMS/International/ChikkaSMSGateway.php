<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class ChikkaSMSGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://post.chikka.com/smsapi/request';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.chikka');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::asForm()->post($this->endpoint, [
                'message_type' => 'SEND',
                'mobile_number' => $number,
                'shortcode' => $this->config['shortcode'],
                'message_id' => uniqid(),
                'message' => $data['message'],
                'client_id' => $this->config['client_id'],
                'secret_key' => $this->config['secret_key']
            ]);

            $results[] = $response->json();
        }

        return $results;
    }
}
