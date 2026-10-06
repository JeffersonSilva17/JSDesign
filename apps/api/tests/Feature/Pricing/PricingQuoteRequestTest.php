<?php

namespace Tests\Feature\Pricing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

final class PricingQuoteRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_extra_fields_with_sanitized_error_and_no_store(): void
    {
        $response = $this->postJson('/api/v1/pricing/quotes', [
            'product_slug' => 'kit-festa', 'quantity' => 2, 'currency' => 'EUR', 'price_minor' => 1,
        ]);
        $response->assertBadRequest()->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonStructure(['error' => ['code', 'message', 'correlation_id']]);
        self::assertEqualsCanonicalizing(['code', 'message', 'correlation_id'], array_keys($response->json('error')));
        self::assertSame('invalid_request', $response->json('error.code'));
    }

    public function test_rejects_fractional_quantities_and_large_bodies(): void
    {
        $this->call('POST', '/api/v1/pricing/quotes', [], [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"product_slug":"kit-festa","quantity":1.5,"currency":"EUR"}')
            ->assertStatus(422)->assertJsonPath('error.code', 'invalid_quantity');
        $this->call('POST', '/api/v1/pricing/quotes', [], [], [], ['CONTENT_TYPE' => 'application/json'], str_repeat(' ', 4097))
            ->assertBadRequest();
    }

    public function test_quote_returns_public_allowlist_and_repeats_deterministically(): void
    {
        [$slug, $ruleId] = $this->publishedProduct();
        DB::table('pricing_quantity_tiers')->insert([
            'id' => (string) Str::uuid(), 'pricing_product_rule_id' => $ruleId, 'minimum_quantity' => 10,
            'maximum_quantity' => 20, 'unit_price_minor' => 450, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $input = ['product_slug' => $slug, 'model_key' => 'premium', 'quantity' => 10, 'currency' => 'EUR'];
        $first = $this->postJson('/api/v1/pricing/quotes', $input)->assertOk()->assertHeader('Cache-Control', 'no-store, private');
        $second = $this->postJson('/api/v1/pricing/quotes', $input)->assertOk();
        self::assertSame($first->json(), $second->json());
        self::assertSame(4500, $first->json('total_minor'));
        self::assertSame(500, $first->json('discount_minor'));
        self::assertSame('premium', $first->json('model_key'));
        self::assertEqualsCanonicalizing([
            'product_slug', 'model_key', 'quantity', 'minimum_quantity', 'maximum_quantity', 'base_unit_price_minor',
            'unit_price_minor', 'subtotal_minor', 'discount_minor', 'total_minor', 'currency', 'pricing_rule_version', 'applied_tier',
        ], array_keys($first->json()));
        self::assertSame(1, DB::table('pricing_quantity_tiers')->count());
    }

    public function test_unavailable_product_and_invalid_model_currency_are_sanitized(): void
    {
        [$slug] = $this->publishedProduct();
        $input = ['product_slug' => $slug, 'model_key' => 'private-model', 'quantity' => 1, 'currency' => 'EUR'];
        $this->postJson('/api/v1/pricing/quotes', $input)->assertUnprocessable()->assertJsonPath('error.code', 'invalid_model');
        $this->postJson('/api/v1/pricing/quotes', [...$input, 'model_key' => 'premium', 'currency' => 'USD'])
            ->assertUnprocessable()->assertJsonPath('error.code', 'unsupported_currency');
        $this->postJson('/api/v1/pricing/quotes', [...$input, 'product_slug' => 'missing-product', 'model_key' => 'premium'])
            ->assertNotFound()->assertJsonPath('error.code', 'quote_unavailable');
        DB::table('catalog_products')->where('slug', $slug)->update(['status' => 'unpublished', 'unpublished_at' => now()]);
        $this->postJson('/api/v1/pricing/quotes', [...$input, 'model_key' => 'premium'])
            ->assertNotFound()->assertJsonPath('error.code', 'quote_unavailable');
    }

    public function test_products_outside_their_publication_window_share_the_unavailable_error(): void
    {
        [$slug] = $this->publishedProduct();
        $input = ['product_slug' => $slug, 'quantity' => 1, 'currency' => 'EUR'];
        DB::table('catalog_products')->where('slug', $slug)->update(['published_at' => now()->addMinute()]);
        $this->postJson('/api/v1/pricing/quotes', $input)->assertNotFound()->assertJsonPath('error.code', 'quote_unavailable');
        DB::table('catalog_products')->where('slug', $slug)->update(['published_at' => now()->subMinute(), 'unpublished_at' => now()]);
        $this->postJson('/api/v1/pricing/quotes', $input)->assertNotFound()->assertJsonPath('error.code', 'quote_unavailable');
        DB::table('catalog_products')->where('slug', $slug)->update(['unpublished_at' => now()->addMinute(), 'availability' => 'unavailable']);
        $this->postJson('/api/v1/pricing/quotes', $input)->assertNotFound()->assertJsonPath('error.code', 'quote_unavailable');
    }

    public function test_integer_multiplication_overflow_returns_a_sanitized_quantity_error(): void
    {
        [$slug] = $this->publishedProduct();
        DB::table('catalog_products')->where('slug', $slug)->update(['price_minor' => PHP_INT_MAX]);
        $this->postJson('/api/v1/pricing/quotes', ['product_slug' => $slug, 'quantity' => 2, 'currency' => 'EUR'])
            ->assertUnprocessable()->assertJsonPath('error.code', 'invalid_quantity');
    }

    public function test_moving_a_tier_increments_both_pricing_rule_versions(): void
    {
        [, $firstRuleId] = $this->publishedProduct('test-product');
        [, $secondRuleId] = $this->publishedProduct('second-product');
        $tierId = (string) Str::uuid();
        DB::table('pricing_quantity_tiers')->insert([
            'id' => $tierId, 'pricing_product_rule_id' => $firstRuleId, 'minimum_quantity' => 5,
            'maximum_quantity' => 10, 'unit_price_minor' => 400, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $firstBefore = (int) DB::table('pricing_product_rules')->where('id', $firstRuleId)->value('version');
        $secondBefore = (int) DB::table('pricing_product_rules')->where('id', $secondRuleId)->value('version');

        DB::table('pricing_quantity_tiers')->where('id', $tierId)->update(['pricing_product_rule_id' => $secondRuleId]);

        self::assertGreaterThan($firstBefore, (int) DB::table('pricing_product_rules')->where('id', $firstRuleId)->value('version'));
        self::assertGreaterThan($secondBefore, (int) DB::table('pricing_product_rules')->where('id', $secondRuleId)->value('version'));
    }

    public function test_digital_ready_product_accepts_only_one_unit_over_http(): void
    {
        [$slug] = $this->publishedProduct();
        DB::table('catalog_products')->where('slug', $slug)->update(['modality' => 'digital_ready']);

        $this->postJson('/api/v1/pricing/quotes', ['product_slug' => $slug, 'quantity' => 1, 'currency' => 'EUR'])->assertOk();
        $this->postJson('/api/v1/pricing/quotes', ['product_slug' => $slug, 'quantity' => 2, 'currency' => 'EUR'])
            ->assertUnprocessable()->assertJsonPath('error.code', 'invalid_quantity');
    }

    public function test_public_quote_rate_limit_is_sixty_requests_per_minute_per_ip(): void
    {
        [$slug] = $this->publishedProduct();
        $ip = '198.51.100.25';
        RateLimiter::clear('public-pricing-quote:'.hash('sha256', $ip));
        for ($request = 0; $request < 60; $request++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->postJson('/api/v1/pricing/quotes', ['product_slug' => $slug, 'quantity' => 1, 'currency' => 'EUR'])
                ->assertOk();
        }
        $limited = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson('/api/v1/pricing/quotes', ['product_slug' => $slug, 'quantity' => 1, 'currency' => 'EUR']);
        $limited->assertTooManyRequests()->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('error.code', 'rate_limited');
    }

    /** @return array{string,string} */
    private function publishedProduct(string $slug = 'test-product'): array
    {
        $now = now();
        $categoryId = (string) Str::uuid();
        $productId = (string) Str::uuid();
        DB::table('catalog_categories')->insert(['id' => $categoryId, 'slug' => $slug.'-category', 'label' => 'Test', 'created_at' => $now, 'updated_at' => $now]);
        DB::table('catalog_products')->insert([
            'id' => $productId, 'slug' => $slug, 'name' => 'Test product', 'modality' => 'physical_personalized',
            'status' => 'published', 'category_id' => $categoryId, 'price_minor' => 500, 'currency' => 'EUR',
            'availability' => 'available', 'minimum_quantity' => 1, 'published_at' => $now->copy()->subMinute(),
            'version' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('catalog_product_models')->insert([
            ['id' => (string) Str::uuid(), 'product_id' => $productId, 'public_key' => 'premium', 'label' => 'Premium', 'difference' => 'Premium', 'sort_order' => 1, 'is_default' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
        $ruleId = (string) DB::table('pricing_product_rules')->where('product_id', $productId)->value('id');

        return [$slug, $ruleId];
    }
}
