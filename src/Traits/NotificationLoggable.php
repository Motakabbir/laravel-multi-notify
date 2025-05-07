<?php

namespace LaravelMultiNotify\Traits;

use LaravelMultiNotify\Models\NotificationLog;

trait NotificationLoggable
{
    protected function logNotification(string $channel, string $gateway, $recipient, array $content, $response = null, string $status = 'success', string $error = null)
    {
        return NotificationLog::create([
            'channel' => $channel,
            'gateway' => $gateway,
            'recipient' => is_array($recipient) ? json_encode($recipient) : $recipient,
            'content' => $content,
            'response' => $response,
            'status' => $status,
            'error_message' => $error
        ]);
    }
}
