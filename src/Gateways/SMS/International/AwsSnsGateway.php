<?php

namespace LaravelMultiNotify\Gateways\SMS\International;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Aws\Sns\SnsClient;

class AwsSnsGateway implements NotificationGateway
{
    protected $client;

    public function __construct()
    {
        $this->client = new SnsClient([
            'version' => 'latest',
            'region'  => config('multi-notify.sms.gateways.aws_sns.region'),
            'credentials' => [
                'key'    => config('multi-notify.sms.gateways.aws_sns.key'),
                'secret' => config('multi-notify.sms.gateways.aws_sns.secret'),
            ],
        ]);
    }

    public function send($to, array $data)
    {
        $numbers = is_array($to) ? $to : [$to];
        $results = [];

        foreach ($numbers as $number) {
            $results[] = $this->client->publish([
                'Message' => $data['message'],
                'PhoneNumber' => $number,
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SenderId' => [
                        'DataType' => 'String',
                        'StringValue' => config('multi-notify.sms.gateways.aws_sns.sender_id')
                    ],
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => 'Transactional'
                    ]
                ]
            ]);
        }

        return $results;
    }
}
