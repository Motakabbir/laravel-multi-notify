<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class QcellGambiaGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'https://api.qcell.gm/messaging/v1/sms/send';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.qcell_gambia');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->config['username'] . ':' . $this->config['password'])
            ])->post($this->endpoint, [
                'to' => $number,
                'text' => $data['message'],
                'from' => $this->config['sender_id']
            ]);

            $results[] = $response->json();
        }

        return $results;
    }
}
