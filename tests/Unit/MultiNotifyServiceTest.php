<?php

namespace LaravelMultiNotify\Tests\Unit;

use LaravelMultiNotify\Tests\TestCase;
use LaravelMultiNotify\Models\NotificationLog;
use Illuminate\Support\Facades\Queue;
use LaravelMultiNotify\Services\MultiNotifyService;
use LaravelMultiNotify\Exceptions\ChannelNotFoundException;
use LaravelMultiNotify\Jobs\SendSmsJob;
use LaravelMultiNotify\Jobs\SendPushJob;
use LaravelMultiNotify\Jobs\SendEmailJob;

class MultiNotifyServiceTest extends TestCase
{
    protected $service;
    protected function setUp(): void
    {
        parent::setUp();
        // Configure basic gateways
        config([
            'multi-notify' => [
                'default' => 'sms',
                'sms' => [
                    'default' => 'twilio',
                    'gateways' => [
                        'twilio' => [
                            'sid' => 'test-sid',
                            'token' => 'test-token',
                            'from' => 'test-from',
                            'class' => \LaravelMultiNotify\Gateways\SMS\International\TwilioGateway::class
                        ]
                    ]
                ],
                'push' => [
                    'default' => 'firebase',
                    'services' => [
                        'firebase' => [
                            'credentials' => __DIR__ . '/dummy-firebase-credentials.json',
                            'class' => \LaravelMultiNotify\Gateways\Push\FirebaseGateway::class
                        ]
                    ]
                ],
                'email' => [
                    'default' => 'smtp',
                    'mailers' => [
                        'smtp' => [
                            'transport' => 'smtp',
                            'host' => 'smtp.example.com',
                            'port' => 587,
                            'encryption' => 'tls',
                            'username' => 'test',
                            'password' => 'test'
                        ]
                    ],
                    'class' => \LaravelMultiNotify\Gateways\Email\EmailGateway::class
                ]
            ]
        ]);

        $this->service = app(MultiNotifyService::class);
    }

    /** @test */
    public function it_uses_default_driver_from_config()
    {
        config(['multi-notify.default' => 'test-driver']);
        $this->assertEquals('test-driver', $this->service->getDefaultDriver());
    }

