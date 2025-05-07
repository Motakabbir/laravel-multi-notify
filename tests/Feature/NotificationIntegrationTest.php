<?php

namespace LaravelMultiNotify\Tests\Feature;

use LaravelMultiNotify\Tests\TestCase;
use LaravelMultiNotify\Facades\MultiNotify;
use LaravelMultiNotify\Jobs\SendSmsJob;
use LaravelMultiNotify\Jobs\SendPushJob;
use LaravelMultiNotify\Jobs\SendEmailJob;
use LaravelMultiNotify\Models\NotificationLog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;

class NotificationIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    /** @test */
    public function it_properly_queues_sms_notifications()
    {
        $recipient = '1234567890';
        $message = 'Test message';
        $gateway = 'twilio';

        Config::set('multi-notify.sms.default', $gateway);

        MultiNotify::sms($recipient, $message);

        Queue::assertPushed(SendSmsJob::class, function ($job) use ($recipient, $message, $gateway) {
            return $job->to === $recipient &&
                $job->data['message'] === $message &&
                $job->gateway === $gateway;
        });
    }

    /** @test */
    public function it_properly_queues_bulk_sms_notifications()
    {
        $recipients = ['1234567890', '0987654321'];
        $message = 'Test message';

        MultiNotify::sms($recipients, $message);

        Queue::assertPushed(SendSmsJob::class, function ($job) use ($recipients, $message) {
            return $job->to === $recipients &&
                $job->data['message'] === $message;
        });

        foreach ($recipients as $recipient) {
            $this->assertDatabaseHas('notification_logs', [
                'channel' => 'sms',
                'recipient' => $recipient,
                'content' => json_encode(['message' => $message])
            ]);
        }
    }

    /** @test */
    public function it_properly_queues_push_notifications()
    {
        $deviceToken = 'device-token-123';
        $data = [
            'title' => 'Test Title',
            'body' => 'Test message'
        ];
        $service = 'firebase';

        Config::set('multi-notify.push.default', $service);

        MultiNotify::push($deviceToken, $data);

        Queue::assertPushed(SendPushJob::class, function ($job) use ($deviceToken, $data, $service) {
            return $job->to === $deviceToken &&
                $job->data === $data &&
                $job->gateway === $service;
        });
    }

    /** @test */
    public function it_properly_queues_email_notifications()
    {
        $recipient = 'test@example.com';
        $data = [
            'subject' => 'Test Subject',
            'content' => 'Test content'
        ];
        $gateway = 'smtp';

        Config::set('multi-notify.email.default', $gateway);

        MultiNotify::email($recipient, $data);

        Queue::assertPushed(SendEmailJob::class, function ($job) use ($recipient, $data, $gateway) {
            return $job->to === $recipient &&
                $job->data === $data &&
                $job->gateway === $gateway;
        });

        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'gateway' => $gateway,
            'recipient' => $recipient,
            'content' => json_encode($data),
            'status' => 'queued'
        ]);
    }

    /** @test */
    public function it_properly_queues_bulk_email_notifications()
    {
        $recipients = [
            'user1@example.com',
            'user2@example.com',
            'user3@example.com'
        ];

        $data = [
            'subject' => 'Bulk Test',
            'content' => 'Test content'
        ];

        $gateway = 'smtp';
        Config::set('multi-notify.email.default', $gateway);

        MultiNotify::email($recipients, $data);

        Queue::assertPushed(SendEmailJob::class, function ($job) use ($recipients, $data, $gateway) {
            return $job->to === $recipients &&
                $job->data === $data &&
                $job->gateway === $gateway;
        });

        foreach ($recipients as $recipient) {
            $this->assertDatabaseHas('notification_logs', [
                'channel' => 'email',
                'gateway' => $gateway,
                'recipient' => $recipient,
                'content' => json_encode($data),
                'status' => 'queued'
            ]);
        }
    }

    /** @test */
    public function it_logs_notifications_with_correct_status()
    {
        $recipient = 'test@example.com';
        $data = [
            'subject' => 'Test',
            'content' => 'Test message'
        ];

        // Test immediate sending
        MultiNotify::email($recipient, $data, false);

        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'recipient' => $recipient,
            'content' => json_encode($data),
            'status' => 'success'
        ]);

        // Test queued sending
        MultiNotify::email('another@example.com', $data);

        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'recipient' => 'another@example.com',
            'content' => json_encode($data),
            'status' => 'queued'
        ]);
    }

    /** @test */
    public function it_respects_configured_defaults()
    {
        // Test SMS default gateway
        Config::set('multi-notify.sms.default', 'twilio');
        MultiNotify::sms('1234567890', 'Test');
        Queue::assertPushed(SendSmsJob::class, function ($job) {
            return $job->gateway === 'twilio';
        });

        // Test Push default service
        Config::set('multi-notify.push.default', 'firebase');
        MultiNotify::push('device-token', ['title' => 'Test']);
        Queue::assertPushed(SendPushJob::class, function ($job) {
            return $job->gateway === 'firebase';
        });
    }

    /** @test */
    public function it_handles_real_integrations()
    {
        $this->markTestSkipped('This test requires actual API credentials');

        // Remove Queue fake for this test
        Queue::swap(app('queue.connection'));

        // SMS Test
        $smsResponse = MultiNotify::sms('1234567890', 'Test SMS', 'twilio', false);
        $this->assertArrayHasKey('success', $smsResponse);

        // Email Test
        $emailResponse = MultiNotify::email('test@example.com', [
            'subject' => 'Test',
            'content' => 'Test content'
        ], false);
        $this->assertArrayHasKey('success', $emailResponse);

        // Push Test
        $pushResponse = MultiNotify::push('test-channel', [
            'event' => 'test',
            'data' => 'Test message'
        ], 'pusher', false);
        $this->assertArrayHasKey('success', $pushResponse);
    }
}
