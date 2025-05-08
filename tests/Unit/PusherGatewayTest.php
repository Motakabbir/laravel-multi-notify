<?php

namespace LaravelMultiNotify\Tests\Unit;

use LaravelMultiNotify\Tests\TestCase;
use LaravelMultiNotify\Gateways\Push\PusherGateway;
use Mockery;
use stdClass;
use Pusher\Pusher;

class PusherGatewayTest extends TestCase
{
    private PusherGateway $gateway;
    private $pusherMock;

    protected function setUp(): void
    {
        parent::setUp();
        config(['multi-notify.push.services.pusher' => [
            'app_key' => 'test-key',
            'app_secret' => 'test-secret',
            'app_id' => 'test-id',
            'cluster' => 'test-cluster'
        ]]);

        $this->gateway = new PusherGateway();

        $this->pusherMock = Mockery::mock(Pusher::class);
        $this->gateway->setPusher($this->pusherMock);
    }

    /** @test */
    public function it_can_send_to_single_channel()
    {
        $channel = 'test-channel';
        $data = [
            'event' => 'test-event',
            'message' => 'Test message'
        ];

        $response = new stdClass();
        $response->status = 200;

        $this->pusherMock->shouldReceive('trigger')
            ->once()
            ->andReturn($response);

        $result = $this->gateway->send($channel, $data);

        $this->assertTrue($result[0]['success']);
        $this->assertLogExists([
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => $channel,
            'content' => $data,
            'status' => 'success'
        ]);
    }

    /** @test */
    public function it_can_send_to_multiple_channels()
    {
        $channels = ['channel-1', 'channel-2'];
        $data = [
            'event' => 'test-event',
            'message' => 'Test message'
        ];

        $response = new stdClass();
        $response->status = 200;

        $this->pusherMock->shouldReceive('trigger')
            ->twice()
            ->andReturn($response);

        $result = $this->gateway->send($channels, $data);

        $this->assertCount(2, $result);
        foreach ($channels as $channel) {
            $this->assertLogExists([
                'channel' => 'push',
                'gateway' => 'pusher',
                'recipient' => $channel,
                'content' => $data,
                'status' => 'success'
            ]);
        }
    }

    /** @test */
    public function it_logs_failed_pushes()
    {
        $channel = 'test-channel';
        $data = [
            'event' => 'test-event',
            'message' => 'Test message'
        ];

        $this->pusherMock->shouldReceive('trigger')
            ->once()
            ->andThrow(new \Exception('Test error'));

        $result = $this->gateway->send($channel, $data);

        $this->assertFalse($result[0]['success']);
        $this->assertEquals('Test error', $result[0]['error']);
        $this->assertLogExists([
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => $channel,
            'content' => $data,
            'status' => 'failed',
            'error_message' => 'Test error'
        ]);
    }

    /** @test */
    public function it_validates_required_event_name()
    {
        $channel = 'test-channel';
        $data = [
            'message' => 'Test message'
        ];

        $result = $this->gateway->send($channel, $data);

        $this->assertFalse($result[0]['success']);
        $this->assertStringContainsString('Event name', $result[0]['error']);
        $this->assertLogExists([
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => $channel,
            'content' => $data,
            'status' => 'failed'
        ]);
    }

    /** @test */
    public function it_can_send_to_private_channels()
    {
        $channel = 'private-channel';
        $data = [
            'event' => 'test-event',
            'message' => 'Test message'
        ];

        $response = new stdClass();
        $response->status = 200;

        $this->pusherMock->shouldReceive('trigger')
            ->once()
            ->andReturn($response);

        $result = $this->gateway->send($channel, $data);

        $this->assertTrue($result[0]['success']);
        $this->assertLogExists([
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => $channel,
            'content' => $data,
            'status' => 'success'
        ]);
    }

    /** @test */
    public function it_can_send_to_presence_channels()
    {
        $channel = 'presence-channel';
        $data = [
            'event' => 'test-event',
            'message' => 'Test message'
        ];

        $response = new stdClass();
        $response->status = 200;

        $this->pusherMock->shouldReceive('trigger')
            ->once()
            ->andReturn($response);

        $result = $this->gateway->send($channel, $data);

        $this->assertTrue($result[0]['success']);
        $this->assertLogExists([
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => $channel,
            'content' => $data,
            'status' => 'success'
        ]);
    }
}
