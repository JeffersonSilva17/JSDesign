<?php

namespace App\Modules\Catalog\Application;

interface IdGenerator
{
    public function generate(): string;
}
