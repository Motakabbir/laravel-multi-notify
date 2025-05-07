<?php

namespace LaravelMultiNotify\Gateways\SMS\Iran;

class MelipayamakPatternGateway extends MelipayamakGateway
{
    protected $endpoint = 'https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber';

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $response = Http::post($this->endpoint, [
                'username' => $this->config['username'],
                'password' => $this->config['password'],
                'to' => $number,
                'text' => $data['message'],
                'bodyId' => $data['pattern_id'] ?? null
            ]);

            $results[] = $response->json();
        }

        return $results;
    }
}
