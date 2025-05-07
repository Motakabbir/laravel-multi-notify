<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Sms77\Api\Client;

class Sms77Gateway implements NotificationGateway
{
    protected $client;

    public function __construct()
    {
        $config = config('multi-notify.sms.gateways.sms77');
        $this->client = new Client($config['api_key']);
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $params = [
                'to' => $number,
                'text' => $data['message'],
            ];

            if (isset($this->config['from'])) {
                $params['from'] = $this->config['from'];
            }

            $results[] = $this->client->sms($params);
        }

        return $results;
    }
}
