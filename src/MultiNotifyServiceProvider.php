<?php

namespace LaravelMultiNotify;

use Illuminate\Support\ServiceProvider;
use LaravelMultiNotify\Services\MultiNotifyService;

class MultiNotifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/multi-notify.php' => config_path('multi-notify.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/multi-notify'),
        ], 'views');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'multi-notify');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/multi-notify.php', 'multi-notify');
        $this->mergeConfigFrom(__DIR__ . '/../config/bd-sms-gateways.php', 'multi-notify.bd-gateways');

        $this->app->singleton('multi-notify', function ($app) {
            return new MultiNotifyService($app);
        });

        $this->app->alias('multi-notify', MultiNotifyService::class);
    }
}
