---
title: Types & value objects
description: The type registry and spatie/laravel-data casts
section: usage
order: 4
---

# Types & value objects

Each setting has a `type`. The **type registry**
(`config('oi-laravel-settings.types')`) maps that type to the way its `value`
column is cast:

```php
'types' => [
    'string'  => 'string',
    'integer' => 'integer',
    'float'   => 'float',
    'boolean' => 'boolean',
    'json'    => 'json',
    'mail'    => \OiLab\OiLaravelSettings\Data\MailContent::class,
],
```

- **Primitive keywords** — `string`, `integer`, `float`, `boolean`, `json`.
- **`Data` classes** — any `Spatie\LaravelData\Data` subclass. The value is
  stored as JSON and rehydrated into the object on read.

## Reading & writing typed values

```php
use OiLab\OiLaravelSettings\Data\MailContent;

Settings::set('WELCOME_MAIL', new MailContent(subject: 'Hi :name'), type: 'mail');

$mail = Settings::get('WELCOME_MAIL'); // MailContent instance
$mail->subject;                        // 'Hi :name'
```

Primitives cast both ways:

```php
Settings::set('MAX_ITEMS', 10, type: 'integer');
Settings::get('MAX_ITEMS'); // int(10)

Settings::set('FLAGS', ['a' => 1], type: 'json');
Settings::get('FLAGS'); // ['a' => 1]
```

## Adding your own value object

Create a `Data` class and register it under a type key — no application code
branches on `type`:

```php
use Spatie\LaravelData\Data;

class AddressData extends Data
{
    public function __construct(
        public string $line1,
        public string $city,
        public string $country,
    ) {}
}
```

```php
'types' => [
    // …
    'address' => \App\Data\AddressData::class,
],

Settings::set('STORE_ADDRESS', new AddressData('1 rue…', 'Paris', 'FR'), type: 'address');
```

Because value objects are `Data` classes, they are also picked up by
**OI Laravel TS** to generate matching TypeScript interfaces for an Inertia /
React front-end.
