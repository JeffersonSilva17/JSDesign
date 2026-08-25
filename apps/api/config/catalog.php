<?php

return [
    'public_read_rate_limit_per_minute' => (int) env('CATALOG_PUBLIC_RATE_LIMIT_PER_MINUTE', 240),
    'public_read_statement_timeout_ms' => (int) env('CATALOG_PUBLIC_STATEMENT_TIMEOUT_MS', 3000),
];
