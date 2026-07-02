---
title: Introduction
description: Discover OI Laravel Settings and what it can do for your project
section: getting-started
order: 1
---

# OI Laravel Settings

OI Laravel Settings is a small, opinionated store for application settings:
key/value pairs persisted in a `settings` table, cached per scope, and cast to
real PHP types — including [spatie/laravel-data](https://spatie.be/docs/laravel-data)
value objects — through a configurable type registry.

## Highlights

- **Scoped values** — every setting belongs to an optional `scope` (`null` = the
  global layer). Reads resolve `current scope → global → default`, so a
  multi-tenant / multi-store app can override any global default per scope.
- **Typed casts** — a setting's `type` decides how its `value` is cast. Built-in
  primitives (`string`, `integer`, `float`, `boolean`, `json`) plus any
  `Data` value object you register (e.g. an email template).
- **Caching** — per-scope maps are cached and automatically busted on write.
- **Ergonomic API** — the `Settings` facade, a `setting()` helper, or the
  injected `SettingsManager`.
- **Definition-driven seeding** — ship default settings with a base seeder.

Head to [Installation](installation.md) to get started.
