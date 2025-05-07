<?php

return [
    'gateways' => [
        'aamarPay' => [
            'name' => 'aamarPay',
            'class' => 'LaravelMultiNotify\Gateways\SMS\BDGateways\AamarPayGateway',
            'config' => [
                'api_key' => env('AAMARPAY_API_KEY'),
                'sender_id' => env('AAMARPAY_SENDER_ID'),
            ],
        ],
        // Add other Bangladesh-specific gateways as needed
    ],
];
