<?php

namespace LaravelMultiNotify\Tests\Unit;

use LaravelMultiNotify\Tests\TestCase;
use LaravelMultiNotify\Gateways\Email\EmailGateway;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class EmailGatewayTest extends TestCase
{
    private EmailGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->gateway = new EmailGateway();
    }

    /** @test */
    public function it_can_send_single_email()
    {
        $to = 'test@example.com';
        $data = [
            'subject' => 'Test Subject',
            'body' => 'Test message',
        ];

        Mail::assertNothingQueued();

        Mail::assertNothingSent(function (Mailable $mailable) use ($to) {
            return $mailable->hasTo($to);
        });

        $response = $this->gateway->send($to, $data);

        $this->assertTrue($response[0]['success']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'gateway' => 'smtp',
            'recipient' => $to,
            'status' => 'success'
        ]);

        Mail::assertSent(function (Mailable $mailable) use ($to) {
            return $mailable->hasTo($to);
        });
    }

    /** @test */
    public function it_can_send_bulk_emails()
    {
        $recipients = [
            'test1@example.com',
            'test2@example.com'
        ];

        $data = [
            'subject' => 'Test Subject',
            'body' => 'Test message',
        ];

        Mail::assertNothingQueued();

        $response = $this->gateway->send($recipients, $data);

        $this->assertCount(2, $response);
        foreach ($recipients as $to) {
            Mail::assertSent(function (Mailable $mailable) use ($to) {
                return $mailable->hasTo($to);
            });
            $this->assertDatabaseHas('notification_logs', [
                'channel' => 'email',
                'gateway' => 'smtp',
                'recipient' => $to,
                'status' => 'success'
            ]);
        }
    }

    /** @test */
    public function it_logs_failed_emails()
    {
        $to = 'invalid-email';
        $data = [
            'subject' => 'Test Subject',
            'body' => 'Test message',
        ];

        Mail::assertNothingQueued();

        $response = $this->gateway->send($to, $data);

        $this->assertFalse($response[0]['success']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'gateway' => 'smtp',
            'recipient' => $to,
            'status' => 'failed'
        ]);
    }

    /** @test */
    public function it_can_use_custom_template()
    {
        $to = 'test@example.com';
        $data = [
            'subject' => 'Test Subject',
            'body' => 'Test message',
            'template' => 'multi-notify::emails.custom',
            'template_data' => [
                'name' => 'John Doe',
                'message' => 'Custom message'
            ]
        ];

        $response = $this->gateway->send($to, $data);

        $this->assertTrue($response[0]['success']);
        Mail::assertSent(function (Mailable $mailable) use ($to) {
            return $mailable->hasTo($to) &&
                $mailable->view === 'multi-notify::emails.custom';
        });
    }

    /** @test */
    public function it_handles_missing_subject()
    {
        $to = 'test@example.com';
        $data = [
            'body' => 'Test message'
        ];

        $response = $this->gateway->send($to, $data);

        $this->assertFalse($response[0]['success']);
        $this->assertStringContainsString('subject', $response[0]['error']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'gateway' => 'smtp',
            'recipient' => $to,
            'status' => 'error'
        ]);
    }

    /** @test */
    public function it_handles_missing_body()
    {
        $to = 'test@example.com';
        $data = [
            'subject' => 'Test Subject'
        ];

        $response = $this->gateway->send($to, $data);

        $this->assertFalse($response[0]['success']);
        $this->assertStringContainsString('body', $response[0]['error']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'gateway' => 'smtp',
            'recipient' => $to,
            'status' => 'error'
        ]);
    }

    /** @test */
    public function it_handles_invalid_email_address()
    {
        $to = 'invalid-email';
        $data = [
            'subject' => 'Test Subject',
            'body' => 'Test message'
        ];

        $response = $this->gateway->send($to, $data);

        $this->assertFalse($response[0]['success']);
        $this->assertStringContainsString('email', $response[0]['error']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'gateway' => 'smtp',
            'recipient' => $to,
            'status' => 'error'
        ]);
    }

    /** @test */
    public function it_can_send_with_attachments()
    {
        $to = 'test@example.com';
        $data = [
            'subject' => 'Test Subject',
            'body' => 'Test message',
            'attachments' => [
                [
                    'name' => 'test.txt',
                    'content' => 'Test content',
                    'type' => 'text/plain'
                ]
            ]
        ];

        $response = $this->gateway->send($to, $data);

        $this->assertTrue($response[0]['success']);
        Mail::assertSent(function (Mailable $mailable) use ($to) {
            return $mailable->hasTo($to);
        });
    }

    /** @test */
    public function it_can_send_html_emails()
    {
        $to = 'test@example.com';
        $data = [
            'subject' => 'Test Subject',
            'body' => '<h1>Test message</h1>',
            'isHtml' => true
        ];

        $response = $this->gateway->send($to, $data);

        $this->assertTrue($response[0]['success']);
        Mail::assertSent(function (Mailable $mailable) use ($to) {
            return $mailable->hasTo($to);
        });
    }

    /** @test */
    public function it_handles_mail_sending_errors()
    {
        Mail::shouldReceive('to')
            ->andThrow(new \Exception('Mail sending failed'));

        $to = 'test@example.com';
        $data = [
            'subject' => 'Test Subject',
            'body' => 'Test message'
        ];

        $response = $this->gateway->send($to, $data);

        $this->assertFalse($response[0]['success']);
        $this->assertStringContainsString('failed', $response[0]['error']);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'gateway' => 'smtp',
            'recipient' => $to,
            'status' => 'error'
        ]);
    }
}
