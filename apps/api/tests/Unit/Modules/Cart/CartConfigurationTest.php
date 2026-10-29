<?php

namespace Tests\Unit\Modules\Cart;

use App\Modules\Cart\Domain\CartConfiguration;
use App\Modules\Cart\Domain\CartFailure;
use Tests\TestCase;

final class CartConfigurationTest extends TestCase
{
    public function test_accepts_physical_configuration_with_optional_note(): void
    {
        $configuration = CartConfiguration::validate([
            'configuration_type' => 'physical_personalized', 'theme' => 'Fazendinha',
            'personalization_text' => 'Lia', 'event_date' => now()->addDay()->format('Y-m-d'),
            'thumbnail_choice' => 'none', 'defer_briefing' => false,
        ], 'physical_personalized', false);
        self::assertSame('Lia', $configuration->value['personalization_text']);
    }

    public function test_rejects_missing_fields_extra_fields_oversized_data_and_forbidden_deferral(): void
    {
        $base = ['configuration_type' => 'digital_personalized', 'invite_type' => 'standard', 'theme' => 'Jardim', 'colors' => 'Azul', 'event_name' => 'Lia', 'event_date' => now()->addDay()->format('Y-m-d'), 'event_time' => '16:30', 'event_location' => 'Quinta', 'thumbnail_choice' => 'none'];
        foreach ([
            [...$base, 'extra' => 'value'],
            [...$base, 'theme' => str_repeat('x', 121)],
            [...$base, 'event_date' => '2020-01-01'],
            [...$base, 'defer_briefing' => true],
            array_diff_key($base, ['event_location' => true]),
        ] as $invalid) {
            try {
                CartConfiguration::validate($invalid, 'digital_personalized', false);
                self::fail('Expected invalid configuration.');
            } catch (CartFailure $failure) {
                self::assertSame('invalid_configuration', $failure->publicCode);
            }
        }
    }
}
