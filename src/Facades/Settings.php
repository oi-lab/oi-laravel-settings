<?php

namespace OiLab\OiLaravelSettings\Facades;

use Illuminate\Support\Facades\Facade;
use OiLab\OiLaravelSettings\SettingsManager;

/**
 * @method static mixed get(string $key, mixed $default = null, string|int|null|false $scope = SettingsManager::CURRENT)
 * @method static bool has(string $key, string|int|null|false $scope = SettingsManager::CURRENT)
 * @method static \OiLab\OiLaravelSettings\Models\Setting set(string $key, mixed $value, ?string $type = null, ?string $label = null, string|int|null|false $scope = SettingsManager::CURRENT)
 * @method static void delete(string $key, string|int|null|false $scope = SettingsManager::CURRENT)
 * @method static array all(string|int|null|false $scope = SettingsManager::CURRENT)
 * @method static void forget(string|int|null|false $scope = SettingsManager::CURRENT)
 * @method static \OiLab\OiLaravelSettings\SettingsManager resolveScopeUsing(\Closure $resolver)
 * @method static string|int|null currentScope()
 *
 * @see SettingsManager
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SettingsManager::class;
    }
}
