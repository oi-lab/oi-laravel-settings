<?php

use OiLab\OiLaravelSettings\Data\MailContent;
use OiLab\OiLaravelSettings\Support\SettingTypeRegistry;

it('decodes primitive types', function () {
    expect(SettingTypeRegistry::decode('string', 'hello'))->toBe('hello')
        ->and(SettingTypeRegistry::decode('integer', '42'))->toBe(42)
        ->and(SettingTypeRegistry::decode('float', '3.5'))->toBe(3.5)
        ->and(SettingTypeRegistry::decode('boolean', '1'))->toBeTrue()
        ->and(SettingTypeRegistry::decode('boolean', '0'))->toBeFalse()
        ->and(SettingTypeRegistry::decode('json', '{"a":1}'))->toBe(['a' => 1]);
});

it('encodes primitive types', function () {
    expect(SettingTypeRegistry::encode('string', 'hello'))->toBe('hello')
        ->and(SettingTypeRegistry::encode('integer', 42))->toBe('42')
        ->and(SettingTypeRegistry::encode('boolean', true))->toBe('1')
        ->and(SettingTypeRegistry::encode('boolean', false))->toBe('0')
        ->and(SettingTypeRegistry::encode('json', ['a' => 1]))->toBe('{"a":1}');
});

it('passes null through both ways', function () {
    expect(SettingTypeRegistry::decode('string', null))->toBeNull()
        ->and(SettingTypeRegistry::encode('string', null))->toBeNull();
});

it('casts a laravel-data value object to and from JSON', function () {
    $mail = new MailContent(subject: 'Hi :name', body: 'Body', actionLabel: 'Go');

    $encoded = SettingTypeRegistry::encode('mail', $mail);

    expect($encoded)->toBeString()
        ->and(json_decode($encoded, true))->toMatchArray([
            'subject' => 'Hi :name',
            'body' => 'Body',
            'action_label' => 'Go',
        ]);

    $decoded = SettingTypeRegistry::decode('mail', $encoded);

    expect($decoded)->toBeInstanceOf(MailContent::class)
        ->and($decoded->subject)->toBe('Hi :name')
        ->and($decoded->actionLabel)->toBe('Go');
});

it('falls back to the default type for unknown types', function () {
    expect(SettingTypeRegistry::decode('does-not-exist', 'raw'))->toBe('raw');
});
