<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

class TsmsGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint = 'http://tsms.ir/url/tsmshttp.php';

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.tsms');
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];

        $response = Http::get($this->endpoint, [
            'from' => $this->config['username'],
            'to' => implode(',', $numbers),
            'msg' => $data['message'],
            'uname' => $this->config['username'],
            'pass' => $this->config['password'],
        ]);

        return $response->body();
    }
}
