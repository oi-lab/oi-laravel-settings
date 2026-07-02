---
title: Custom model
description: Subclass the Setting model to add relations or behaviour
section: advanced
order: 2
---

# Custom model

The package resolves the setting model through
`OiLaravelSettings::settingModel()`, so you can swap in a subclass without
forking anything.

```php
namespace App\Models;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OiLab\OiLaravelSettings\Models\Setting as BaseSetting;

class Setting extends BaseSetting
{
    /**
     * Expose the scope as a real relationship when it holds a shop id.
     *
     * @return BelongsTo<Shop, $this>
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'scope');
    }
}
```

Point the config at it:

```php
// config/oi-laravel-settings.php
'models' => [
    'setting' => \App\Models\Setting::class,
],
```

The `SettingsManager`, factory and seeder all read the configured class, so your
subclass is used everywhere. Keep the `scope`, `key`, `label`, `type` and `value`
columns intact so the cast and scope resolution keep working.
