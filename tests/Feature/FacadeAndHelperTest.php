<?php

use OiLab\OiLaravelSettings\Facades\Settings;
use OiLab\OiLaravelSettings\SettingsManager;

it('reads and writes through the facade', function () {
    Settings::set('APP_TAGLINE', 'Hello', label: 'Tagline');

    expect(Settings::get('APP_TAGLINE'))->toBe('Hello')
        ->and(Settings::has('APP_TAGLINE'))->toBeTrue();
});

it('returns the manager when the helper is called without a key', function () {
    expect(setting())->toBeInstanceOf(SettingsManager::class);
});

it('reads a value through the helper', function () {
    Settings::set('COLOR', 'blue');

    expect(setting('COLOR'))->toBe('blue')
        ->and(setting('UNKNOWN', 'default'))->toBe('default');
});

it('writes values through the helper array form', function () {
    setting(['ONE' => '1', 'TWO' => '2']);

    expect(setting('ONE'))->toBe('1')
        ->and(setting('TWO'))->toBe('2');
});
