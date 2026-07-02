<?php

namespace OiLab\OiLaravelSettings;

use Closure;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Cache;
use OiLab\OiLaravelSettings\Models\Setting;

/**
 * Central access point for application settings.
 *
 * Settings are resolved per scope: a value defined for the current scope wins,
 * otherwise the manager falls back to the global layer (scope = null) and
 * finally to the provided default. Per-scope maps are cached and busted on
 * every write.
 *
 * Pass `scope` explicitly to any method to target a specific scope; leave it as
 * the {@see self::CURRENT} sentinel to use the resolved current scope.
 */
class SettingsManager
{
    /**
     * Sentinel meaning "use the current scope". Distinct from null, which is a
     * valid scope (the global layer).
     */
    public const CURRENT = false;

    /**
     * Runtime override for the scope resolver, taking precedence over config.
     */
    protected ?Closure $scopeResolver = null;

    /**
     * Register the closure used to resolve the current scope. Prefer this over
     * the config entry when config caching is enabled.
     */
    public function resolveScopeUsing(Closure $resolver): static
    {
        $this->scopeResolver = $resolver;

        return $this;
    }

    /**
     * Resolve a setting value, falling back to the global layer then $default.
     */
    public function get(string $key, mixed $default = null, string|int|null|false $scope = self::CURRENT): mixed
    {
        $scope = $this->resolveScope($scope);

        $map = $this->map($scope);

        if (array_key_exists($key, $map)) {
            return $map[$key];
        }

        if ($scope !== null) {
            $global = $this->map(null);

            if (array_key_exists($key, $global)) {
                return $global[$key];
            }
        }

        return $default;
    }

    /**
     * Whether a setting is defined for the given (or current) scope or globally.
     */
    public function has(string $key, string|int|null|false $scope = self::CURRENT): bool
    {
        $sentinel = new \stdClass;

        return $this->get($key, $sentinel, $scope) !== $sentinel;
    }

    /**
     * Create or update a setting and refresh the cached map.
     */
    public function set(
        string $key,
        mixed $value,
        ?string $type = null,
        ?string $label = null,
        string|int|null|false $scope = self::CURRENT,
    ): Setting {
        $scope = $this->resolveScope($scope);

        $model = OiLaravelSettings::settingModel();

        /** @var Setting $setting */
        $setting = $model::firstOrNew(['scope' => $scope, 'key' => $key]);

        // Only override type/label when explicitly provided so writing a value
        // never silently changes the type of an existing setting.
        if (! $setting->exists) {
            $setting->type = $type ?? OiLaravelSettings::defaultType();
            $setting->label = $label ?? $key;
        } else {
            if ($type !== null) {
                $setting->type = $type;
            }

            if ($label !== null) {
                $setting->label = $label;
            }
        }

        // The value cast encodes based on `type`, which is now resolved.
        $setting->value = $value;
        $setting->save();

        $this->forget($scope);

        return $setting;
    }

    /**
     * Delete a setting so reads fall back to the next layer.
     */
    public function delete(string $key, string|int|null|false $scope = self::CURRENT): void
    {
        $scope = $this->resolveScope($scope);

        $model = OiLaravelSettings::settingModel();

        $model::query()->where('scope', $scope)->where('key', $key)->delete();

        $this->forget($scope);
    }

    /**
     * The full resolved key/value map for the given (or current) scope, merged
     * on top of the global layer.
     *
     * @return array<string, mixed>
     */
    public function all(string|int|null|false $scope = self::CURRENT): array
    {
        $scope = $this->resolveScope($scope);

        $base = $scope === null ? [] : $this->map(null);

        return array_merge($base, $this->map($scope));
    }

    /**
     * Forget the cached map for a scope (or every scope when passed a boolean
     * sentinel resolving to the current scope).
     */
    public function forget(string|int|null|false $scope = self::CURRENT): void
    {
        $scope = $this->resolveScope($scope);

        $this->cache()->forget($this->cacheKey($scope));
    }

    /**
     * The cached key/value map of the settings owned by a single scope.
     *
     * @return array<string, mixed>
     */
    protected function map(string|int|null $scope): array
    {
        if (! config('oi-laravel-settings.cache.enabled', true)) {
            return $this->fetchMap($scope);
        }

        $key = $this->cacheKey($scope);
        $ttl = config('oi-laravel-settings.cache.ttl');

        if ($ttl === null) {
            return $this->cache()->rememberForever($key, fn () => $this->fetchMap($scope));
        }

        return $this->cache()->remember($key, $ttl, fn () => $this->fetchMap($scope));
    }

    /**
     * @return array<string, mixed>
     */
    protected function fetchMap(string|int|null $scope): array
    {
        $model = OiLaravelSettings::settingModel();

        return $model::query()
            ->where('scope', $scope)
            ->get()
            ->mapWithKeys(fn (Setting $setting): array => [$setting->key => $setting->value])
            ->all();
    }

    /**
     * Normalise the scope argument: the sentinel resolves to the current scope,
     * otherwise the value is used verbatim (null being the global layer).
     */
    protected function resolveScope(string|int|null|false $scope): string|int|null
    {
        if ($scope !== self::CURRENT) {
            return $scope;
        }

        return $this->currentScope();
    }

    /**
     * The scope used when none is provided, from the runtime override, the
     * config resolver, or the configured default scope.
     */
    public function currentScope(): string|int|null
    {
        if ($this->scopeResolver !== null) {
            return ($this->scopeResolver)();
        }

        $resolver = config('oi-laravel-settings.scope_resolver');

        if ($resolver instanceof Closure) {
            return $resolver();
        }

        if (is_string($resolver) && class_exists($resolver)) {
            return app($resolver)();
        }

        return config('oi-laravel-settings.default_scope');
    }

    protected function cacheKey(string|int|null $scope): string
    {
        $prefix = config('oi-laravel-settings.cache.prefix', 'oi-settings');

        return $prefix.'.scope.'.($scope === null ? 'global' : $scope);
    }

    protected function cache(): CacheRepository
    {
        return Cache::store(config('oi-laravel-settings.cache.store'));
    }
}
