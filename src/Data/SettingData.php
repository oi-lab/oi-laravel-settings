<?php

namespace OiLab\OiLaravelSettings\Data;

use OiLab\OiLaravelSettings\Models\Setting;
use Spatie\LaravelData\Data;

/**
 * Typed representation of a {@see Setting} row.
 *
 * `value` stays `mixed` because it carries the already-cast runtime value,
 * which may itself be a primitive, an array, or a registered `Data` value
 * object depending on the setting's `type`.
 */
class SettingData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $scope,
        public readonly string $key,
        public readonly string $label,
        public readonly string $type,
        public readonly mixed $value,
    ) {}

    public static function fromModel(Setting $setting): self
    {
        return new self(
            id: $setting->id,
            scope: $setting->scope,
            key: $setting->key,
            label: $setting->label,
            type: $setting->type,
            value: $setting->value,
        );
    }
}
