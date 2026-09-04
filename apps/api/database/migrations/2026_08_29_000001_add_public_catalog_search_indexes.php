<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::unprepared(<<<'SQL'
            DO $$
            DECLARE
                function_oid oid;
            BEGIN
                SELECT p.oid INTO function_oid
                FROM pg_proc p
                JOIN pg_namespace n ON n.oid = p.pronamespace
                WHERE n.nspname = 'public'
                  AND p.proname = 'catalog_public_search_normalize_v1'
                  AND pg_get_function_identity_arguments(p.oid) = 'value text';

                IF function_oid IS NULL THEN
                    EXECUTE $create$
                        CREATE FUNCTION catalog_public_search_normalize_v1(value text)
                        RETURNS text
                        LANGUAGE sql
                        IMMUTABLE
                        PARALLEL SAFE
                        STRICT
                        AS $body$
                            SELECT btrim(regexp_replace(
                                lower(public.unaccent('public.unaccent', normalize(value, NFKC))),
                                '[[:space:]]+',
                                ' ',
                                'g'
                            ))
                        $body$
                    $create$;
                ELSIF coalesce(obj_description(function_oid, 'pg_proc'), '') NOT LIKE 'Owned by Story 2.3 public catalog search.%' THEN
                    RAISE EXCEPTION 'catalog_public_search_normalize_v1(text) exists but is not owned by Story 2.3 public catalog search';
                END IF;
            END;
            $$;
        SQL);
        DB::statement(<<<'SQL'
            COMMENT ON FUNCTION catalog_public_search_normalize_v1(text)
            IS 'Owned by Story 2.3 public catalog search. Depends on public.unaccent rules; REINDEX related catalog search indexes after unaccent dictionary changes.'
        SQL);
        DB::statement('CREATE INDEX catalog_products_search_name_trgm_idx ON catalog_products USING gin (catalog_public_search_normalize_v1(name) gin_trgm_ops)');
        DB::statement('CREATE INDEX catalog_categories_search_label_trgm_idx ON catalog_categories USING gin (catalog_public_search_normalize_v1(label) gin_trgm_ops)');
        DB::statement('CREATE INDEX catalog_taxonomy_search_label_trgm_idx ON catalog_taxonomy_terms USING gin (catalog_public_search_normalize_v1(label) gin_trgm_ops)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS catalog_taxonomy_search_label_trgm_idx');
        DB::statement('DROP INDEX IF EXISTS catalog_categories_search_label_trgm_idx');
        DB::statement('DROP INDEX IF EXISTS catalog_products_search_name_trgm_idx');
        DB::statement('DROP FUNCTION IF EXISTS catalog_public_search_normalize_v1(text)');
    }
};
