<?php

namespace App\Modules\Catalog\Domain;

enum Availability: string
{
    case Available = 'available';
    case Unavailable = 'unavailable';
    case MadeToOrder = 'made_to_order';
}
