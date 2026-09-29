<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_product_models', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('catalog_products')->cascadeOnDelete();
            $table->string('public_key', 120);
            $table->string('label', 120);
            $table->string('difference', 360);
            $table->string('image_reference', 180)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestampsTz();
            $table->unique(['product_id', 'public_key'], 'catalog_product_models_product_key_unique');
            $table->index(['product_id', 'sort_order', 'id'], 'catalog_product_models_public_order_idx');
        });

        DB::statement("ALTER TABLE catalog_product_models ADD CONSTRAINT catalog_product_models_public_key_check CHECK (public_key ~ '^[a-z0-9]+(?:-[a-z0-9]+)*$')");
        DB::statement("ALTER TABLE catalog_product_models ADD CONSTRAINT catalog_product_models_label_check CHECK (btrim(label) <> '')");
        DB::statement("ALTER TABLE catalog_product_models ADD CONSTRAINT catalog_product_models_difference_check CHECK (btrim(difference) <> '')");
        DB::statement('CREATE UNIQUE INDEX catalog_product_models_one_default ON catalog_product_models (product_id) WHERE is_default = true');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS catalog_product_models_one_default');
        Schema::dropIfExists('catalog_product_models');
    }
};
