<?php

namespace LaravelMultiNotify\Gateways\Push;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Messaging;
use Kreait\Firebase\Exception\MessagingException;
use LaravelMultiNotify\Traits\NotificationLoggable;
use InvalidArgumentException;

/**
 * Firebase Cloud Messaging Gateway for Laravel Multi Notify
 * 
 * This gateway implements Firebase Cloud Messaging (FCM) functionality for sending
 * push notifications to mobile devices and web browsers.
 * 
 * Features:
 * - Supports both single and batch token notifications
 * - Handles notification and data payloads
 * - Supports advanced FCM features like notification priority and TTL
 * - Automatic token validation and error handling
 * - Built-in notification logging
 */
class FirebaseGateway implements NotificationGateway
{
    use NotificationLoggable;

    protected $messaging;

    public function __construct()
    {
        $credentialsPath = config('multi-notify.push.services.firebase.credentials');

        if (!file_exists($credentialsPath)) {
            throw new InvalidArgumentException('Firebase credentials file not found');
        }

        $factory = (new Factory)->withServiceAccount($credentialsPath);
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Set the messaging service (useful for testing)
     */    public function setMessaging(Messaging $messaging): void
    {
        $this->messaging = $messaging;
    }

    /**
     * Send a push notification via Firebase Cloud Messaging
     *
     * @param string|array $to Single token or array of tokens
     * @param array $data Notification data containing:
     *                    - title: The notification title (required)
     *                    - body: The notification body (required)
     *                    - data: Additional data payload (optional)
     *                    - imageUrl: URL to an image to show (optional)
     *                    - priority: high|normal (optional, defaults to high)
     *                    - ttl: Time-to-live in seconds (optional)
     *                    - sound: Sound to play (optional)
     *                    - clickAction: Action on notification click (optional)
     *                    - color: Notification color in #rrggbb format (optional)
     * @return array Array of response results
     * @throws InvalidArgumentException when required fields are missing
     * @throws MessagingException when Firebase messaging fails
     */
    public function send($to, array $data): array
    {
        try {
            // Validate payload
            $this->validatePayload($data);

            $tokens = is_array($to) ? $to : [$to];
            $tokens = array_filter($tokens); // Remove empty tokens
            $results = [];

            foreach ($tokens as $token) {
                try {
                    // Validate token
                    if (empty($token) || !is_string($token) || strlen($token) < 32) {
                        throw new InvalidArgumentException("Invalid FCM token: {$token}");
                    }

                    $notification = $this->buildNotificationPayload($data);
                    $message = $this->buildCloudMessage($token, $notification, $data);

                    $response = $this->messaging->send($message);
                    $result = array_merge(['success' => true], $response);

                    $this->logNotification(
                        'push',
                        'firebase',
                        $token,
                        [
                            'notification' => $notification,
                            'data' => $data['data'] ?? []
                        ],
                        $result,
                        'success'
                    );

                    $results[] = $result;
                } catch (\Exception $e) {
                    $error = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];

                    $this->logNotification(
                        'push',
                        'firebase',
                        $token,
                        [
                            'notification' => $notification ?? [],
                            'data' => $data['data'] ?? []
                        ],
                        null,
                        'error',
                        $e->getMessage()
                    );

                    $results[] = $error;
                }
            }

            return $results;
        } catch (InvalidArgumentException $e) {
            // Handle malformed payload at the top level
            $this->logNotification(
                'push',
                'firebase',
                is_array($to) ? json_encode($to) : $to,
                $data,
                null,
                'error',
                $e->getMessage()
            );

            return [[
                'success' => false,
                'error' => $e->getMessage()
            ]];
        }
    }

    /**
     * Validate the notification payload
     *
     * @param array $data
     * @throws InvalidArgumentException
     */
    protected function validatePayload(array $data): void
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Notification payload cannot be empty');
        }

        if (empty($data['title'])) {
            throw new InvalidArgumentException('Notification title is required');
        }

        if (empty($data['body'])) {
            throw new InvalidArgumentException('Notification body is required');
        }

        // Validate optional fields if present
        if (isset($data['imageUrl']) && !filter_var($data['imageUrl'], FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid image URL format');
        }

        if (isset($data['priority']) && !in_array($data['priority'], ['high', 'normal'])) {
            throw new InvalidArgumentException('Invalid priority value. Must be "high" or "normal"');
        }

        if (isset($data['ttl']) && (!is_numeric($data['ttl']) || $data['ttl'] < 0)) {
            throw new InvalidArgumentException('Invalid TTL value. Must be a non-negative number');
        }
    }

    /**
     * Build the notification payload
     *
     * @param array $data
     * @return array
     */
    protected function buildNotificationPayload(array $data): array
    {
        $notification = [
            'title' => $data['title'],
            'body' => $data['body']
        ];

        // Add optional notification parameters
        if (!empty($data['imageUrl'])) {
            $notification['image'] = $data['imageUrl'];
        }
        if (!empty($data['sound'])) {
            $notification['sound'] = $data['sound'];
        }
        if (!empty($data['clickAction'])) {
            $notification['click_action'] = $data['clickAction'];
        }
        if (!empty($data['color'])) {
            $notification['color'] = $data['color'];
        }

        return $notification;
    }

    /**
     * Build the cloud message
     *
     * @param string $token
     * @param array $notification
     * @param array $data
     * @return CloudMessage
     */
    protected function buildCloudMessage(string $token, array $notification, array $data): CloudMessage
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification);
        if (!empty($data['data'])) {
            $message = $message->withData($data['data']);
        }

        if (!empty($data['priority'])) {
            $message = $message->withHighPriority($data['priority'] === 'high');
        }

        if (isset($data['ttl'])) {
            $message = $message->withTimeToLive($data['ttl']);
        }

        return $message;
    }
}
