<?php

namespace OiLab\OiLaravelSettings;

use Illuminate\Support\ServiceProvider;
use OiLab\OiLaravelSettings\Console\Commands\InstallAiSkillCommand;

class OiLaravelSettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/oi-laravel-settings.php',
            'oi-laravel-settings'
        );

        $this->app->singleton(SettingsManager::class);
        $this->app->alias(SettingsManager::class, 'oi-laravel-settings');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallAiSkillCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/oi-laravel-settings.php' => config_path('oi-laravel-settings.php'),
            ], 'oi-laravel-settings-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'oi-laravel-settings-migrations');

            $this->publishes([
                __DIR__.'/../resources/stubs/ai-skill.md' => base_path('.claude/skills/oilab-laravel-settings/SKILL.md'),
            ], 'oi-laravel-settings-skill');
        }
    }
}
