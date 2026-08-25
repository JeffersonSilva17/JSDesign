<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("CREATE INDEX catalog_products_public_order_idx ON catalog_products (published_at DESC, id DESC) WHERE status = 'published'");
        DB::statement("CREATE INDEX catalog_products_public_category_order_idx ON catalog_products (category_id, published_at DESC, id DESC) WHERE status = 'published'");
        DB::statement("CREATE INDEX catalog_products_public_modality_order_idx ON catalog_products (modality, published_at DESC, id DESC) WHERE status = 'published'");
        DB::statement('CREATE INDEX catalog_product_taxonomy_term_product_idx ON catalog_product_taxonomy (taxonomy_term_id, product_id)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS catalog_product_taxonomy_term_product_idx');
        DB::statement('DROP INDEX IF EXISTS catalog_products_public_modality_order_idx');
        DB::statement('DROP INDEX IF EXISTS catalog_products_public_category_order_idx');
        DB::statement('DROP INDEX IF EXISTS catalog_products_public_order_idx');
    }
};
