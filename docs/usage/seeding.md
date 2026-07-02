---
title: Seeding
description: Shipping default settings with the base seeder
section: usage
order: 5
---

# Seeding

Extend the definition-driven base seeder to ship an application's default
settings. Each definition is upserted idempotently by `[scope, key]`, and the
per-scope cache is flushed at the end.

```php
use OiLab\OiLaravelSettings\Seeders\SettingsSeeder;

class AppSettingsSeeder extends SettingsSeeder
{
    protected function definitions(): array
    {
        return [
            ['key' => 'SITE_ONLINE', 'value' => true, 'type' => 'boolean', 'label' => 'Site online'],
            ['key' => 'MAX_ITEMS',   'value' => 10,   'type' => 'integer'],

            // Scoped override (null = global, omitted = global):
            ['key' => 'THEME', 'value' => 'dark',  'scope' => null],
            ['key' => 'THEME', 'value' => 'light', 'scope' => 'shop-2'],

            // A typed value object:
            ['key' => 'WELCOME_MAIL', 'type' => 'mail', 'value' => [
                'subject' => 'Welcome!',
                'body'    => 'Glad to have you.',
            ]],
        ];
    }
}
```

A definition is the array shape:

```php
[
    'key'   => string,          // required
    'value' => mixed,           // required (array or value object for typed settings)
    'type'  => ?string,         // defaults to config('oi-laravel-settings.default_type')
    'label' => ?string,         // defaults to the key
    'scope' => string|int|null, // defaults to null (global)
]
```

Register it from `DatabaseSeeder` as usual:

```php
$this->call(AppSettingsSeeder::class);
```

Because it upserts, re-running the seeder is safe and updates existing rows in
place.
