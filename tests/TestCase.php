<?php

namespace LaravelMultiNotify\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use LaravelMultiNotify\MultiNotifyServiceProvider;
use LaravelMultiNotify\Tests\RefreshDatabase;
use LaravelMultiNotify\Tests\TestTraits\DatabaseAssertions;
use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, DatabaseAssertions;
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->runDatabaseMigrations();
    }

    protected function runDatabaseMigrations()
    {
        $this->artisan('migrate:fresh', [
            '--env' => 'testing',
            '--path' => __DIR__ . '/../database/migrations',
            '--realpath' => true,
        ]);

        $this->beginDatabaseTransaction();
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
