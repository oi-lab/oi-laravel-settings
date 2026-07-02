# Oi Laravel Settings — AI Context

Scoped, typed application settings for Laravel. Key/value pairs live in a
`settings` table, are cached per scope, and are cast to real PHP types — including
`spatie/laravel-data` value objects — via a configurable type registry.

## Core concepts

- **Setting model** (`OiLab\OiLaravelSettings\Models\Setting`) — columns
  `scope` (nullable, `null` = global), `key`, `label`, `type`, `value` (text).
  Unique on `[scope, key]`. The `value` column is cast by `SettingValueCast`
  based on the sibling `type` column.
- **Scope** — any `string|int|null`. `null` is the global layer. A read resolves
  `current scope → global (null) → provided default`. The current scope comes
  from `config('oi-laravel-settings.scope_resolver')` or a runtime override
  `Settings::resolveScopeUsing(fn () => …)`.
- **Type registry** — `config('oi-laravel-settings.types')` maps a `type` string
  to either a primitive keyword (`string|integer|float|boolean|json`) or a
  `Spatie\LaravelData\Data` class stored as JSON (e.g. `mail => MailContent`).
- **SettingsManager** — the singleton behind the `Settings` facade and the
  `setting()` helper. Caches per-scope maps (busted on every write).

## Public API

```php
use OiLab\OiLaravelSettings\Facades\Settings;

Settings::get('KEY', $default);                 // current scope, global fallback
Settings::set('KEY', $value, type: 'boolean');  // create/update + bust cache
Settings::has('KEY');
Settings::all();                                 // merged global + current scope
Settings::delete('KEY');                         // fall back to next layer
Settings::forget();                              // flush the cache for a scope

// Explicit scope on any method:
Settings::get('KEY', $default, scope: 'shop-2');
Settings::set('KEY', $value, scope: null);       // write to the global layer

// Helper:
setting('KEY');            // get
setting('KEY', $default);  // get with default
setting(['A' => 1]);       // set
setting();                 // the SettingsManager instance
```

`set()` never changes an existing setting's `type`/`label` unless you pass them
explicitly, so writing a value can't silently reinterpret it.

## Typed value objects

Register a `Data` class under a type key and it is cast automatically:

```php
// config/oi-laravel-settings.php
'types' => [
    // …primitives…
    'mail' => \OiLab\OiLaravelSettings\Data\MailContent::class,
    'address' => \App\Data\AddressData::class, // your own Spatie Data class
],

Settings::set('WELCOME_MAIL', new MailContent(subject: 'Hi'), type: 'mail');
$mail = Settings::get('WELCOME_MAIL'); // MailContent instance
```

## Seeding

Extend the definition-driven base seeder:

```php
use OiLab\OiLaravelSettings\Seeders\SettingsSeeder;

class AppSettingsSeeder extends SettingsSeeder
{
    protected function definitions(): array
    {
        return [
            ['key' => 'SITE_ONLINE', 'value' => true, 'type' => 'boolean', 'label' => 'Site online'],
            ['key' => 'THEME', 'value' => 'dark', 'scope' => 'shop-2'],
        ];
    }
}
```

Definitions upsert idempotently by `[scope, key]` and flush the cache.

## Configuration

Everything is resolved through `OiLab\OiLaravelSettings\OiLaravelSettings`
(`settingModel()`, `tableName()`, `types()`, `defaultType()`) — never hardcode
`Setting::class` or a type map. Override the model, table, scope resolver, cache
store/ttl and type registry in `config/oi-laravel-settings.php`.

```bash
php artisan vendor:publish --tag=oi-laravel-settings-config
php artisan vendor:publish --tag=oi-laravel-settings-migrations   # optional
```

## Conventions

- Read/write settings through the `Settings` facade, `setting()` helper, or the
  injected `SettingsManager` — never query the `Setting` model directly for
  values (you would bypass scope fallback and the cache).
- To register the current scope, prefer `Settings::resolveScopeUsing()` in a
  service provider over a config closure when using `config:cache`.
- Add new setting types by registering them in the config `types` map, not by
  branching on `type` in application code.

## Updating the AI Skill

After updating this package, re-install the skill files:

```bash
php artisan oi:install-ai-skill
```
