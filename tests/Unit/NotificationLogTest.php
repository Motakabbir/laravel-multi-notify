<?php

namespace LaravelMultiNotify\Tests\Unit;

use LaravelMultiNotify\Tests\TestCase;
use LaravelMultiNotify\Models\NotificationLog;

class NotificationLogTest extends TestCase
{
    /** @test */
    public function it_can_create_log_entry()
    {
        $data = [
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '1234567890',
            'content' => ['message' => 'Test message'],
            'response' => ['status' => 'delivered'],
            'status' => 'success',
            'error_message' => null
        ];

        $log = NotificationLog::create($data);

        $this->assertInstanceOf(NotificationLog::class, $log);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '1234567890',
            'content' => ['message' => 'Test message'],
            'response' => ['status' => 'delivered'],
            'status' => 'success',
            'error_message' => null
        ]);
    }

    /** @test */
    public function it_casts_content_and_response_as_array()
    {
        $log = NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '1234567890',
            'content' => ['message' => 'Test message'],
            'response' => ['status' => 'delivered'],
            'status' => 'success'
        ]);

        $this->assertIsArray($log->content);
        $this->assertIsArray($log->response);
        $this->assertEquals('Test message', $log->content['message']);
        $this->assertEquals('delivered', $log->response['status']);
    }

    /** @test */
    public function it_can_filter_by_status()
    {
        NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '1234567890',
            'content' => ['message' => 'Test 1'],
            'response' => null,
            'status' => 'success'
        ]);

        NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '0987654321',
            'content' => ['message' => 'Test 2'],
            'response' => null,
            'status' => 'failed'
        ]);

        $this->assertEquals(1, NotificationLog::whereStatus('failed')->count());
        $this->assertEquals(1, NotificationLog::whereStatus('success')->count());
    }

    /** @test */
    public function it_can_filter_by_channel_and_gateway()
    {
        NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '1234567890',
            'content' => ['message' => 'Test SMS'],
            'status' => 'success'
        ]);

        NotificationLog::create([
            'channel' => 'push',
            'gateway' => 'firebase',
            'recipient' => 'device-token',
            'content' => ['message' => 'Test Push'],
            'status' => 'success'
        ]);

        $this->assertEquals(1, NotificationLog::whereChannel('sms')->count());
        $this->assertEquals(1, NotificationLog::whereGateway('firebase')->count());
    }

    /** @test */
    public function it_can_filter_by_channel()
    {
        NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '1234567890',
            'content' => ['message' => 'SMS 1'],
            'response' => null,
            'status' => 'success'
        ]);

        NotificationLog::create([
            'channel' => 'email',
            'gateway' => 'smtp',
            'recipient' => 'test@example.com',
            'content' => ['subject' => 'Email 1'],
            'response' => null,
            'status' => 'success'
        ]);

        $smsLogs = NotificationLog::whereChannel('sms')->get();
        $emailLogs = NotificationLog::whereChannel('email')->get();

        $this->assertEquals(1, $smsLogs->count());
        $this->assertEquals(1, $emailLogs->count());
        $this->assertEquals('sms', $smsLogs->first()->channel);
        $this->assertEquals('email', $emailLogs->first()->channel);
    }

    /** @test */
    public function it_can_filter_by_gateway()
    {
        NotificationLog::create([
            'channel' => 'push',
            'gateway' => 'firebase',
            'recipient' => 'device-token-1',
            'content' => ['title' => 'Test 1'],
            'response' => null,
            'status' => 'success'
        ]);

        NotificationLog::create([
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => 'channel-1',
            'content' => ['event' => 'Test 1'],
            'response' => null,
            'status' => 'success'
        ]);

        $firebaseLogs = NotificationLog::whereGateway('firebase')->get();
        $pusherLogs = NotificationLog::whereGateway('pusher')->get();

        $this->assertEquals(1, $firebaseLogs->count());
        $this->assertEquals(1, $pusherLogs->count());
        $this->assertEquals('firebase', $firebaseLogs->first()->gateway);
        $this->assertEquals('pusher', $pusherLogs->first()->gateway);
    }
    /** @test */
    public function it_can_filter_by_date_range()
    {
        $yesterday = now()->subDay()->startOfDay();
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '1234567890',
            'content' => ['message' => 'Past'],
            'created_at' => $yesterday->copy()->addHours(12),
            'status' => 'success'
        ]);

        NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '1234567890',
            'content' => ['message' => 'Present'],
            'created_at' => $today->copy()->addHours(12),
            'status' => 'success'
        ]);

        NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '1234567890',
            'content' => ['message' => 'Future'],
            'created_at' => $tomorrow->copy()->addHours(12),
            'status' => 'success'
        ]);

        $logs = NotificationLog::whereBetween('created_at', [$yesterday->startOfDay(), $today->endOfDay()])->get();

        $this->assertEquals(2, $logs->count());
    }

    /** @test */
    public function it_can_filter_by_recipient()
    {
        $recipient1 = '1234567890';
        $recipient2 = '0987654321';

        NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => $recipient1,
            'content' => ['message' => 'Test 1'],
            'status' => 'success'
        ]);

        NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => $recipient2,
            'content' => ['message' => 'Test 2'],
            'status' => 'success'
        ]);

        $logs1 = NotificationLog::whereRecipient($recipient1)->get();
        $logs2 = NotificationLog::whereRecipient($recipient2)->get();

        $this->assertEquals(1, $logs1->count());
        $this->assertEquals(1, $logs2->count());
        $this->assertEquals($recipient1, $logs1->first()->recipient);
        $this->assertEquals($recipient2, $logs2->first()->recipient);
    }

    /** @test */
    public function it_soft_deletes_logs()
    {
        $log = NotificationLog::create([
            'channel' => 'sms',
            'gateway' => 'twilio',
            'recipient' => '1234567890',
            'content' => ['message' => 'Test'],
            'status' => 'success'
        ]);

        $this->assertEquals(1, NotificationLog::count());

        $log->delete();

        $this->assertEquals(0, NotificationLog::count());
        $this->assertEquals(1, NotificationLog::withTrashed()->count());
    }
}
