<?php

namespace App\Modules\Catalog\Application;

enum FileReferenceStatus: string
{
    case Accepted = 'accepted';
    case Missing = 'missing';
    case Rejected = 'rejected';
}
