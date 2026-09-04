<?php

use App\Modules\Catalog\Infrastructure\Config\CatalogSearchConfiguration;

return [
    'public_read_rate_limit_per_minute' => (int) env('CATALOG_PUBLIC_RATE_LIMIT_PER_MINUTE', 240),
    'public_read_statement_timeout_ms' => (int) env('CATALOG_PUBLIC_STATEMENT_TIMEOUT_MS', 3000),
    'search_similarity_threshold' => CatalogSearchConfiguration::decimal(env('CATALOG_SEARCH_SIMILARITY_THRESHOLD'), 0.30, 0.20, 0.80),
    'search_candidate_limit' => CatalogSearchConfiguration::integer(env('CATALOG_SEARCH_CANDIDATE_LIMIT'), 500, 50, 2000),
    'search_rate_limit_per_minute' => CatalogSearchConfiguration::integer(env('CATALOG_SEARCH_RATE_LIMIT_PER_MINUTE'), 60, 10, 120),
    'search_statement_timeout_ms' => CatalogSearchConfiguration::integer(env('CATALOG_SEARCH_STATEMENT_TIMEOUT_MS'), 3000, 100, 5000),
    'search_invitation_vocabulary' => ['convite', 'convites', 'convite digital'],
];
