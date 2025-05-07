<?php

namespace LaravelMultiNotify\Services;

use LaravelMultiNotify\Contracts\NotificationGateway;
use LaravelMultiNotify\Exceptions\ChannelNotFoundException;
use Illuminate\Container\Container;

abstract class BaseChannelService implements NotificationGateway
{
    protected $container;
    protected $channelType;
    protected $defaultGateway;

    public function __construct(Container $container, string $channelType)
    {
        $this->container = $container;
        $this->channelType = $channelType;
        $this->defaultGateway = config("multi-notify.{$channelType}.default");
    }

    /**
     * Send a notification through the channel
     *
     * @param string|array $to
     * @param array $data
     * @param string|null $gateway
     * @return array
     */
    public function send($to, array $data, ?string $gateway = null): array
    {
        return $this->makeGateway($gateway)->send($to, $data);
    }

    /**
     * Create a gateway instance
     *
     * @param string|null $name
     * @return NotificationGateway
     * @throws ChannelNotFoundException
     */
    protected function makeGateway(?string $name = null): NotificationGateway
    {
        $config = config("multi-notify.{$this->channelType}");

        if (!$config) {
            throw new ChannelNotFoundException("Channel [{$this->channelType}] not found");
        }

        $name = $name ?? $this->defaultGateway ?? null;

        $gatewayConfig = $this->getGatewayConfig($name);

        if (!$gatewayConfig || !isset($gatewayConfig['class'])) {
            throw new ChannelNotFoundException("Gateway [{$name}] not found for channel [{$this->channelType}]");
        }

        return $this->container->make($gatewayConfig['class']);
    }

    /**
     * Get the configuration for a specific gateway
     *
     * @param string|null $name
     * @return array|null
     */
    protected function getGatewayConfig(?string $name): ?array
    {
        $config = config("multi-notify.{$this->channelType}");

        if ($this->channelType === 'push') {
            return $config['services'][$name] ?? null;
        }

        if ($this->channelType === 'sms') {
            return $config['gateways'][$name] ?? null;
        }

        if ($this->channelType === 'email') {
            return ['class' => \LaravelMultiNotify\Gateways\Email\EmailGateway::class];
        }

        return null;
    }

    /**
     * Get the default gateway name
     *
     * @return string|null
     */
    public function getDefaultGateway(): ?string
    {
        return $this->defaultGateway;
    }

    /**
     * Set the default gateway
     *
     * @param string $name
     * @return void
     */
    public function setDefaultGateway(string $name): void
    {
        $this->defaultGateway = $name;
    }
}
