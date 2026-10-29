<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_products', function (Blueprint $table): void {
            $table->boolean('allows_deferred_briefing')->default(false);
        });

        Schema::create('guest_carts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->char('owner_hash', 64)->unique();
            $table->timestampTz('expires_at')->index();
            $table->timestampsTz();
        });

        Schema::create('guest_cart_sessions', function (Blueprint $table): void {
            $table->string('reference', 43)->primary();
            $table->char('owner_hash', 64)->unique();
            $table->char('csrf_hash', 64);
            $table->char('idempotency_key_hash', 64);
            $table->char('payload_hash', 64)->nullable();
            $table->timestampTz('expires_at')->index();
            $table->timestampsTz();
        });

        Schema::create('cart_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('cart_id')->constrained('guest_carts')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('catalog_products')->restrictOnDelete();
            $table->foreignUuid('model_id')->constrained('catalog_product_models')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->jsonb('configuration');
            $table->char('quote_snapshot_id', 64);
            $table->unsignedBigInteger('pricing_rule_version');
            $table->bigInteger('total_minor');
            $table->string('currency', 3)->default('EUR');
            $table->timestampsTz();
            $table->index(['cart_id', 'created_at']);
        });

        Schema::create('cart_idempotency_keys', function (Blueprint $table): void {
            $table->id();
            $table->char('owner_hash', 64);
            $table->string('idempotency_key', 120);
            $table->char('payload_hash', 64);
            $table->foreignUuid('cart_item_id')->constrained('cart_items')->cascadeOnDelete();
            $table->timestampTz('expires_at')->index();
            $table->timestampsTz();
            $table->unique(['owner_hash', 'idempotency_key']);
        });

        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT cart_items_quantity_check CHECK (quantity BETWEEN 1 AND 10000)');
        DB::statement("ALTER TABLE cart_items ADD CONSTRAINT cart_items_total_check CHECK (total_minor >= 0 AND currency = 'EUR')");
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_cart_sessions');
        Schema::dropIfExists('cart_idempotency_keys');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('guest_carts');
        Schema::table('catalog_products', function (Blueprint $table): void {
            $table->dropColumn('allows_deferred_briefing');
        });
    }
};
