<?php

namespace App\Modules\Pricing\Application;

interface QuoteProduct
{
    /** @param array{product_slug:string,model_key?:string,quantity:int,currency:string} $input @return array<string,mixed>|null */
    public function findSnapshot(array $input): ?array;
}
