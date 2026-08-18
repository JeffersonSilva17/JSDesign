<?php

namespace Tests\Unit\Modules\Catalog;

use App\Modules\Catalog\Domain\Money;
use App\Modules\Catalog\Domain\Product;
use App\Modules\Catalog\Domain\ProductModality;
use App\Modules\Catalog\Domain\RightsVerificationStatus;
use App\Modules\Catalog\Domain\TaxonomyTerm;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CatalogDomainTest extends TestCase
{
    public function test_money_uses_non_negative_minor_units_and_iso_currency(): void
    {
        self::assertSame(1299, (new Money(1299, 'eur'))->minor);
        self::assertSame('EUR', (new Money(1299, 'eur'))->currency);

        $this->expectException(InvalidArgumentException::class);
        new Money(-1, 'EUR');
    }

    public function test_money_rejects_unapproved_three_letter_codes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Money(100, 'XYZ');
    }

    public function test_product_rejects_malformed_price_minor_without_http(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Product::draft($this->baseProduct(['price_minor' => '12abc']));
    }

    public function test_taxonomy_normalizes_case_whitespace_and_accents_but_preserves_label(): void
    {
        $term = TaxonomyTerm::fromLabel("  Festa   de Cora\u{00E7}\u{00E3}o  ", 'theme');

        self::assertSame("Festa de Cora\u{00E7}\u{00E3}o", $term->label);
        self::assertSame('festa de coracao', $term->canonicalKey);
        self::assertSame(
            $term->canonicalKey,
            TaxonomyTerm::fromLabel("FESTA DE CORAC\u{0327}A\u{0303}O", 'theme')->canonicalKey,
        );
    }

    public function test_supported_modalities_are_closed(): void
    {
        self::assertSame(ProductModality::DigitalReady, ProductModality::from('digital_ready'));

        $this->expectException(\ValueError::class);
        ProductModality::from('service');
    }

    public function test_domain_rejects_invalid_delivery_and_availability_even_without_http(): void
    {
        $this->expectException(\ValueError::class);
        Product::draft($this->baseProduct(['delivery_type' => 'teleport']));
    }

    public function test_physical_personalized_publication_reports_conditional_fields(): void
    {
        $product = Product::draft($this->baseProduct([
            'modality' => 'physical_personalized',
            'delivery_type' => 'physical',
            'is_personalized' => true,
            'minimum_quantity' => null,
            'variants_reference' => null,
            'materials' => null,
            'composition' => null,
            'production_lead_time_days' => null,
        ]));

        $paths = array_column($product->publicationErrors(), 'field_path');

        self::assertContains('minimum_quantity', $paths);
        self::assertContains('materials', $paths);
        self::assertContains('composition', $paths);
        self::assertContains('production_lead_time_days', $paths);
    }

    public function test_digital_ready_rejects_personalization_briefing_and_non_immediate_delivery(): void
    {
        $product = Product::draft($this->baseProduct([
            'modality' => 'digital_ready',
            'delivery_type' => 'digital',
            'is_personalized' => true,
            'is_immediate_delivery' => false,
            'requires_briefing' => true,
            'requires_approval' => true,
            'file_description' => null,
            'compatibility' => null,
            'usage_terms' => null,
        ]));

        $paths = array_column($product->publicationErrors(), 'field_path');

        self::assertContains('is_personalized', $paths);
        self::assertContains('is_immediate_delivery', $paths);
        self::assertContains('requires_briefing', $paths);
        self::assertContains('requires_approval', $paths);
        self::assertContains('file_description', $paths);
        self::assertContains('compatibility', $paths);
        self::assertContains('usage_terms', $paths);
    }

    public function test_publication_rejects_whitespace_only_required_text(): void
    {
        $product = Product::draft($this->baseProduct([
            'name' => '   ',
            'file_description' => "\t",
        ]));

        $paths = array_column($product->publicationErrors(), 'field_path');

        self::assertContains('name', $paths);
        self::assertContains('file_description', $paths);
    }

    public function test_protected_assets_must_have_verified_rights(): void
    {
        $product = Product::draft($this->baseProduct([
            'protected_assets' => [[
                'label' => 'Personagem X',
                'status' => RightsVerificationStatus::Pending->value,
            ]],
        ]));

        self::assertContains('protected_assets.0.status', array_column($product->publicationErrors(), 'field_path'));
    }

    public function test_digital_personalized_requires_creation_time_and_forbids_immediate_download(): void
    {
        $product = Product::draft($this->baseProduct([
            'modality' => 'digital_personalized',
            'is_personalized' => true,
            'is_immediate_delivery' => true,
            'production_lead_time_days' => null,
        ]));

        $paths = array_column($product->publicationErrors(), 'field_path');
        self::assertContains('production_lead_time_days', $paths);
        self::assertContains('is_immediate_delivery', $paths);
    }

    /** @param array<string, mixed> $overrides */
    private function baseProduct(array $overrides = []): array
    {
        return array_replace([
            'id' => '019bf1c0-bd64-7000-8000-000000000001',
            'slug' => 'convite-fazendinha',
            'name' => 'Convite Fazendinha',
            'description' => 'Convite digital pronto para celebrar.',
            'modality' => 'digital_ready',
            'status' => 'draft',
            'category_id' => '019bf1c0-bd64-7000-8000-000000000002',
            'price_minor' => 1299,
            'currency' => 'EUR',
            'availability' => 'available',
            'delivery_type' => 'digital',
            'is_personalized' => false,
            'is_immediate_delivery' => true,
            'requires_briefing' => false,
            'requires_approval' => false,
            'file_description' => 'PDF e PNG',
            'compatibility' => 'Leitores de PDF',
            'usage_terms' => 'Uso pessoal',
            'images' => [[
                'storage_reference' => 'file_01HXYZABCDEF0123456789ABCD',
                'alt_text' => 'Convite com animais da fazenda',
                'sort_order' => 0,
                'is_primary' => true,
                'validated' => true,
            ]],
            'protected_assets' => [],
            'version' => 1,
        ], $overrides);
    }
}
