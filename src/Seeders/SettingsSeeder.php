<?php

namespace OiLab\OiLaravelSettings\Seeders;

use Illuminate\Database\Seeder;
use OiLab\OiLaravelSettings\Models\Setting;
use OiLab\OiLaravelSettings\OiLaravelSettings;
use OiLab\OiLaravelSettings\SettingsManager;

/**
 * Definition-driven base seeder. Extend it and implement {@see definitions()}
 * to declare the settings an application ships with. Each definition is
 * upserted idempotently by [scope, key], and the cache is flushed at the end so
 * seeded values are visible immediately.
 *
 * A definition is an array shape:
 *   ['key' => string, 'value' => mixed, 'type' => ?string, 'label' => ?string, 'scope' => string|int|null]
 */
abstract class SettingsSeeder extends Seeder
{
    /**
     * The settings to seed.
     *
     * @return array<int, array{key: string, value: mixed, type?: string, label?: string, scope?: string|int|null}>
     */
    abstract protected function definitions(): array;

    public function run(): void
    {
        $model = OiLaravelSettings::settingModel();
        $default = OiLaravelSettings::defaultType();

        $scopes = [];

        foreach ($this->definitions() as $definition) {
            $scope = $definition['scope'] ?? null;
            $type = $definition['type'] ?? $default;

            /** @var Setting $setting */
            $setting = $model::firstOrNew([
                'scope' => $scope,
                'key' => $definition['key'],
            ]);

            $setting->type = $type;
            $setting->label = $definition['label'] ?? $definition['key'];
            $setting->value = $definition['value'];
            $setting->save();

            $scopes[(string) $scope] = $scope;
        }

        // Settings are written directly (not via the manager), so flush the
        // long-lived per-scope cache to surface the seeded values immediately.
        $manager = app(SettingsManager::class);

        foreach ($scopes as $scope) {
            $manager->forget($scope);
        }
    }
}
