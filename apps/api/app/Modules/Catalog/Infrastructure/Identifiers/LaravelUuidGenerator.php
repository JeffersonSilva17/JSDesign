<?php

namespace App\Modules\Catalog\Infrastructure\Identifiers;

use App\Modules\Catalog\Application\IdGenerator;
use Illuminate\Support\Str;

final class LaravelUuidGenerator implements IdGenerator
{
    public function generate(): string
    {
        return (string) Str::uuid();
    }
}
