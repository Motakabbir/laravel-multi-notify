<?php

namespace LaravelMultiNotify\Gateways\Push;

use LaravelMultiNotify\Contracts\NotificationGateway;
use Pusher\Pusher;
use LaravelMultiNotify\Traits\NotificationLoggable;
use InvalidArgumentException;

class PusherGateway implements NotificationGateway
{
    use NotificationLoggable;

    protected Pusher $pusher;
    protected $config;

    /**
     * Maximum length for channel names
     */
    private const MAX_CHANNEL_LENGTH = 164;

    /**
     * Maximum length for event names
     */
    private const MAX_EVENT_LENGTH = 200;

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

    /**
     * Set the Pusher instance (useful for testing)
     */
    public function setPusher(Pusher $pusher): void
    {
        $this->pusher = $pusher;
    }

    public function send($to, array $data): array
    {
        try {
            $this->validateEventName($data);
            $channels = is_array($to) ? $to : [$to];
            $results = [];

            $payload = $this->buildPayload($data);

            foreach ($channels as $channel) {
                try {
                    $this->validateChannelName($channel);
                    $options = $this->getChannelOptions($channel);

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
                        'error',
                        $e->getMessage()
                    );

                    $results[] = $result;
                }
            }

            return $results;
        } catch (InvalidArgumentException $e) {
            return [[
                'success' => false,
                'error' => $e->getMessage()
            ]];
        }
    }

    /**
     * Validate the event name
     *
     * @param array $data
     * @throws InvalidArgumentException
     */
    protected function validateEventName(array $data): void
    {
        if (empty($data['event'])) {
            throw new InvalidArgumentException('Event name is required');
        }

        if (!is_string($data['event'])) {
            throw new InvalidArgumentException('Event name must be a string');
        }

        if (strlen($data['event']) > self::MAX_EVENT_LENGTH) {
            throw new InvalidArgumentException('Event name is too long');
        }

        if (!preg_match('/^[a-zA-Z0-9_\-=@,.;]+$/', $data['event'])) {
            throw new InvalidArgumentException('Event name contains invalid characters');
        }
    }

    /**
     * Validate the channel name
     *
     * @param string $channel
     * @throws InvalidArgumentException
     */
    protected function validateChannelName(string $channel): void
    {
        if (empty($channel)) {
            throw new InvalidArgumentException('Channel name cannot be empty');
        }

        if (strlen($channel) > self::MAX_CHANNEL_LENGTH) {
            throw new InvalidArgumentException('Channel name is too long');
        }

        // Check channel name format
        if (!preg_match('/^(?:presence-|private-)?[a-zA-Z0-9_\-=@,.;]+$/', $channel)) {
            throw new InvalidArgumentException('Invalid channel name format');
        }

        // Validate private/presence channel prefix
        if (strpos($channel, 'private-') === 0 || strpos($channel, 'presence-') === 0) {
            $parts = explode('-', $channel, 2);
            if (empty($parts[1])) {
                throw new InvalidArgumentException('Invalid private/presence channel name');
            }
        }
    }

    /**
     * Get channel-specific options
     *
     * @param string $channel
     * @return array
     */
    protected function getChannelOptions(string $channel): array
    {
        $options = [];

        // Set encryption for private and presence channels
        if (strpos($channel, 'private-') === 0 || strpos($channel, 'presence-') === 0) {
            $options['encrypted'] = true;
        }

        return $options;
    }

    /**
     * Build the notification payload
     *
     * @param array $data
     * @return array
     */
    protected function buildPayload(array $data): array
    {
        $payload = [
            'message' => $data['message'] ?? null
        ];

        if (!empty($data['title'])) {
            $payload['title'] = $data['title'];
        }

        if (!empty($data['data']) && is_array($data['data'])) {
            $payload = array_merge($payload, $data['data']);
        }

        return $payload;
    }
}
