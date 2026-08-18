<?php

namespace App\Modules\Catalog\Domain;

enum PublicationStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Unpublished = 'unpublished';
}
