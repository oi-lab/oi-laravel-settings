<?php

use Illuminate\Support\Facades\Cache;
use OiLab\OiLaravelSettings\Data\MailContent;
use OiLab\OiLaravelSettings\Models\Setting;
use OiLab\OiLaravelSettings\SettingsManager;

beforeEach(function () {
    $this->manager = app(SettingsManager::class);
});

it('sets and gets a value with type casting', function () {
    $this->manager->set('SITE_ONLINE', true, type: 'boolean');

    expect($this->manager->get('SITE_ONLINE'))->toBeTrue()
        ->and(Setting::where('key', 'SITE_ONLINE')->value('value'))->toBe('1');
});

it('returns the default when a key is missing', function () {
    expect($this->manager->get('MISSING', 'fallback'))->toBe('fallback');
});

it('reports presence with has()', function () {
    $this->manager->set('PRESENT', 'x');

    expect($this->manager->has('PRESENT'))->toBeTrue()
        ->and($this->manager->has('ABSENT'))->toBeFalse();
});

it('falls back from a scope to the global layer', function () {
    $this->manager->set('THEME', 'dark', scope: null);       // global
    $this->manager->set('THEME', 'light', scope: 'shop-2');  // override

    expect($this->manager->get('THEME', scope: 'shop-2'))->toBe('light')
        ->and($this->manager->get('THEME', scope: 'shop-9'))->toBe('dark')
        ->and($this->manager->get('THEME', scope: null))->toBe('dark');
});

it('merges global and scoped maps in all()', function () {
    $this->manager->set('A', '1', scope: null);
    $this->manager->set('B', '2', scope: null);
    $this->manager->set('B', 'override', scope: 'shop-2');
    $this->manager->set('C', '3', scope: 'shop-2');

    expect($this->manager->all(scope: 'shop-2'))->toBe([
        'A' => '1',
        'B' => 'override',
        'C' => '3',
    ]);
});

it('uses the runtime scope resolver', function () {
    $this->manager->resolveScopeUsing(fn () => 'shop-7');
    $this->manager->set('X', 'seven');

    expect(Setting::where('key', 'X')->value('scope'))->toBe('shop-7')
        ->and($this->manager->get('X'))->toBe('seven');
});

it('busts the cache on write', function () {
    $this->manager->set('N', '1');
    expect($this->manager->get('N'))->toBe('1');

    $this->manager->set('N', '2');
    expect($this->manager->get('N'))->toBe('2');
});

it('deletes a setting and falls back to global', function () {
    $this->manager->set('LANG', 'fr', scope: null);
    $this->manager->set('LANG', 'en', scope: 'shop-2');

    $this->manager->delete('LANG', scope: 'shop-2');

    expect($this->manager->get('LANG', scope: 'shop-2'))->toBe('fr');
});

it('stores and resolves a typed value object', function () {
    $this->manager->set('WELCOME_MAIL', new MailContent(subject: 'Hi', body: 'B'), type: 'mail');

    $value = $this->manager->get('WELCOME_MAIL');

    expect($value)->toBeInstanceOf(MailContent::class)
        ->and($value->subject)->toBe('Hi');
});

it('does not change an existing type when writing a value without one', function () {
    $this->manager->set('FLAG', true, type: 'boolean');
    $this->manager->set('FLAG', false);

    expect($this->manager->get('FLAG'))->toBeFalse()
        ->and(Setting::where('key', 'FLAG')->value('type'))->toBe('boolean');
});

it('reads directly when caching is disabled', function () {
    config()->set('oi-laravel-settings.cache.enabled', false);

    $this->manager->set('NOCACHE', 'v');

    expect($this->manager->get('NOCACHE'))->toBe('v')
        ->and(Cache::has('oi-settings.scope.global'))->toBeFalse();
});

it('caches raw payloads rather than cast value objects', function () {
    $this->manager->set('WELCOME_MAIL', new MailContent(subject: 'Hi', body: 'B'), type: 'mail');

    $this->manager->get('WELCOME_MAIL');

    $cached = Cache::get('oi-settings.scope.global');

    expect($cached)->toHaveKey('WELCOME_MAIL')
        ->and($cached['WELCOME_MAIL']['type'])->toBe('mail')
        ->and($cached['WELCOME_MAIL']['value'])->toBeString()
        ->and($cached)->each->not->toBeInstanceOf(MailContent::class);
});

it('re-casts a typed value object on every read from cache', function () {
    $this->manager->set('WELCOME_MAIL', new MailContent(subject: 'Hi', body: 'B'), type: 'mail');

    $this->manager->get('WELCOME_MAIL'); // primes the cache

    $value = $this->manager->get('WELCOME_MAIL');

    expect($value)->toBeInstanceOf(MailContent::class)
        ->and($value->subject)->toBe('Hi');
});

it('discards and refetches a cached map left in a previous (object) format', function () {
    $this->manager->set('THEME', 'dark');

    // Simulate a cache entry poisoned by the old format, which stored cast
    // values (here an object) directly under the setting key.
    Cache::forever('oi-settings.scope.global', ['THEME' => new stdClass]);

    expect($this->manager->get('THEME'))->toBe('dark');

    $cached = Cache::get('oi-settings.scope.global');

    expect($cached['THEME'])->toBe(['type' => 'string', 'value' => 'dark']);
});
