<?php

namespace OiLab\OiLaravelSettings\Support;

use OiLab\OiLaravelSettings\OiLaravelSettings;
use Spatie\LaravelData\Data;

/**
 * Translates a setting value between its stored (string) representation and its
 * runtime PHP value according to the configured type registry.
 *
 * A type resolves to either a primitive keyword (string, integer, float,
 * boolean, json) or a {@see Data} value object class stored as JSON.
 */
class SettingTypeRegistry
{
    /**
     * Resolve the caster spec for a type, falling back to the default type and
     * finally to a plain string.
     */
    public static function specFor(string $type): string
    {
        $types = OiLaravelSettings::types();

        return $types[$type]
            ?? $types[OiLaravelSettings::defaultType()]
            ?? 'string';
    }

    /**
     * Cast a raw database string into its typed PHP value.
     */
    public static function decode(string $type, ?string $raw): mixed
    {
        if ($raw === null) {
            return null;
        }

        $spec = static::specFor($type);

        if (static::isDataClass($spec)) {
            /** @var class-string<Data> $spec */
            return $spec::from(static::decodeJson($raw));
        }

        return match ($spec) {
            'integer' => (int) $raw,
            'float' => (float) $raw,
            'boolean' => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            'json' => static::decodeJson($raw),
            default => $raw,
        };
    }

    /**
     * Cast a PHP value into the string persisted in the database.
     */
    public static function encode(string $type, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $spec = static::specFor($type);

        if (static::isDataClass($spec)) {
            $array = $value instanceof Data ? $value->toArray() : (array) $value;

            return json_encode($array);
        }

        return match ($spec) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => is_array($value) ? json_encode($value) : (string) $value,
        };
    }

    /**
     * Whether the given spec is a Spatie laravel-data value object class.
     */
    public static function isDataClass(string $spec): bool
    {
        return class_exists($spec) && is_subclass_of($spec, Data::class);
    }

    /**
     * @return array<int|string, mixed>
     */
    protected static function decodeJson(string $raw): array
    {
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
