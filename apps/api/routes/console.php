<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Modules\Cart\Application\DeleteExpiredGuestCarts;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cart:purge-expired', function (DeleteExpiredGuestCarts $purge): void {
    $purge->execute();
    $this->info('Expired guest cart data removed.');
})->purpose('Remove expired guest cart data');
Schedule::command('cart:purge-expired')->dailyAt('03:15')->withoutOverlapping();
