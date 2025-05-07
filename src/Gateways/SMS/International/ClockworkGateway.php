<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Clockwork\Clockwork;

class ClockworkGateway implements NotificationGateway
{
    protected $client;

    public function __construct()
    {
        $config = config('multi-notify.sms.gateways.clockwork');
        $this->client = new Clockwork($config['api_key']);
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $message = [
                'to' => $number,
                'message' => $data['message'],
            ];

            if (isset($this->config['from'])) {
                $message['from'] = $this->config['from'];
            }

            $results[] = $this->client->send($message);
        }

        return $results;
    }
}
