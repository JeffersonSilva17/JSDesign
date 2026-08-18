<?php

namespace App\Modules\Catalog\Domain;

final class Product
{
    /** @param array<string, mixed> $attributes */
    private function __construct(private array $attributes) {}

    /** @param array<string, mixed> $attributes */
    public static function draft(array $attributes): self
    {
        $attributes['status'] = $attributes['status'] ?? PublicationStatus::Draft->value;
        $attributes['version'] = (int) ($attributes['version'] ?? 1);

        if (isset($attributes['price_minor'], $attributes['currency'])) {
            if (! is_int($attributes['price_minor'])) {
                throw new \InvalidArgumentException('O valor monetario deve usar unidades menores inteiras.');
            }

            $money = new Money((int) $attributes['price_minor'], (string) $attributes['currency']);
            $attributes['price_minor'] = $money->minor;
            $attributes['currency'] = $money->currency;
        }

        if (isset($attributes['modality'])) {
            ProductModality::from((string) $attributes['modality']);
        }

        if (isset($attributes['status'])) {
            PublicationStatus::from((string) $attributes['status']);
        }

        if (isset($attributes['availability'])) {
            Availability::from((string) $attributes['availability']);
        }

        if (isset($attributes['delivery_type'])) {
            DeliveryType::from((string) $attributes['delivery_type']);
        }

        foreach ($attributes['protected_assets'] ?? [] as $asset) {
            if (isset($asset['status'])) {
                RightsVerificationStatus::from((string) $asset['status']);
            }
        }

        return new self($attributes);
    }

    /** @return array<string, mixed> */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /** @return list<array{code: string, message_key: string, field_path: string, recoverable: bool, next_action?: string}> */
    public function publicationErrors(): array
    {
        $errors = [];
        $required = [
            'name', 'description', 'modality', 'category_id', 'price_minor', 'currency',
            'availability', 'delivery_type',
        ];

        foreach ($required as $field) {
            if (! array_key_exists($field, $this->attributes) || $this->isBlank($this->attributes[$field])) {
                $errors[] = $this->error('required_for_publication', $field);
            }
        }

        $primaryImages = [];
        foreach ($this->attributes['images'] ?? [] as $index => $image) {
            if (($image['validated'] ?? false) !== true) {
                $errors[] = $this->error('storage_reference_not_validated', "images.$index.storage_reference");
            }

            if (($image['is_primary'] ?? false) === true) {
                $primaryImages[$index] = $image;
            }
        }

        if (count($primaryImages) !== 1) {
            $errors[] = $this->error('exactly_one_primary_image', 'images');
        } else {
            $primaryIndex = array_key_first($primaryImages);
            if (trim((string) ($primaryImages[$primaryIndex]['alt_text'] ?? '')) === '') {
                $errors[] = $this->error('primary_image_alt_required', "images.$primaryIndex.alt_text");
            }
        }

        foreach ($this->attributes['protected_assets'] ?? [] as $index => $asset) {
            if (($asset['status'] ?? null) !== RightsVerificationStatus::Verified->value) {
                $errors[] = $this->error(
                    'commercial_rights_not_verified',
                    "protected_assets.$index.status",
                    'verify_commercial_rights',
                );
            } elseif (empty($asset['evidence_reference']) || empty($asset['verified_by']) || empty($asset['verified_at'])) {
                $errors[] = $this->error(
                    'commercial_rights_verification_incomplete',
                    "protected_assets.$index.evidence_reference",
                    'complete_commercial_rights_verification',
                );
            }
        }

        if (! isset($this->attributes['modality'])) {
            return $errors;
        }

        return [...$errors, ...$this->modalityErrors(ProductModality::from((string) $this->attributes['modality']))];
    }

    public function publish(): self
    {
        if (($this->attributes['status'] ?? null) === PublicationStatus::Published->value) {
            throw new CatalogConflict('already_published', 'Produto ja publicado.');
        }

        $errors = $this->publicationErrors();

        if ($errors !== []) {
            throw new CatalogValidationFailed($errors);
        }

        $copy = $this->attributes;
        $copy['status'] = PublicationStatus::Published->value;

        return new self($copy);
    }

    public function unpublish(): self
    {
        if (($this->attributes['status'] ?? null) !== PublicationStatus::Published->value) {
            throw new CatalogConflict('not_published', 'Apenas produtos publicados podem ser retirados de publicacao.');
        }

        $copy = $this->attributes;
        $copy['status'] = PublicationStatus::Unpublished->value;

        return new self($copy);
    }

    /** @return list<array{code: string, message_key: string, field_path: string, recoverable: bool, next_action?: string}> */
    private function modalityErrors(ProductModality $modality): array
    {
        $errors = [];

        if ($modality === ProductModality::PhysicalPersonalized) {
            if (empty($this->attributes['minimum_quantity']) && empty($this->attributes['variants_reference'])) {
                $errors[] = $this->error('minimum_quantity_or_variants_required', 'minimum_quantity');
            }
            foreach (['materials', 'composition', 'production_lead_time_days'] as $field) {
                if ($this->isBlank($this->attributes[$field] ?? null)) {
                    $errors[] = $this->error('required_for_physical_personalized', $field);
                }
            }
            if (($this->attributes['is_personalized'] ?? null) !== true) {
                $errors[] = $this->error('must_be_personalized', 'is_personalized');
            }
            if (($this->attributes['delivery_type'] ?? null) !== DeliveryType::Physical->value) {
                $errors[] = $this->error('physical_delivery_required', 'delivery_type');
            }
        }

        if ($modality === ProductModality::DigitalPersonalized) {
            if (($this->attributes['is_personalized'] ?? null) !== true) {
                $errors[] = $this->error('must_be_personalized', 'is_personalized');
            }
            if (empty($this->attributes['production_lead_time_days'])) {
                $errors[] = $this->error('creation_lead_time_required', 'production_lead_time_days');
            }
            if (($this->attributes['delivery_type'] ?? null) !== DeliveryType::Digital->value) {
                $errors[] = $this->error('digital_delivery_required', 'delivery_type');
            }
            if (($this->attributes['is_immediate_delivery'] ?? false) === true) {
                $errors[] = $this->error('immediate_delivery_forbidden', 'is_immediate_delivery');
            }
        }

        if ($modality === ProductModality::DigitalReady) {
            $expected = [
                'is_personalized' => false,
                'is_immediate_delivery' => true,
                'requires_briefing' => false,
                'requires_approval' => false,
            ];
            foreach ($expected as $field => $value) {
                if (($this->attributes[$field] ?? null) !== $value) {
                    $errors[] = $this->error('invalid_for_digital_ready', $field);
                }
            }
            foreach (['file_description', 'compatibility', 'usage_terms'] as $field) {
                if ($this->isBlank($this->attributes[$field] ?? null)) {
                    $errors[] = $this->error('required_for_digital_ready', $field);
                }
            }
            if (($this->attributes['delivery_type'] ?? null) !== DeliveryType::Digital->value) {
                $errors[] = $this->error('digital_delivery_required', 'delivery_type');
            }
        }

        return $errors;
    }

    private function isBlank(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        return $value === '';
    }

    /** @return array{code: string, message_key: string, field_path: string, recoverable: bool, next_action?: string} */
    private function error(string $code, string $field, ?string $nextAction = null): array
    {
        $error = [
            'code' => $code,
            'message_key' => "catalog.validation.$code",
            'field_path' => $field,
            'recoverable' => true,
        ];

        if ($nextAction !== null) {
            $error['next_action'] = $nextAction;
        }

        return $error;
    }
}
