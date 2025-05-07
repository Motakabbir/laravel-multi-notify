<?php

namespace LaravelMultiNotify\Services;

use Illuminate\Container\Container;

class EmailChannelService extends BaseChannelService
{
    public function __construct(Container $container)
    {
        parent::__construct($container, 'email');
    }

    public function send($to, array $data): array
    {
        return $this->makeGateway()->send($to, $data);
    }
}
