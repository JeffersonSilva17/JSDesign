<?php

namespace App\Modules\Catalog\Application\Security;

interface AdminIdentityResolver
{
    public function resolve(): ?AdminIdentity;
}
