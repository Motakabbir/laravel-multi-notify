<?php

namespace LaravelMultiNotify\Services;

use Illuminate\Support\Manager;
use LaravelMultiNotify\Contracts\NotificationGateway;
use LaravelMultiNotify\Exceptions\ChannelNotFoundException;
use LaravelMultiNotify\Jobs\SendPushJob;
use LaravelMultiNotify\Jobs\SendSmsJob;
use LaravelMultiNotify\Jobs\SendEmailJob;
use Illuminate\Container\Container;
use LaravelMultiNotify\Traits\NotificationLoggable;

class MultiNotifyService extends Manager
{
    use NotificationLoggable;

    protected $smsService;
    protected $pushService;
    protected $emailService;

    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->emailService = $container->make(EmailChannelService::class);
        $this->smsService = $container->make(SmsChannelService::class);
        $this->pushService = $container->make(PushChannelService::class);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return config('multi-notify.default', 'email');
    }

    /**
     * Send an SMS notification
     *
     * @param string|array $to
     * @param array $data
     * @param string|null $gateway
     * @param bool $queue
     * @return array
     */
    public function sms($to, array $data, ?string $gateway = null, bool $queue = true): array
    {
        $gateway = $gateway ?? config('multi-notify.sms.default');

        if ($queue) {
            dispatch(new SendSmsJob($to, $data, $gateway));
            return ['queued' => true, 'recipients' => $to];
        }

        return $this->smsService->send($to, $data, $gateway);
    }

    /**
     * Send a push notification
     *
     * @param string|array $to
     * @param array $data
     * @param string|null $service
     * @param bool $queue
     * @return array
     */
    public function push($to, array $data, ?string $service = null, bool $queue = true): array
    {
        if (!isset($data['title'])) {
            return ['success' => false, 'error' => 'Push notification title is required'];
        }

        $service = $service ?? config('multi-notify.push.default');

        if ($queue) {
            dispatch(new SendPushJob($to, $data, $service));
            return ['success' => true, 'queued' => true, 'recipients' => $to];
        }

        return array_merge(['success' => true], $this->pushService->send($to, $data, $service));
    }

    /**
     * Send an email
     *
     * @param string|array $to
     * @param array $data
     * @param bool $queue
     * @return array
     */
    public function email($to, array $data, bool $queue = true): array
    {
        $gateway = config('multi-notify.email.default');

        if ($queue) {
            dispatch(new SendEmailJob($to, $data, $gateway));
            return ['queued' => true, 'recipients' => $to];
        }

        return $this->emailService->send($to, $data, $gateway);
    }

    /**
     * Get a channel service instance
     *
     * @param string $channel
     * @return BaseChannelService
     * @throws ChannelNotFoundException
     */
    protected function channel(string $channel): BaseChannelService
    {
        switch ($channel) {
            case 'sms':
                return $this->smsService;
            case 'push':
                return $this->pushService;
            case 'email':
                return $this->emailService;
            default:
                throw new ChannelNotFoundException("Channel [{$channel}] not found");
        }
    }
    /**
     * Get the gateway instance for a channel
     *
     * @param string $channel
     * @param string|null $name
     * @return NotificationGateway
     * @throws ChannelNotFoundException
     */
    protected function gateway(string $channel, ?string $name = null): NotificationGateway
    {
        $channelService = $this->channel($channel);
        return $channelService->getGatewayInstance($name);
    }

    /**
     * Create a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        $method = 'create' . ucfirst($driver) . 'Driver';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new ChannelNotFoundException("Driver [{$driver}] not supported.");
    }
}
