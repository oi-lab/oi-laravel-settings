<?php

use OiLab\OiLaravelSettings\SettingsManager;

if (! function_exists('setting')) {
    /**
     * Read or write settings, or grab the manager.
     *
     *   setting()                 → the SettingsManager instance
     *   setting('KEY')            → the value (current scope, global fallback)
     *   setting('KEY', $default)  → the value or $default
     *   setting(['KEY' => $val])  → set one or more settings, returns the manager
     *
     * @param  string|array<string, mixed>|null  $key
     */
    function setting(string|array|null $key = null, mixed $default = null): mixed
    {
        $manager = app(SettingsManager::class);

        if ($key === null) {
            return $manager;
        }

        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $manager->set($name, $value);
            }

            return $manager;
        }

        return $manager->get($key, $default);
    }
}
