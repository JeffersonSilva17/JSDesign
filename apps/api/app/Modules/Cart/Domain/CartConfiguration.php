<?php

namespace App\Modules\Cart\Domain;

final readonly class CartConfiguration
{
    /** @param array<string, mixed> $value */
    public static function validate(array $value, string $modality, bool $allowsDeferred): self
    {
        $common = ['configuration_type', 'thumbnail_choice', 'defer_briefing'];
        $fields = $modality === 'physical_personalized'
            ? [...$common, 'theme', 'personalization_text', 'event_date', 'note']
            : [...$common, 'invite_type', 'theme', 'colors', 'event_name', 'event_date', 'event_time', 'event_location'];
        if (array_diff(array_keys($value), $fields) !== [] || array_diff(array_filter($fields, static fn (string $field): bool => $field !== 'note'), array_keys($value)) !== []) {
            throw new CartFailure('invalid_configuration', 422);
        }
        $type = $modality === 'physical_personalized' ? 'physical_personalized' : 'digital_personalized';
        if (($value['configuration_type'] ?? null) !== $type || ! in_array($value['thumbnail_choice'] ?? null, ['none', 'client_file', 'create_by_js_designs'], true)) {
            throw new CartFailure('invalid_configuration', 422);
        }
        $limits = $modality === 'physical_personalized'
            ? ['theme' => 120, 'personalization_text' => 120, 'note' => 500]
            : ['theme' => 120, 'colors' => 160, 'event_name' => 120, 'event_location' => 200];
        foreach ($limits as $field => $max) {
            if ($field !== 'note' && (! is_string($value[$field] ?? null) || mb_strlen($value[$field], 'UTF-8') > $max || trim($value[$field]) === '')) {
                throw new CartFailure('invalid_configuration', 422);
            }
            if ($field === 'note' && array_key_exists($field, $value) && (! is_string($value[$field]) || mb_strlen($value[$field], 'UTF-8') > $max || trim($value[$field]) === '')) {
                throw new CartFailure('invalid_configuration', 422);
            }
        }
        if ($modality === 'physical_personalized') {
            if (! is_string($value['event_date'] ?? null) || ! self::validDate($value['event_date']) || (isset($value['note']) && ! is_string($value['note']))) {
                throw new CartFailure('invalid_configuration', 422);
            }
        } elseif (! in_array($value['invite_type'] ?? null, ['standard', 'complex'], true)
            || ! self::validDate($value['event_date'] ?? null)
            || ! is_string($value['event_time'] ?? null) || preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value['event_time']) !== 1) {
            throw new CartFailure('invalid_configuration', 422);
        }
        $defer = $value['defer_briefing'] ?? null;
        if (! is_bool($defer) || ($defer && ! $allowsDeferred)) {
            throw new CartFailure('invalid_configuration', 422);
        }

        return new self($value);
    }

    /** @param array<string, mixed> $value */
    private function __construct(public array $value) {}

    private static function validDate(mixed $value): bool
    {
        if (! is_string($value) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) !== 1) {
            return false;
        }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value, new \DateTimeZone((string) config('app.timezone', 'UTC')));

        return $date !== false && $date->format('Y-m-d') === $value && $date >= now(config('app.timezone', 'UTC'))->startOfDay();
    }
}
