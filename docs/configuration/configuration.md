---
title: Configuration reference
description: Every option in config/oi-laravel-settings.php
section: configuration
order: 2
---

# Configuration reference

Publish the config file:

```bash
php artisan vendor:publish --tag=oi-laravel-settings-config
```

```php
return [
    'table' => 'settings',

    'models' => [
        'setting' => \OiLab\OiLaravelSettings\Models\Setting::class,
    ],

    'scope_resolver' => null,
    'default_scope'  => null,

    'cache' => [
        'enabled' => true,
        'store'   => null,
        'prefix'  => 'oi-settings',
        'ttl'     => null,
    ],

    'default_type' => 'string',

    'types' => [
        'string'  => 'string',
        'integer' => 'integer',
        'float'   => 'float',
        'boolean' => 'boolean',
        'json'    => 'json',
        'mail'    => \OiLab\OiLaravelSettings\Data\MailContent::class,
    ],
];
```

## Options

| Key | Description |
|-----|-------------|
| `table` | The settings table name. Also read by the migration. |
| `models.setting` | The Eloquent model. Point it at a subclass to add behaviour; always resolved through the `OiLaravelSettings` resolver. |
| `scope_resolver` | Returns the current scope. `null`, a `Closure`, or an invokable class-string. Prefer `Settings::resolveScopeUsing()` when using `config:cache`. |
| `default_scope` | Scope used when no resolver is set (default `null` = global). |
| `cache.enabled` | Toggle per-scope map caching. |
| `cache.store` | Cache store name (`null` = default store). |
| `cache.prefix` | Cache key prefix. |
| `cache.ttl` | Seconds to cache, or `null` to cache forever. |
| `default_type` | Fallback type when a setting has none. |
| `types` | The type registry: `type => primitive keyword` or `Data` class. |

## Resolving values in code

Never hardcode the model or the type map. Read them through the resolver:

```php
use OiLab\OiLaravelSettings\OiLaravelSettings;

OiLaravelSettings::settingModel(); // configured model class
OiLaravelSettings::tableName();
OiLaravelSettings::types();
OiLaravelSettings::defaultType();
```
