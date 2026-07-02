<?php

namespace OiLab\OiLaravelSettings\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Example typed setting value: the content of a transactional email.
 *
 * Demonstrates how a Spatie laravel-data object becomes a first-class setting
 * type. Register it (or your own value object) under a type key in
 * config('oi-laravel-settings.types') and the package casts the JSON column to
 * and from this object automatically. Property names map to snake_case keys in
 * the stored JSON.
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class MailContent extends Data
{
    /**
     * @param  array<int, string>  $attachments
     */
    public function __construct(
        public string $subject = '',
        public string $summary = '',
        public string $body = '',
        public ?string $actionLabel = null,
        public ?string $actionUrl = null,
        public ?string $footer = null,
        public ?string $replyTo = null,
        public array $attachments = [],
    ) {}

    /**
     * Replace :placeholders across the textual fields, returning a new instance.
     *
     * @param  array<string, string>  $variables
     */
    public function replaceVariables(array $variables): self
    {
        $replace = [];

        foreach ($variables as $key => $value) {
            $replace[':'.$key] = (string) $value;
        }

        return new self(
            subject: strtr($this->subject, $replace),
            summary: strtr($this->summary, $replace),
            body: strtr($this->body, $replace),
            actionLabel: $this->actionLabel !== null ? strtr($this->actionLabel, $replace) : null,
            actionUrl: $this->actionUrl !== null ? strtr($this->actionUrl, $replace) : null,
            footer: $this->footer !== null ? strtr($this->footer, $replace) : null,
            replyTo: $this->replyTo !== null ? strtr($this->replyTo, $replace) : null,
            attachments: $this->attachments,
        );
    }
}
