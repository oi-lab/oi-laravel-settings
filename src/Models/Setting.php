<?php

namespace OiLab\OiLaravelSettings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OiLab\OiLaravelSettings\Casts\SettingValueCast;
use OiLab\OiLaravelSettings\Data\SettingData;
use OiLab\OiLaravelSettings\Database\Factories\SettingFactory;
use OiLab\OiLaravelSettings\OiLaravelSettings;

/**
 * A single configuration entry, scoped by an optional `scope` (null = global).
 *
 * The `value` column is a string in the database and is cast to its runtime
 * type based on the `type` column via {@see SettingValueCast}.
 *
 * @property int $id
 * @property string|null $scope
 * @property string $key
 * @property string $label
 * @property string $type
 * @property mixed $value
 */
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'scope',
        'key',
        'label',
        'type',
        'value',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => SettingValueCast::class,
        ];
    }

    public function getTable(): string
    {
        return $this->table ?? OiLaravelSettings::tableName();
    }

    public function toData(): SettingData
    {
        return SettingData::fromModel($this);
    }

    protected static function newFactory(): SettingFactory
    {
        return SettingFactory::new();
    }
}
