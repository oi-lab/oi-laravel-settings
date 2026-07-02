<?php

namespace OiLab\OiLaravelSettings\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use OiLab\OiLaravelSettings\Models\Setting;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scope' => null,
            'key' => Str::upper(Str::snake($this->faker->unique()->words(2, true))),
            'label' => Str::title($this->faker->words(2, true)),
            'type' => 'string',
            'value' => $this->faker->word(),
        ];
    }

    /**
     * Attach the setting to a specific scope (null = global).
     */
    public function scope(string|int|null $scope): static
    {
        return $this->state(['scope' => $scope === null ? null : (string) $scope]);
    }

    /**
     * Set the setting type and value together so the value is cast correctly.
     */
    public function typed(string $type, mixed $value): static
    {
        return $this->state([
            'type' => $type,
            'value' => $value,
        ]);
    }
}
