---
title: Installation
description: Install the package, run the migration, and publish the config
section: getting-started
order: 2
---

# Installation

## Requirements

- PHP 8.2+
- Laravel 11, 12 or 13
- `spatie/laravel-data` ^4.0

## Install

```bash
composer require oi-lab/oi-laravel-settings
```

The service provider is auto-discovered and the package migration is loaded
automatically. Run it with:

```bash
php artisan migrate
```

This creates the `settings` table (`scope`, `key`, `label`, `type`, `value`,
unique on `[scope, key]`).

## Publishing

Publish the config to customise the model, scope resolver, cache and type
registry:

```bash
php artisan vendor:publish --tag=oi-laravel-settings-config
```

If you prefer to own the migration (e.g. to rename the table), publish it too:

```bash
php artisan vendor:publish --tag=oi-laravel-settings-migrations
```

## First read/write

```php
use OiLab\OiLaravelSettings\Facades\Settings;

Settings::set('SITE_ONLINE', true, type: 'boolean', label: 'Site online');

Settings::get('SITE_ONLINE');   // true
setting('SITE_ONLINE', false);  // helper, with default
```

Next: [Usage](../usage/_index.md).
