<?php

namespace LaravelMultiNotify\Gateways\SMS\BD;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Http;

abstract class BaseBDGateway implements NotificationGateway
{
    protected $config;
    protected $endpoint;

    public function __construct()
    {
        $this->config = config('multi-notify.sms.gateways.' . $this->getGatewayName());
    }

    abstract protected function getGatewayName(): string;

    abstract protected function buildPayload($to, array $data): array;

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $payload = $this->buildPayload($number, $data);
            $response = Http::post($this->getEndpoint(), $payload);
            $results[] = $response->json() ?? $response->body();
        }

        return $results;
    }
}
