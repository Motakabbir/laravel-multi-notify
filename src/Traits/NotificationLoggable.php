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

        // Handle content array to prevent double-encoding
        if (is_array($content)) {
            $content = json_encode($content);
        }

        // Handle response array to prevent double-encoding
        if (is_array($response)) {
            $response = json_encode($response);
        }

        return NotificationLog::create([
            'channel' => $channel,
            'gateway' => $gateway,
            'recipient' => $recipient,
            'content' => $content,
            'response' => $response,
            'status' => $status,
            'error_message' => $error
        ]);
    }
}
