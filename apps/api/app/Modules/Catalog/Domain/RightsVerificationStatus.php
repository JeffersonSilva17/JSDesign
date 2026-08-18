<?php

namespace App\Modules\Catalog\Domain;

enum RightsVerificationStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';
}
