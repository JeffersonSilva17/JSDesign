<?php

return [
    'rate_limit_per_minute' => (int) env('CART_RATE_LIMIT_PER_MINUTE', 10),
    'read_rate_limit_per_minute' => (int) env('CART_READ_RATE_LIMIT_PER_MINUTE', 60),
    'max_items' => (int) env('CART_MAX_ITEMS', 50),
];
