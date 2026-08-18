<?php

namespace App\Modules\Catalog\Infrastructure\Security;

use App\Modules\Catalog\Application\Security\AdminIdentity;
use App\Modules\Catalog\Application\Security\AdminIdentityResolver;

final class FailClosedAdminIdentityResolver implements AdminIdentityResolver
{
    public function resolve(): ?AdminIdentity
    {
        return null;
    }
}
