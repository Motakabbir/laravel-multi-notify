<?php

namespace LaravelMultiNotify\Traits;

use LaravelMultiNotify\Models\NotificationLog;

trait NotificationLoggable
{
    protected function logNotification(string $channel, string $gateway, $recipient, array $content, $response = null, string $status = 'success', string $error = null)
    {
        if ($status === 'error') {
            $status = 'failed';
        }
        return NotificationLog::create([
            'channel' => $channel,
            'gateway' => $gateway,
            'recipient' => is_array($recipient) ? json_encode($recipient) : $recipient,
            'content' => is_string($content) ? $content : json_encode($content),
            'response' => $response,
            'status' => $status,
            'error_message' => $error
        ]);
    }
}
