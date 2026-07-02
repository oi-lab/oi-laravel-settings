<?php

namespace OiLab\OiLaravelSettings;

use OiLab\OiLaravelSettings\Models\Setting;

/**
 * Central resolver for every configurable class / value used by the package.
 *
 * Nothing in the package hardcodes a model or a type map: everything is read
 * through this resolver so host applications can override the model, the table
 * or the type registry from config without forking the package.
 */
class OiLaravelSettings
{
    /**
     * The Eloquent model class used to persist settings.
     *
     * @return class-string<Setting>
     */
    public static function settingModel(): string
    {
        return config('oi-laravel-settings.models.setting', Setting::class);
    }

    /**
     * A fresh instance of the configured setting model.
     */
    public static function newSettingModel(): Setting
    {
        $class = static::settingModel();

        return new $class;
    }

    /**
     * The settings table name.
     */
    public static function tableName(): string
    {
        return config('oi-laravel-settings.table', 'settings');
    }

    /**
     * The type registry mapping a setting type to a primitive keyword or a
     * Spatie\LaravelData\Data value object class.
     *
     * @return array<string, string>
     */
    public static function types(): array
    {
        return config('oi-laravel-settings.types', []);
    }

    /**
     * The fallback type used when a setting has no explicit type.
     */
    public static function defaultType(): string
    {
        return config('oi-laravel-settings.default_type', 'string');
    }
}
