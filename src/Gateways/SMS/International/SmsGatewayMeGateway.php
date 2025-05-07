<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class SmsGatewayMeGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://smsgateway.me/api/v4/messages/send';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.sms_gateway_me');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $messages = [];

        foreach ($numbers as $number) {
            $messages[] = [
                'phone_number' => $number,
                'message' => $data['message'],
                'device_id' => $this->config['device_id']
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => $this->config['api_key']
        ])->post($this->endpoint, $messages);

        return $response->json();
    }
}
