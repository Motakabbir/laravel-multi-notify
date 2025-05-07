<?php

namespace LaravelMultiNotify\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use LaravelMultiNotify\MultiNotifyServiceProvider;
use LaravelMultiNotify\Tests\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->refreshDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            MultiNotifyServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'MultiNotify' => \LaravelMultiNotify\Facades\MultiNotify::class,
        ];
    }
}
