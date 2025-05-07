<?php

namespace LaravelMultiNotify\Gateways\SMS;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Twilio\Rest\Client;

class TwilioGateway implements NotificationGateway
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            config('multi-notify.sms.gateways.twilio.sid'),
            config('multi-notify.sms.gateways.twilio.token')
        );
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $results[] = $this->client->messages->create(
                $number,
                [
                    'from' => config('multi-notify.sms.gateways.twilio.from'),
                    'body' => $data['message']
                ]
            );
        }

        return $results;
    }
}
