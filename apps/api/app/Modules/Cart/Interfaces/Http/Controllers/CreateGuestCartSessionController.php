<?php

namespace App\Modules\Cart\Interfaces\Http\Controllers;

use App\Modules\Cart\Application\GuestCartSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class CreateGuestCartSessionController
{
    public function __construct(private GuestCartSession $sessions) {}

    public function __invoke(Request $request): JsonResponse
    {
        $reference = (string) $request->header('X-Guest-Session', '');

        return response()->json($this->sessions->issue(preg_match('/^[A-Za-z0-9_-]{43}$/', $reference) === 1 ? $reference : null), 201)
            ->header('Cache-Control', 'no-store, private');
    }
}
