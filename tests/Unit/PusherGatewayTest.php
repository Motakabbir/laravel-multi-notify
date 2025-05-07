<?php

namespace LaravelMultiNotify\Tests\Unit;

use LaravelMultiNotify\Tests\TestCase;
use LaravelMultiNotify\Gateways\Push\PusherGateway;
use Mockery;
use Pusher\Pusher;
use stdClass;

class PusherGatewayTest extends TestCase
{
    private PusherGateway $gateway;
    private Pusher $pusherMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Set test configuration
        $this->app['config']->set('multi-notify.push.services.pusher.app_id', 'test-app-id');
        $this->app['config']->set('multi-notify.push.services.pusher.app_key', 'test-app-key');
        $this->app['config']->set('multi-notify.push.services.pusher.app_secret', 'test-app-secret');
        $this->app['config']->set('multi-notify.push.services.pusher.cluster', 'test-cluster');

        $this->pusherMock = Mockery::mock(Pusher::class);
        $this->gateway = new PusherGateway();
        $this->gateway->setPusher($this->pusherMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
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
            ->with($channel, $data['event'], ['message' => $data['message']], [])
            ->andReturn($response);

        $result = $this->gateway->send($channel, $data);

        $this->assertTrue($result[0]['success']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => $channel,
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
            ->with(\Mockery::any(), $data['event'], ['message' => $data['message']], [])
            ->andReturn($response);

        $result = $this->gateway->send($channels, $data);

        $this->assertCount(2, $result);
        foreach ($channels as $channel) {
            $this->assertDatabaseHas('notification_logs', [
                'channel' => 'push',
                'gateway' => 'pusher',
                'recipient' => $channel,
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
            ->andThrow(new \Exception('Connection failed'));

        $result = $this->gateway->send($channel, $data);

        $this->assertFalse($result[0]['success']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => $channel,
            'status' => 'failed'
        ]);
    }
    /** @test */
    public function it_handles_pusher_errors()
    {
        $channel = 'test-channel';
        $data = [
            'event' => 'test-event',
            'message' => 'Test message'
        ];

        $this->pusherMock->shouldReceive('trigger')
            ->once()
            ->with($channel, $data['event'], ['message' => $data['message']], [])
            ->andThrow(new \Exception('Test error'));

        $result = $this->gateway->send($channel, $data);

        $this->assertFalse($result[0]['success']);
        $this->assertEquals('Test error', $result[0]['error']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => $channel,
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
        $this->assertStringContainsString('event', $result[0]['error']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'push',
            'gateway' => 'pusher',
            'recipient' => $channel,
            'status' => 'error'
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
            ->with($channel, $data['event'], ['message' => $data['message']])
            ->andReturn($response);

        $result = $this->gateway->send($channel, $data);

        $this->assertTrue($result[0]['success']);
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
            ->with($channel, $data['event'], ['message' => $data['message']])
            ->andReturn($response);

        $result = $this->gateway->send($channel, $data);

        $this->assertTrue($result[0]['success']);
    }

    /** @test */
    public function it_validates_channel_name_format()
    {
        $channel = 'invalid:channel';
        $data = [
            'event' => 'test-event',
            'message' => 'Test message'
        ];

        $result = $this->gateway->send($channel, $data);

        $this->assertFalse($result[0]['success']);
        $this->assertStringContainsString('channel', $result[0]['error']);
    }
}
