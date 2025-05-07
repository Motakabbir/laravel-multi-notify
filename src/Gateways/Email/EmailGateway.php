<?php

namespace LaravelMultiNotify\Gateways\Email;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use LaravelMultiNotify\Traits\NotificationLoggable;

class EmailGateway implements NotificationGateway
{
    use NotificationLoggable;

    protected $config;
    protected $view = 'vendor.multi-notify.emails.default';

    public function __construct()
    {
        $this->config = config('multi-notify.email');
    }

    public function send($to, array $data): array
    {
        $recipients = is_array($to) ? $to : [$to];
        $results = [];

        // Validate required fields
        if (!isset($data['subject'])) {
            return [[
                'success' => false,
                'error' => 'Email subject is required'
            ]];
        }

        if (!isset($data['body'])) {
            return [[
                'success' => false,
                'error' => 'Email body is required'
            ]];
        }

        foreach ($recipients as $recipient) {
            try {
                // Validate email format
                if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    throw new \InvalidArgumentException('Invalid email address format: ' . $recipient);
                }

                // Handle custom template
                if (isset($data['template'])) {
                    if (!View::exists($data['template'])) {
                        throw new \InvalidArgumentException('Email template not found: ' . $data['template']);
                    }
                    $this->view = $data['template'];
                }

                $mailData = [
                    'subject' => $data['subject'],
                    'body' => $data['body'],
                    'template' => $this->view,
                    'template_data' => $data['template_data'] ?? [],
                    'isHtml' => $data['isHtml'] ?? false,
                    'attachments' => $data['attachments'] ?? []
                ];

                Mail::to($recipient)->send(new NotificationEmail($mailData));

                $result = [
                    'success' => true,
                    'email' => $recipient
                ];

                $this->logNotification(
                    'email',
                    'smtp',
                    $recipient,
                    $data,
                    $result,
                    'success'
                );

                $results[] = $result;
            } catch (\Exception $e) {
                $result = [
                    'success' => false,
                    'email' => $recipient,
                    'error' => $e->getMessage()
                ];

                $this->logNotification(
                    'email',
                    'smtp',
                    $recipient,
                    $data,
                    null,
                    'error', // Changed from 'failed' to 'error' for consistency
                    $e->getMessage()
                );

                $results[] = $result;
            }
        }

        return $results;
    }

    protected function getFromAddress(array $data)
    {
        if (isset($data['from'])) {
            return is_array($data['from']) ? $data['from'] : ['address' => $data['from'], 'name' => null];
        }

        return $this->config['from'] ?? null;
    }
}
