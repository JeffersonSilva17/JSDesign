<?php

namespace App\Modules\Catalog\Application\Security;

final readonly class AdminIdentity
{
    /** @param list<string> $permissions */
    public function __construct(public string $id, public array $permissions) {}

    public function can(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }
}
