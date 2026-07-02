<?php

namespace OiLab\OiLaravelSettings\Tests;

use OiLab\OiLaravelSettings\Facades\Settings;
use OiLab\OiLaravelSettings\OiLaravelSettingsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelData\LaravelDataServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            OiLaravelSettingsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Settings' => Settings::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }
}
