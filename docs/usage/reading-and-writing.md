---
title: Reading & writing
description: The SettingsManager API and the setting() helper
section: usage
order: 2
---

# Reading & writing

## Get

```php
Settings::get('KEY');            // current scope, global fallback, else null
Settings::get('KEY', $default);  // else $default
```

Resolution order is always **current scope → global (`null`) → provided
default**. See [Scopes](scopes.md).

## Set

```php
Settings::set('KEY', $value);                              // type defaults to string
Settings::set('KEY', $value, type: 'boolean');             // cast on write & read
Settings::set('KEY', $value, type: 'mail', label: 'Home'); // label for admin UIs
```

`set()` creates or updates the row for the resolved scope, then busts the cache.
It **never** changes an existing setting's `type` or `label` unless you pass them
explicitly — writing a value can't silently reinterpret it.

## Has / delete / forget

```php
Settings::has('KEY');     // defined for the current scope or globally?
Settings::delete('KEY');  // remove the row so reads fall back to the next layer
Settings::forget();       // flush the cached map for the current scope
```

## all()

```php
Settings::all(); // ['KEY' => value, …] merged: global first, current scope wins
```

## The `setting()` helper

```php
setting();                  // the SettingsManager instance
setting('KEY');             // get
setting('KEY', $default);   // get with default
setting(['A' => 1, 'B' => 2]); // set several, returns the manager
```

## Injecting the manager

```php
use OiLab\OiLaravelSettings\SettingsManager;

public function __construct(private SettingsManager $settings) {}
```

The manager is a singleton, so a runtime scope override
(`$this->settings->resolveScopeUsing(...)`) sticks for the request.
