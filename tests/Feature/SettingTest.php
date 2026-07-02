<?php

use Illuminate\Database\QueryException;
use OiLab\OiLaravelSettings\Data\MailContent;
use OiLab\OiLaravelSettings\Data\SettingData;
use OiLab\OiLaravelSettings\Models\Setting;

it('creates a Setting via the factory', function () {
    $setting = Setting::factory()->create();

    expect($setting->exists)->toBeTrue()
        ->and(Setting::count())->toBe(1);
});

it('casts the value column based on the type column', function () {
    $setting = Setting::factory()->typed('integer', 10)->create();

    expect($setting->fresh()->value)->toBe(10);
});

it('round-trips a data value object through the model', function () {
    $setting = Setting::factory()->typed('mail', new MailContent(subject: 'S', body: 'B'))->create();

    $fresh = $setting->fresh();

    expect($fresh->value)->toBeInstanceOf(MailContent::class)
        ->and($fresh->value->subject)->toBe('S');
});

it('exposes a typed SettingData via toData()', function () {
    $setting = Setting::factory()->scope('shop-2')->typed('boolean', true)->create([
        'key' => 'SITE_ONLINE',
        'label' => 'Site online',
    ]);

    $data = $setting->toData();

    expect($data)->toBeInstanceOf(SettingData::class)
        ->and($data->id)->toBe($setting->id)
        ->and($data->scope)->toBe('shop-2')
        ->and($data->key)->toBe('SITE_ONLINE')
        ->and($data->label)->toBe('Site online')
        ->and($data->type)->toBe('boolean')
        ->and($data->value)->toBeTrue();
});

it('enforces uniqueness per scope and key', function () {
    Setting::factory()->create(['scope' => 'shop-1', 'key' => 'DUP']);

    expect(fn () => Setting::factory()->create(['scope' => 'shop-1', 'key' => 'DUP']))
        ->toThrow(QueryException::class);
});
