# OI Laravel Settings

Use the `oi-laravel-settings` package to manage scoped, typed application settings.
Key/value pairs live in a `settings` table, are cached per scope, and are cast to
real PHP types — including `spatie/laravel-data` value objects — via the type
registry in `config/oi-laravel-settings.php`. Read and write through the `Settings`
facade, the `setting()` helper, or the injected `SettingsManager`; never query the
`Setting` model directly for values (that bypasses scope fallback and the cache).
Add new value types by registering a `Data` class in the config `types` map.

- IMPORTANT: Activate `oilab-laravel-settings` when reading or writing application
  settings, adding a new setting type / value object, seeding settings, or
  configuring per-scope (multi-tenant / multi-store) configuration in this Laravel
  application.
