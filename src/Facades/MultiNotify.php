<?php

namespace LaravelMultiNotify\Facades;

use Illuminate\Support\Facades\Facade;

class MultiNotify extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'multi-notify';
    }
}
