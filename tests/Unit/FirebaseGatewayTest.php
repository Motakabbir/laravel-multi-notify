<?php

namespace LaravelMultiNotify\Tests\Unit;

use LaravelMultiNotify\Tests\TestCase;
use LaravelMultiNotify\Gateways\Push\FirebaseGateway;
use InvalidArgumentException;
use Kreait\Firebase\Messaging\Messaging;
use Mockery;

class FirebaseGatewayTest extends TestCase
{
    private FirebaseGateway $gateway;
    private Messaging $messagingMock;
    private string $tempCredentialsPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a temporary credentials file with valid structure
        $this->tempCredentialsPath = __DIR__ . '/dummy-credentials.json';
        $dummyCredentials = [
            'type' => 'service_account',
            'project_id' => 'dummy-project',
            'private_key_id' => 'dummy-key-id',
            'private_key' => "-----BEGIN PRIVATE KEY-----\nDUMMY_KEY\n-----END PRIVATE KEY-----\n",
            'client_email' => 'dummy@project.iam.gserviceaccount.com',
            'client_id' => '123456789',
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509/dummy@project.iam.gserviceaccount.com'
        ];
        file_put_contents($this->tempCredentialsPath, json_encode($dummyCredentials));

        // Set up config
        $this->app['config']->set('multi-notify.push.services.firebase.credentials', $this->tempCredentialsPath);

        $this->messagingMock = Mockery::mock(Messaging::class);
        $this->gateway = new FirebaseGateway();
        $this->gateway->setMessaging($this->messagingMock);
    }

    protected function tearDown(): void
    {
        // Clean up the temporary credentials file
        @unlink($this->tempCredentialsPath);

        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_send_to_single_token()
    {
        $token = 'test-token';
        $data = [
            'title' => 'Test Title',
            'body' => 'Test message',
            'data' => ['key' => 'value']
        ];

        $this->messagingMock->shouldReceive('send')
            ->once()
            ->andReturn(['message_id' => 'test-id']);

        $response = $this->gateway->send($token, $data);

        $this->assertArrayHasKey('message_id', $response[0]);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'push',
            'gateway' => 'firebase',
            'recipient' => $token,
            'status' => 'success'
        ]);
    }

    /** @test */
    public function it_can_send_to_multiple_tokens()
    {
        $tokens = ['token-1', 'token-2'];
        $data = [
            'title' => 'Test Title',
            'body' => 'Test message'
        ];

        $this->messagingMock->shouldReceive('send')
            ->twice()
            ->andReturn(['message_id' => 'test-id']);

        $response = $this->gateway->send($tokens, $data);

        $this->assertCount(2, $response);
        foreach ($tokens as $token) {
            $this->assertDatabaseHas('notification_logs', [
                'channel' => 'push',
                'gateway' => 'firebase',
                'recipient' => $token
            ]);
        }
    }

    /** @test */
    public function it_logs_failed_messages()
    {
        $token = 'test-token';
        $data = [
            'title' => 'Test Title',
            'body' => 'Test message'
        ];

        $this->messagingMock->shouldReceive('send')
            ->once()
            ->andThrow(new \Exception('Invalid token'));

        $response = $this->gateway->send($token, $data);

        $this->assertArrayHasKey('error', $response[0]);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'push',
            'gateway' => 'firebase',
            'recipient' => $token,
            'status' => 'failed'
        ]);
    }

    /** @test */
    public function it_validates_required_notification_fields()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Notification title and body are required');

        $token = 'test-token';
        $data = [
            'title' => '', // Empty title should trigger validation
            'body' => 'Test message'
        ];

        $this->gateway->send($token, $data);
    }

    /** @test */
    public function it_skips_empty_tokens()
    {
        $tokens = ['token-1', '', 'token-2'];
        $data = [
            'title' => 'Test Title',
            'body' => 'Test message'
        ];

        $this->messagingMock->shouldReceive('send')
            ->twice() // Should only be called twice, skipping the empty token
            ->andReturn(['message_id' => 'test-id']);

        $response = $this->gateway->send($tokens, $data);

        $this->assertCount(2, $response);
    }

    /** @test */
    public function it_handles_invalid_credentials_path()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->app['config']->set('multi-notify.push.services.firebase.credentials', '/invalid/path.json');
        new FirebaseGateway();
    }

    /** @test */
    public function it_handles_messaging_errors()
    {
        $token = 'test-token';
        $data = [
            'title' => 'Test Title',
            'body' => 'Test message'
        ];
        $this->messagingMock->shouldReceive('send')
            ->once()
            ->andThrow(new \Exception('Test error'));

        $response = $this->gateway->send($token, $data);

        $this->assertFalse($response[0]['success']);
        $this->assertEquals('Test error', $response[0]['error']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'push',
            'gateway' => 'firebase',
            'recipient' => $token,
            'status' => 'error',
            'error_message' => 'Test error'
        ]);
    }

    /** @test */
    public function it_handles_malformed_payload()
    {
        $token = 'test-token';
        $data = []; // Empty data

        $response = $this->gateway->send($token, $data);

        $this->assertFalse($response[0]['success']);
        $this->assertStringContainsString('title', $response[0]['error']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'push',
            'gateway' => 'firebase',
            'recipient' => $token,
            'status' => 'error'
        ]);
    }

    /** @test */
    public function it_sends_with_optional_parameters()
    {
        $token = 'test-token';
        $data = [
            'title' => 'Test Title',
            'body' => 'Test message',
            'data' => ['custom' => 'data'],
            'imageUrl' => 'https://example.com/image.jpg',
            'priority' => 'high',
            'ttl' => 3600,
            'sound' => 'default',
            'clickAction' => 'OPEN_ACTIVITY'
        ];

        $this->messagingMock->shouldReceive('send')
            ->once()
            ->andReturn(['message_id' => 'test-id']);

        $response = $this->gateway->send($token, $data);

        $this->assertTrue($response[0]['success']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'push',
            'gateway' => 'firebase',
            'recipient' => $token,
            'status' => 'success'
        ]);
    }
}
