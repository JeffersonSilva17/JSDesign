<?php

namespace App\Modules\Catalog\Domain;

enum DeliveryType: string
{
    case Physical = 'physical';
    case Digital = 'digital';
}
