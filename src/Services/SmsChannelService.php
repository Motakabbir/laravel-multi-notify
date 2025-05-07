<?php

namespace LaravelMultiNotify\Services;

use Illuminate\Container\Container;
use LaravelMultiNotify\Exceptions\ChannelNotFoundException;

class SmsChannelService extends BaseChannelService
{
    protected $currentGateway;

    public function __construct(Container $container)
    {
        parent::__construct($container, 'sms');
    }

    /**
     * Send SMS through the specified or default gateway
     *
     * @param string|array $to
     * @param array $data
     * @param string|null $gateway
     * @return array
     * @throws ChannelNotFoundException
     */
    public function send($to, array $data, ?string $gateway = null): array
    {
        $this->currentGateway = $this->makeGateway($gateway);
        return $this->currentGateway->send($to, $data);
    }

    /**
     * Get the current gateway instance
     *
     * @return mixed
     */
    public function getCurrentGateway()
    {
        return $this->currentGateway;
    }

    /**
     * Get the default gateway name
     *
     * @return string|null
     */
    public function getDefaultGateway(): ?string
    {
        return config('multi-notify.sms.default');
    }
}
