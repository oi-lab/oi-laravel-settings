---
title: AI Assistant Skill
description: Install the bundled AI skill into your application
section: advanced
order: 3
---

# AI Assistant Skill

The package ships an AI-assistant skill (`oilab-laravel-settings`) describing its
API so tools like Claude Code and Junie can use it correctly.

## Install into your app

```bash
php artisan oi:install-ai-skill
```

This copies the skill into your app's `.claude/skills/oilab-laravel-settings/`
and `.junie/skills/oilab-laravel-settings/` directories and upserts an
`=== oi-lab/oi-laravel-settings rules ===` section into your root `CLAUDE.md`. It
is idempotent — re-running updates in place.

## Keeping it in sync

The canonical skill lives at `resources/stubs/ai-skill.md`. After changing the
package, re-run:

```bash
composer sync-ai-skills
```

which refreshes the committed copies under the package's own `.claude/` and
`.junie/` directories (it also runs automatically on `composer install`).