    /** @test */    public function it_can_send_single_sms()
    {
        $to = '1234567890';
        $data = ['message' => 'Test message'];

        $response = $this->service->sms($to, $data, 'twilio', false);

        $this->assertNotEmpty($response);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => $to,
            'content' => json_encode($data),
            'status' => 'success'
        ]);
    }
    /** @test */
    public function it_can_send_bulk_sms()
    {
        $to = ['1234567890', '0987654321'];
        $data = ['message' => 'Test message'];
        $response = $this->service->sms($to, $data, 'twilio', false);

        $this->assertNotEmpty($response);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => json_encode($to),
            'content' => json_encode($data)
        ]);
    }
    /** @test */
    public function it_queues_sms_by_default()
    {
        Queue::fake();

        $to = '1234567890';
        $data = ['message' => 'Test message'];

        $this->service->sms($to, $data);

        Queue::assertPushed(SendSmsJob::class);
    }
    /** @test */
    public function it_queues_push_notification_by_default()
    {
        Queue::fake();

        $token = 'device_token';
        $data = ['title' => 'Test', 'body' => 'Test message'];

        $this->service->push($token, $data);

        Queue::assertPushed(SendPushJob::class, function ($job) use ($token, $data) {
            return $job->to === $token &&
                $job->data === $data &&
                $job->gateway === config('multi-notify.push.default');
        });
    }

    /** @test */
    public function it_can_send_push_notification_immediately()
    {
        $to = 'test-channel';
        $data = [
            'event' => 'test-event',
            'data' => ['message' => 'test']
        ];

        $response = $this->service->push($to, $data, 'pusher', false);

        $this->assertNotEmpty($response);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => $to,
            'content' => json_encode($data)
        ]);
    }
    /** @test */
    public function it_uses_default_sms_gateway_when_not_specified()
    {
        $defaultGateway = 'twilio';
        config(['multi-notify.sms.default' => $defaultGateway]);

        $to = '1234567890';
        $data = ['message' => 'Test message'];

        $response = $this->service->sms($to, $data, null, false);

        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'sms',
            'gateway' => $defaultGateway
        ]);
    }

    /** @test */
    public function it_uses_default_push_service_when_not_specified()
    {
        $defaultService = 'firebase';
        config(['multi-notify.push.default' => $defaultService]);

        $to = 'device_token';
        $data = ['title' => 'Test'];

        $response = $this->service->push($to, $data, null, false);
        $this->assertLogExists([
            'channel' => 'push',
            'gateway' => $defaultService,
            'recipient' => $to,
            'content' => $data
        ]);
    }
    /** @test */
    public function it_throws_exception_for_invalid_sms_gateway()
    {
        $this->expectException(ChannelNotFoundException::class);
        $this->expectExceptionMessage("Gateway [invalid_gateway] not found for channel [sms]");

        $this->service->sms('1234567890', ['message' => 'Test message'], 'invalid_gateway', false);
    }

    /** @test */
    public function it_throws_exception_for_invalid_push_service()
    {
        $this->expectException(ChannelNotFoundException::class);
        $this->expectExceptionMessage("Gateway [invalid_service] not found for channel [push]");

        $this->service->push('device_token', ['title' => 'Test'], 'invalid_service', false);
    }

    /** @test */
    public function it_throws_exception_for_invalid_channel()
    {
        $this->expectException(ChannelNotFoundException::class);
        $this->service->notify('invalid-channel', 'recipient', []);
    }

    /** @test */
    public function it_can_send_email()
    {
        Queue::fake();

        $to = 'test@example.com';
        $data = [
            'subject' => 'Test Email',
            'body' => 'Test content'
        ];

        $this->service->email($to, $data);

        Queue::assertPushed(SendEmailJob::class, function ($job) use ($to, $data) {
            return $job->to === $to &&
                $job->data['subject'] === $data['subject'] &&
                $job->data['body'] === $data['body'];
        });
    }
    /** @test */
    public function it_can_change_default_gateway()
    {
        $this->markTestSkipped('Default gateway functionality is not yet implemented');
    }
    /** @test */
    public function it_can_send_with_custom_gateway()
    {
        Queue::fake();

        $to = '1234567890';
        $data = ['message' => 'Test message'];
        $customGateway = 'custom-sms-gateway';
        $this->service->sms($to, $data, $customGateway);

        Queue::assertPushed(SendSmsJob::class, function ($job) use ($to, $data, $customGateway) {
            return $job->to === $to &&
                $job->data === $data &&
                $job->gateway === $customGateway;
        });
    }
    /** @test */
    public function it_can_send_without_queueing()
    {
        Queue::fake();

        $to = '1234567890';
        $data = ['message' => 'Test message'];

        $this->service->sms($to, $data, 'twilio', false);

        Queue::assertNotPushed(SendSmsJob::class);
    }

    /** @test */
    public function it_properly_formats_push_notification_data()
    {
        Queue::fake();

        $token = 'device-token';
        $data = [
            'title' => 'Test',
            'body' => 'Test message',
            'data' => ['custom' => 'value']
        ];

        $this->service->push($token, $data);

        Queue::assertPushed(SendPushJob::class, function ($job) use ($token, $data) {
            return $job->to === $token &&
                $job->data === $data &&
                $job->gateway === config('multi-notify.push.default');
        });
    }

    /** @test */
    public function it_validates_push_notification_data()
    {
        Queue::fake();

        $token = 'device-token';
        $invalidData = ['invalid' => 'data'];

        $response = $this->service->push($token, $invalidData);

        $this->assertFalse($response['success']);
        $this->assertStringContainsString('title', $response['error']);
        Queue::assertNotPushed(SendPushJob::class);
    }
}
