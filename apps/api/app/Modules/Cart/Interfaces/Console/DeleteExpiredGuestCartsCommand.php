<?php

namespace App\Modules\Cart\Interfaces\Console;

use App\Modules\Cart\Application\DeleteExpiredGuestCarts;
use Illuminate\Console\Command;

final class DeleteExpiredGuestCartsCommand extends Command
{
    protected $signature = 'cart:purge-expired';

    protected $description = 'Remove carrinhos e configurações abandonados após a retenção configurada.';

    public function handle(DeleteExpiredGuestCarts $purge): int
    {
        $purge->execute();
        $this->info('Expired guest cart data removed.');

        return self::SUCCESS;
    }
}
