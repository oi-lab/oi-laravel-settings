# Changelog

All notable changes to `oi-lab/oi-laravel-settings` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.0.0] - 2026-07-01

### Added

- `Setting` Eloquent model backed by a `settings` table (`scope`, `key`,
  `label`, `type`, `value`), unique on `[scope, key]`.
- Scoped resolution with global fallback: `current scope → global (null) →
  default`, via a configurable scope resolver or `Settings::resolveScopeUsing()`.
- Type registry casting `value` from the sibling `type` column — primitives
  (`string`, `integer`, `float`, `boolean`, `json`) and `spatie/laravel-data`
  value objects stored as JSON (`SettingValueCast`, `SettingTypeRegistry`).
- `SettingData` (`Spatie\LaravelData\Data`) exposed from `Setting::toData()`.
- `MailContent` example `Data` value object (type `mail`).
- `SettingsManager` singleton with `get`/`set`/`has`/`all`/`delete`/`forget`,
  per-scope caching busted on every write.
- `Settings` facade and a `setting()` helper.
- `OiLaravelSettings` static resolver for the model, table, types and default
  type.
- Definition-driven `SettingsSeeder` base class and a `SettingFactory`.
- Publishable config and migration; PHP 8.2–8.4 × Laravel 11–13.
- AI-assistant skill (`oilab-laravel-settings`) and documentation tree.
- Test suite: 25 Pest tests (Unit + Feature) via Orchestra Testbench.
