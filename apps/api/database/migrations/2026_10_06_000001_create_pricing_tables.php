<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_product_rules', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->unique()->constrained('catalog_products')->cascadeOnDelete();
            $table->unsignedInteger('maximum_quantity')->nullable();
            $table->unsignedBigInteger('version')->default(1);
            $table->timestampsTz();
        });
        Schema::create('pricing_quantity_tiers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('pricing_product_rule_id')->constrained('pricing_product_rules')->cascadeOnDelete();
            $table->unsignedInteger('minimum_quantity');
            $table->unsignedInteger('maximum_quantity')->nullable();
            $table->bigInteger('unit_price_minor');
            $table->timestampsTz();
            $table->index(['pricing_product_rule_id', 'minimum_quantity'], 'pricing_tiers_rule_minimum_idx');
        });
        DB::statement('ALTER TABLE pricing_product_rules ADD CONSTRAINT pricing_product_rules_maximum_check CHECK (maximum_quantity IS NULL OR maximum_quantity > 0)');
        DB::statement('ALTER TABLE pricing_product_rules ADD CONSTRAINT pricing_product_rules_version_check CHECK (version > 0)');
        DB::statement('ALTER TABLE pricing_quantity_tiers ADD CONSTRAINT pricing_tiers_range_check CHECK (minimum_quantity > 0 AND (maximum_quantity IS NULL OR maximum_quantity >= minimum_quantity))');
        DB::statement('ALTER TABLE pricing_quantity_tiers ADD CONSTRAINT pricing_tiers_price_check CHECK (unit_price_minor >= 0)');
        DB::statement('INSERT INTO pricing_product_rules (id, product_id, maximum_quantity, version, created_at, updated_at) SELECT gen_random_uuid(), id, NULL, version, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM catalog_products');
        DB::statement('CREATE OR REPLACE FUNCTION pricing_create_product_rule() RETURNS trigger LANGUAGE plpgsql AS $$ BEGIN INSERT INTO pricing_product_rules (id, product_id, maximum_quantity, version, created_at, updated_at) VALUES (gen_random_uuid(), NEW.id, NULL, NEW.version, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP); RETURN NEW; END $$');
        DB::statement('CREATE TRIGGER catalog_product_create_pricing_rule AFTER INSERT ON catalog_products FOR EACH ROW EXECUTE FUNCTION pricing_create_product_rule()');
        DB::statement('CREATE OR REPLACE FUNCTION pricing_bump_rule_version_for_product() RETURNS trigger LANGUAGE plpgsql AS $$ BEGIN UPDATE pricing_product_rules SET version = GREATEST(version, NEW.version) + 1, updated_at = CURRENT_TIMESTAMP WHERE product_id = NEW.id; RETURN NEW; END $$');
        DB::statement('CREATE TRIGGER catalog_product_pricing_version AFTER UPDATE OF price_minor, minimum_quantity, modality, availability, status, published_at, unpublished_at ON catalog_products FOR EACH ROW WHEN (OLD.price_minor IS DISTINCT FROM NEW.price_minor OR OLD.minimum_quantity IS DISTINCT FROM NEW.minimum_quantity OR OLD.modality IS DISTINCT FROM NEW.modality OR OLD.availability IS DISTINCT FROM NEW.availability OR OLD.status IS DISTINCT FROM NEW.status OR OLD.published_at IS DISTINCT FROM NEW.published_at OR OLD.unpublished_at IS DISTINCT FROM NEW.unpublished_at) EXECUTE FUNCTION pricing_bump_rule_version_for_product()');
        DB::statement(<<<'SQL'
            CREATE OR REPLACE FUNCTION pricing_bump_rule_version_for_tier() RETURNS trigger LANGUAGE plpgsql AS $$
            BEGIN
                IF TG_OP = 'DELETE' THEN
                    UPDATE pricing_product_rules r
                    SET version = GREATEST(r.version, (SELECT p.version FROM catalog_products p WHERE p.id = r.product_id)) + 1,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE r.id = OLD.pricing_product_rule_id;
                    RETURN OLD;
                END IF;
                UPDATE pricing_product_rules r
                SET version = GREATEST(r.version, (SELECT p.version FROM catalog_products p WHERE p.id = r.product_id)) + 1,
                    updated_at = CURRENT_TIMESTAMP
                WHERE r.id = NEW.pricing_product_rule_id;
                IF TG_OP = 'UPDATE' AND OLD.pricing_product_rule_id IS DISTINCT FROM NEW.pricing_product_rule_id THEN
                    UPDATE pricing_product_rules r
                    SET version = GREATEST(r.version, (SELECT p.version FROM catalog_products p WHERE p.id = r.product_id)) + 1,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE r.id = OLD.pricing_product_rule_id;
                END IF;
                RETURN NEW;
            END $$
            SQL);
        DB::statement('CREATE TRIGGER pricing_tier_version_insert_update_delete AFTER INSERT OR UPDATE OR DELETE ON pricing_quantity_tiers FOR EACH ROW EXECUTE FUNCTION pricing_bump_rule_version_for_tier()');
        DB::statement('CREATE OR REPLACE FUNCTION pricing_bump_rule_version_for_maximum() RETURNS trigger LANGUAGE plpgsql AS $$ BEGIN IF OLD.maximum_quantity IS DISTINCT FROM NEW.maximum_quantity THEN NEW.version := GREATEST(OLD.version, (SELECT p.version FROM catalog_products p WHERE p.id = NEW.product_id)) + 1; END IF; RETURN NEW; END $$');
        DB::statement('CREATE TRIGGER pricing_rule_maximum_version BEFORE UPDATE OF maximum_quantity ON pricing_product_rules FOR EACH ROW EXECUTE FUNCTION pricing_bump_rule_version_for_maximum()');
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS pricing_tier_version_insert_update_delete ON pricing_quantity_tiers');
        DB::statement('DROP FUNCTION IF EXISTS pricing_bump_rule_version_for_tier()');
        DB::statement('DROP TRIGGER IF EXISTS pricing_rule_maximum_version ON pricing_product_rules');
        DB::statement('DROP FUNCTION IF EXISTS pricing_bump_rule_version_for_maximum()');
        DB::statement('DROP TRIGGER IF EXISTS catalog_product_pricing_version ON catalog_products');
        DB::statement('DROP FUNCTION IF EXISTS pricing_bump_rule_version_for_product()');
        DB::statement('DROP TRIGGER IF EXISTS catalog_product_create_pricing_rule ON catalog_products');
        DB::statement('DROP FUNCTION IF EXISTS pricing_create_product_rule()');
        Schema::dropIfExists('pricing_quantity_tiers');
        Schema::dropIfExists('pricing_product_rules');
    }
};
