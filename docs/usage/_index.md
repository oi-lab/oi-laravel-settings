---
title: Overview
description: Reading, writing, scoping and typing settings
section: usage
order: 1
---

# Usage

There are three ways to reach the store, all backed by the same
`SettingsManager` singleton:

```php
use OiLab\OiLaravelSettings\Facades\Settings;

Settings::get('KEY', $default);
```

```php
// The helper
setting('KEY', $default);
```

```php
use OiLab\OiLaravelSettings\SettingsManager;

public function __construct(private SettingsManager $settings) {}

$this->settings->get('KEY');
```

Explore:

- [Reading & writing](reading-and-writing.md) — `get`, `set`, `has`, `all`,
  `delete`, `forget`, and the `setting()` helper.
- [Scopes](scopes.md) — per-tenant / per-store values and global fallback.
- [Types & value objects](types-and-value-objects.md) — the type registry and
  spatie/laravel-data casts.
- [Seeding](seeding.md) — shipping default settings.
