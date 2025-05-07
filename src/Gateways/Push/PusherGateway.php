<?php

namespace LaravelMultiNotify\Gateways\Push;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Pusher\Pusher;
use LaravelMultiNotify\Traits\NotificationLoggable;

class PusherGateway implements NotificationGateway
{
    use NotificationLoggable;

    protected Pusher $pusher;

    /**
     * Set the Pusher instance (useful for testing)
     */
    public function setPusher(Pusher $pusher): void
    {
        $this->pusher = $pusher;
    }
    protected $config;

    public function __construct()
    {
        $this->config = config('multi-notify.push.services.pusher');
        $this->pusher = new Pusher(
            $this->config['app_key'],
            $this->config['app_secret'],
            $this->config['app_id'],
            [
                'cluster' => $this->config['cluster'],
                'useTLS' => true
            ]
        );
    }

    public function send($to, array $data): array
    {
        if (empty($data['event'])) {
            $error = [
                'success' => false,
                'error' => 'Event name is required'
            ];
            return [$error];
        }

        $channels = is_array($to) ? $to : [$to];
        $results = [];

        $payload = [
            'message' => $data['message'] ?? null
        ];

        if (!empty($data['title'])) {
            $payload['title'] = $data['title'];
        }

        if (!empty($data['data'])) {
            $payload = array_merge($payload, $data['data']);
        }

        foreach ($channels as $channel) {
            try {
                $options = [];
                if (strpos($channel, 'private-') === 0 || strpos($channel, 'presence-') === 0) {
                    $options['encrypted'] = true;
                }

                $response = $this->pusher->trigger($channel, $data['event'], $payload, $options);
                $result = [
                    'success' => true,
                    'channel' => $channel,
                    'response' => $response
                ];

                $this->logNotification(
                    'push',
                    'pusher',
                    $channel,
                    array_merge($payload, ['event' => $data['event']]),
                    $response,
                    'success'
                );

                $results[] = $result;
            } catch (\Exception $e) {
                $result = [
                    'success' => false,
                    'channel' => $channel,
                    'error' => $e->getMessage()
                ];

                $this->logNotification(
                    'push',
                    'pusher',
                    $channel,
                    array_merge($payload, ['event' => $data['event']]),
                    null,
                    'failed',
                    $e->getMessage()
                );

                $results[] = $result;
            }
        }

        return $results;
    }
}
