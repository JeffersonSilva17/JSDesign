<?php

namespace App\Modules\Catalog\Domain;

enum ProductModality: string
{
    case PhysicalPersonalized = 'physical_personalized';
    case DigitalReady = 'digital_ready';
    case DigitalPersonalized = 'digital_personalized';
}
