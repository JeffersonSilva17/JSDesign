<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_categories', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('slug', 160)->unique();
            $table->string('label', 160);
            $table->timestampsTz();
        });

        Schema::create('catalog_products', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('slug', 180)->unique();
            $table->string('name', 180)->nullable();
            $table->text('description')->nullable();
            $table->string('modality', 32)->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->foreignUuid('category_id')->nullable()->constrained('catalog_categories')->restrictOnDelete();
            $table->bigInteger('price_minor')->nullable();
            $table->char('currency', 3)->nullable();
            $table->string('availability', 24)->nullable();
            $table->string('delivery_type', 16)->nullable();
            $table->unsignedInteger('minimum_quantity')->nullable();
            $table->string('variants_reference', 180)->nullable();
            $table->boolean('is_personalized')->nullable();
            $table->boolean('is_immediate_delivery')->nullable();
            $table->boolean('requires_briefing')->nullable();
            $table->boolean('requires_approval')->nullable();
            $table->unsignedInteger('production_lead_time_days')->nullable();
            $table->text('materials')->nullable();
            $table->text('composition')->nullable();
            $table->text('file_description')->nullable();
            $table->text('compatibility')->nullable();
            $table->text('usage_terms')->nullable();
            $table->unsignedBigInteger('version')->default(1);
            $table->timestampTz('published_at')->nullable();
            $table->timestampTz('unpublished_at')->nullable();
            $table->timestampsTz();
        });

        Schema::create('catalog_taxonomy_terms', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type', 24);
            $table->string('label', 160);
            $table->string('canonical_key', 180);
            $table->timestampsTz();
            $table->unique(['type', 'canonical_key'], 'catalog_taxonomy_type_key_unique');
        });

        Schema::create('catalog_product_taxonomy', function (Blueprint $table): void {
            $table->foreignUuid('product_id')->constrained('catalog_products')->cascadeOnDelete();
            $table->foreignUuid('taxonomy_term_id')->constrained('catalog_taxonomy_terms')->restrictOnDelete();
            $table->boolean('is_protected')->default(false);
            $table->string('verification_status', 16)->nullable();
            $table->text('verification_notes')->nullable();
            $table->string('evidence_reference', 180)->nullable();
            $table->string('verified_by', 180)->nullable();
            $table->timestampTz('verified_at')->nullable();
            $table->timestampsTz();
            $table->primary(['product_id', 'taxonomy_term_id']);
        });

        Schema::create('catalog_product_images', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('catalog_products')->cascadeOnDelete();
            $table->string('storage_reference', 180);
            $table->string('alt_text', 300)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestampsTz();
            $table->unique(['product_id', 'storage_reference'], 'catalog_product_image_reference_unique');
            $table->index(['product_id', 'sort_order']);
        });

        DB::statement("ALTER TABLE catalog_products ADD CONSTRAINT catalog_products_modality_check CHECK (modality IS NULL OR modality IN ('physical_personalized', 'digital_ready', 'digital_personalized'))");
        DB::statement("ALTER TABLE catalog_products ADD CONSTRAINT catalog_products_status_check CHECK (status IN ('draft', 'published', 'unpublished'))");
        DB::statement('ALTER TABLE catalog_products ADD CONSTRAINT catalog_products_price_check CHECK (price_minor IS NULL OR price_minor >= 0)');
        DB::statement("ALTER TABLE catalog_products ADD CONSTRAINT catalog_products_currency_check CHECK (currency IS NULL OR currency = 'EUR')");
        DB::statement("ALTER TABLE catalog_products ADD CONSTRAINT catalog_products_availability_check CHECK (availability IS NULL OR availability IN ('available', 'unavailable', 'made_to_order'))");
        DB::statement("ALTER TABLE catalog_products ADD CONSTRAINT catalog_products_delivery_check CHECK (delivery_type IS NULL OR delivery_type IN ('physical', 'digital'))");
        DB::statement('ALTER TABLE catalog_products ADD CONSTRAINT catalog_products_version_check CHECK (version > 0)');
        DB::statement("ALTER TABLE catalog_taxonomy_terms ADD CONSTRAINT catalog_taxonomy_type_check CHECK (type IN ('theme', 'occasion', 'character', 'search_alias'))");
        DB::statement("ALTER TABLE catalog_product_taxonomy ADD CONSTRAINT catalog_rights_status_check CHECK (verification_status IS NULL OR verification_status IN ('pending', 'verified', 'rejected'))");
        DB::statement("ALTER TABLE catalog_product_taxonomy ADD CONSTRAINT catalog_verified_rights_complete_check CHECK (verification_status <> 'verified' OR (evidence_reference IS NOT NULL AND verified_by IS NOT NULL AND verified_at IS NOT NULL))");
        DB::statement('CREATE UNIQUE INDEX catalog_product_one_primary_image ON catalog_product_images (product_id) WHERE is_primary = true');
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_product_images');
        Schema::dropIfExists('catalog_product_taxonomy');
        Schema::dropIfExists('catalog_taxonomy_terms');
        Schema::dropIfExists('catalog_products');
        Schema::dropIfExists('catalog_categories');
    }
};
