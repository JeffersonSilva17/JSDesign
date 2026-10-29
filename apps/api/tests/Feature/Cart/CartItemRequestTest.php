<?php

namespace Tests\Feature\Cart;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class CartItemRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.cart.internal_token' => 'test-cart-service-token',
            'cart.rate_limit_per_minute' => 1000,
            'cart.read_rate_limit_per_minute' => 1000,
        ]);
        $this->flushHeaders();
    }

    public function test_addition_is_server_owned_and_idempotent(): void
    {
        [$slug] = $this->publishedProduct();
        $quote = $this->postJson('/api/v1/pricing/quotes', ['product_slug' => $slug, 'model_key' => 'standard', 'quantity' => 1, 'currency' => 'EUR'])->assertOk()->json();
        $body = [
            'product_slug' => $slug, 'model_key' => 'standard', 'quantity' => 1, 'quote_snapshot_id' => $quote['quote_snapshot_id'],
            'configuration' => ['configuration_type' => 'physical_personalized', 'theme' => 'Jardim', 'personalization_text' => 'Lia', 'event_date' => now()->addDay()->format('Y-m-d'), 'thumbnail_choice' => 'none', 'defer_briefing' => false],
        ];
        $headers = $this->cartHeaders();
        $first = $this->withHeaders($headers)->postJson('/api/v1/cart/items', $body)->assertCreated()->json();
        $replay = $this->withHeaders($headers)->postJson('/api/v1/cart/items', $body)->assertOk()->json();
        self::assertSame($first, $replay);
        self::assertSame(1, DB::table('cart_items')->count());
        self::assertSame(1, DB::table('guest_carts')->count());
        self::assertSame('Lia', $first['configuration']['personalization_text']);
    }

    public function test_rejects_payload_conflict_stale_quote_extra_fields_and_unsupported_mode(): void
    {
        [$slug] = $this->publishedProduct();
        $quote = $this->postJson('/api/v1/pricing/quotes', ['product_slug' => $slug, 'model_key' => 'standard', 'quantity' => 1, 'currency' => 'EUR'])->assertOk()->json();
        $body = ['product_slug' => $slug, 'model_key' => 'standard', 'quantity' => 1, 'quote_snapshot_id' => $quote['quote_snapshot_id'], 'configuration' => [
            'configuration_type' => 'physical_personalized', 'theme' => 'Jardim', 'personalization_text' => 'Lia', 'event_date' => now()->addDay()->format('Y-m-d'), 'thumbnail_choice' => 'none', 'defer_briefing' => false,
        ]];
        $headers = $this->cartHeaders();
        $this->withHeaders($headers)->postJson('/api/v1/cart/items', $body)->assertCreated();
        $this->withHeaders($headers)->postJson('/api/v1/cart/items', [...$body, 'configuration' => [...$body['configuration'], 'theme' => 'Outro']])
            ->assertStatus(409)->assertJsonPath('error.code', 'idempotency_conflict');
        $this->withHeaders($this->cartHeaders($headers['X-Guest-Session']))->postJson('/api/v1/cart/items', [...$body, 'quote_snapshot_id' => str_repeat('0', 64)])
            ->assertStatus(409)->assertJsonPath('error.code', 'stale_quote');
        $this->withHeaders($this->cartHeaders($headers['X-Guest-Session']))->postJson('/api/v1/cart/items', [...$body, 'price_minor' => 1])
            ->assertBadRequest()->assertJsonPath('error.code', 'invalid_request');
        DB::table('catalog_products')->where('slug', $slug)->update(['modality' => 'digital_ready']);
        $this->withHeaders($this->cartHeaders($headers['X-Guest-Session']))->postJson('/api/v1/cart/items', $body)
            ->assertUnprocessable()->assertJsonPath('error.code', 'unsupported_mode');
    }

    public function test_cart_reads_are_isolated_to_the_guest_session(): void
    {
        [$slug] = $this->publishedProduct();
        $quote = $this->postJson('/api/v1/pricing/quotes', ['product_slug' => $slug, 'model_key' => 'standard', 'quantity' => 1, 'currency' => 'EUR'])->assertOk()->json();
        $body = ['product_slug' => $slug, 'model_key' => 'standard', 'quantity' => 1, 'quote_snapshot_id' => $quote['quote_snapshot_id'], 'configuration' => [
            'configuration_type' => 'physical_personalized', 'theme' => 'Jardim', 'personalization_text' => 'Lia', 'event_date' => now()->addDay()->format('Y-m-d'), 'thumbnail_choice' => 'none', 'defer_briefing' => false,
        ]];
        $headersA = $this->cartHeaders();
        $sessionA = $headersA['X-Guest-Session'];
        $this->withHeaders($headersA)->postJson('/api/v1/cart/items', $body)->assertCreated();

        $this->withHeaders(['Authorization' => 'Bearer test-cart-service-token', 'X-Guest-Session' => $sessionA])
            ->getJson('/api/v1/cart/items')->assertOk()->assertJsonCount(1, 'items')->assertJsonPath('items.0.configuration.personalization_text', 'Lia');
        $other = $this->cartHeaders();
        $this->withHeaders(['Authorization' => 'Bearer test-cart-service-token', 'X-Guest-Session' => $other['X-Guest-Session']])
            ->getJson('/api/v1/cart/items')->assertOk()->assertExactJson(['items' => []]);
        $this->withHeaders(['Authorization' => 'Bearer wrong', 'X-Guest-Session' => $sessionA])->getJson('/api/v1/cart/items')->assertStatus(503);
    }

    public function test_invitation_prefill_is_returned_for_the_later_briefing_without_resubmission(): void
    {
        [$slug,$modelId] = $this->publishedProduct();
        DB::table('catalog_products')->where('slug', $slug)->update(['modality' => 'digital_personalized', 'delivery_type' => 'digital']);
        DB::table('catalog_product_models')->where('id', $modelId)->update(['public_key' => 'complex']);
        $quote = $this->postJson('/api/v1/pricing/quotes', ['product_slug' => $slug, 'model_key' => 'complex', 'quantity' => 1, 'currency' => 'EUR'])->assertOk()->json();
        $configuration = [
            'configuration_type' => 'digital_personalized', 'invite_type' => 'complex', 'theme' => 'Jardim', 'colors' => 'Azul e dourado',
            'event_name' => 'Lia', 'event_date' => now()->addDay()->format('Y-m-d'), 'event_time' => '16:30', 'event_location' => 'Quinta',
            'thumbnail_choice' => 'none', 'defer_briefing' => false,
        ];
        $headers = $this->cartHeaders();
        $session = $headers['X-Guest-Session'];
        $this->withHeaders($headers)->postJson('/api/v1/cart/items', [
            'product_slug' => $slug, 'model_key' => 'complex', 'quantity' => 1, 'quote_snapshot_id' => $quote['quote_snapshot_id'], 'configuration' => $configuration,
        ])->assertCreated();
        $this->withHeaders(['Authorization' => 'Bearer test-cart-service-token', 'X-Guest-Session' => $session])->getJson('/api/v1/cart/items')
            ->assertOk()->assertJsonPath('items.0.model_key', 'complex')->assertJsonPath('items.0.configuration.invite_type', 'complex')
            ->assertJsonPath('items.0.configuration.event_name', 'Lia')->assertJsonPath('items.0.configuration.event_location', 'Quinta');
    }

    public function test_expired_guest_cart_and_configured_personal_data_are_purged(): void
    {
        [$slug] = $this->publishedProduct();
        $quote = $this->postJson('/api/v1/pricing/quotes', ['product_slug' => $slug, 'model_key' => 'standard', 'quantity' => 1, 'currency' => 'EUR'])->assertOk()->json();
        $body = ['product_slug' => $slug, 'model_key' => 'standard', 'quantity' => 1, 'quote_snapshot_id' => $quote['quote_snapshot_id'], 'configuration' => [
            'configuration_type' => 'physical_personalized', 'theme' => 'Jardim', 'personalization_text' => 'Lia', 'event_date' => now()->addDay()->format('Y-m-d'), 'thumbnail_choice' => 'none', 'defer_briefing' => false,
        ]];
        $headers = $this->cartHeaders();
        $this->withHeaders($headers)->postJson('/api/v1/cart/items', $body)->assertCreated();
        DB::table('guest_carts')->update(['expires_at' => now()->subSecond()]);
        DB::table('cart_idempotency_keys')->update(['expires_at' => now()->subSecond()]);

        $this->artisan('cart:purge-expired')->assertExitCode(0);
        self::assertSame(0, DB::table('cart_items')->count());
        self::assertSame(0, DB::table('guest_carts')->count());
        self::assertSame(0, DB::table('cart_idempotency_keys')->count());
    }

    public function test_cart_mutation_rejects_non_json_media_types(): void
    {
        $headers = $this->cartHeaders();
        $response = $this->call('POST', '/api/v1/cart/items', [], [], [], [
            'CONTENT_TYPE' => 'application/jsonx', 'HTTP_AUTHORIZATION' => 'Bearer test-cart-service-token',
            'HTTP_X_GUEST_SESSION' => $headers['X-Guest-Session'], 'HTTP_X_CSRF_TOKEN' => $headers['X-CSRF-Token'], 'HTTP_IDEMPOTENCY_KEY' => $headers['Idempotency-Key'],
        ], '{}');
        $response->assertStatus(400)->assertJsonPath('error.code', 'invalid_request');
    }

    /** @return array{string,string} */
    private function publishedProduct(): array
    {
        $now = now();
        $slug = 'personalizado-'.Str::lower(Str::random(6));
        $categoryId = (string) Str::uuid();
        $productId = (string) Str::uuid();
        DB::table('catalog_categories')->insert(['id' => $categoryId, 'slug' => $slug, 'label' => 'Teste', 'created_at' => $now, 'updated_at' => $now]);
        DB::table('catalog_products')->insert([
            'id' => $productId, 'slug' => $slug, 'name' => 'Produto teste', 'description' => 'Produto', 'modality' => 'physical_personalized', 'status' => 'published',
            'category_id' => $categoryId, 'price_minor' => 500, 'currency' => 'EUR', 'availability' => 'available', 'delivery_type' => 'physical',
            'minimum_quantity' => 1, 'published_at' => $now->copy()->subMinute(), 'version' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $modelId = (string) Str::uuid();
        DB::table('catalog_product_models')->insert(['id' => $modelId, 'product_id' => $productId, 'public_key' => 'standard', 'label' => 'Padrão', 'difference' => 'Modelo padrão', 'sort_order' => 1, 'is_default' => true, 'created_at' => $now, 'updated_at' => $now]);

        return [$slug, $modelId];
    }

    /** @return array<string,string> */
    private function cartHeaders(?string $reference = null): array
    {
        $this->flushHeaders();
        $response = $this->withHeaders(array_filter(['Authorization' => 'Bearer test-cart-service-token', 'X-Guest-Session' => $reference]))
            ->postJson('/api/v1/cart/sessions')->assertCreated()->json();
        $this->flushHeaders();

        return ['Authorization' => 'Bearer test-cart-service-token', 'X-Guest-Session' => $response['reference'], 'X-CSRF-Token' => $response['csrf_token'], 'Idempotency-Key' => $response['idempotency_key']];
    }
}
