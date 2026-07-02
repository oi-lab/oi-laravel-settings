<?php

namespace OiLab\OiLaravelSettings\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use OiLab\OiLaravelSettings\OiLaravelSettings;
use OiLab\OiLaravelSettings\Support\SettingTypeRegistry;

/**
 * Casts a setting's `value` column based on its sibling `type` column, using
 * the configured type registry. Because the cast is type-aware it must read the
 * `type` attribute alongside the value.
 *
 * @implements CastsAttributes<mixed, mixed>
 */
class SettingValueCast implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return SettingTypeRegistry::decode($this->type($model, $attributes), $value);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, ?string>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        return [$key => SettingTypeRegistry::encode($this->type($model, $attributes), $value)];
    }

    /**
     * Resolve the setting type from the attributes being cast, falling back to
     * the model and finally the configured default type.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function type(Model $model, array $attributes): string
    {
        return $attributes['type']
            ?? $model->getAttribute('type')
            ?? OiLaravelSettings::defaultType();
    }
}
