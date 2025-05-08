<?php

namespace LaravelMultiNotify\Tests\Unit;

use LaravelMultiNotify\Tests\TestCase;
use LaravelMultiNotify\Gateways\Push\FirebaseGateway;
use InvalidArgumentException;
use Kreait\Firebase\Messaging\CloudMessage;
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

        $this->tempCredentialsPath = __DIR__ . '/dummy-firebase-credentials.json';
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

        // Configure the application to use this file
        $this->app['config']->set('multi-notify.push.services.firebase.credentials', $this->tempCredentialsPath);        // Create mock and gateway
        $this->messagingMock = Mockery::mock(Messaging::class);
        $this->gateway = new FirebaseGateway($this->messagingMock);
    }
    protected function tearDown(): void
    {
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
            ->with(Mockery::on(function ($message) use ($token) {
                return $message instanceof CloudMessage;
            }))
            ->once()
            ->andReturn(['messageId' => 'test-id']);

        $response = $this->gateway->send($token, $data);

        $this->assertTrue($response[0]['success']);
        $this->assertLogExists([
            'channel' => 'push',
            'gateway' => 'firebase',
            'recipient' => $token,
            'content' => [
                'notification' => [
                    'title' => 'Test Title',
                    'body' => 'Test message'
                ],
                'data' => ['key' => 'value']
            ],
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
        ];        // CloudMessage is a final class, we just need to check its type
        $this->messagingMock->shouldReceive('send')
            ->with(Mockery::on(function ($message) {
                return $message instanceof CloudMessage;
            }))
            ->times(2)
            ->andReturn(['messageId' => 'test-id']);

        $response = $this->gateway->send($tokens, $data);

        $this->assertCount(2, $response);
        foreach ($tokens as $token) {
            $this->assertDatabaseHas('notification_logs', [
                'channel' => 'push',
                'gateway' => 'firebase',
                'recipient' => $token,
                'status' => 'success'
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

        $error = 'Invalid FCM token: test-token';
        $this->messagingMock->shouldReceive('send')
            ->with(Mockery::on(function ($message) use ($token) {
                return $message instanceof CloudMessage;
            }))
            ->once()
            ->andThrow(new \Exception($error));

        $response = $this->gateway->send($token, $data);

        $this->assertFalse($response[0]['success']);
        $this->assertEquals($error, $response[0]['error']);
        $this->assertLogExists([
            'channel' => 'push',
            'gateway' => 'firebase',
            'recipient' => $token,
            'content' => [
                'notification' => [
                    'title' => 'Test Title',
                    'body' => 'Test message'
                ],
                'data' => []
            ],
            'status' => 'failed',
            'error_message' => $error
        ]);
    }

    /** @test */
    public function it_validates_required_notification_fields()
    {
        $token = 'test-token';
        $data = [];

        $response = $this->gateway->send($token, $data);

        $this->assertFalse($response[0]['success']);
        $this->assertStringContainsString('empty', $response[0]['error']);
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
            ->with(Mockery::on(function ($message) {
                return $message instanceof CloudMessage;
            }))
            ->times(2)
            ->andReturn(['messageId' => 'test-id']);

        $response = $this->gateway->send($tokens, $data);

        $this->assertCount(2, $response);
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
            ->with(Mockery::on(function ($message) {
                return $message instanceof CloudMessage;
            }))
            ->once()
            ->andReturn(['messageId' => 'test-id']);

        $response = $this->gateway->send($token, $data);

        $this->assertTrue($response[0]['success']);
        $this->assertLogExists([
            'channel' => 'push',
            'gateway' => 'firebase',
            'recipient' => $token,
            'content' => [
                'notification' => [
                    'title' => 'Test Title',
                    'body' => 'Test message',
                    'imageUrl' => 'https://example.com/image.jpg',
                    'sound' => 'default',
                    'clickAction' => 'OPEN_ACTIVITY'
                ],
                'data' => ['custom' => 'data']
            ],
            'status' => 'success'
        ]);
    }
}
