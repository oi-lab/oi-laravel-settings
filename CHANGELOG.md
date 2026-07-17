# Changelog

All notable changes to `oi-lab/oi-laravel-settings` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

## [1.0.4] - 2026-07-17

### Fixed
- The per-scope cache now stores raw scalar payloads (`key => {type, value}`) and casts values on read, instead of caching already-cast values. Previously a typed `spatie/laravel-data` value object was serialised into the cache and, with the default `cache.ttl => null` (forever), survived deploys; when the value object's class was renamed, moved, or reshaped it deserialised to a `__PHP_Incomplete_Class`, so `Settings::get()` returned a broken object (an `instanceof` check would fail) until a manual `cache:clear`. Casting on read keeps class-bound objects out of the cache entirely. A cached map left over from the previous format is detected and refetched automatically, so no `cache:clear` is required when upgrading.

### Changed
- Documentation: the scope resolution diagram in `docs/usage/scopes.md` is now authored as a Mermaid `flowchart` block instead of ASCII art, matching the Mermaid diagram standard rendered by `oi-lab/oi-laravel-documentation`.

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
