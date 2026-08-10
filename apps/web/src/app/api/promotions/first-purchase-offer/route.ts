import { NextResponse } from 'next/server';

import { fetchFirstPurchaseOffer } from '@/bff/apiClient';

export async function GET(): Promise<NextResponse> {
  try {
    const offer = await fetchFirstPurchaseOffer();

    if (
      offer.enabled &&
      offer.delivery_mode === 'email' &&
      process.env.NODE_ENV === 'production' &&
      process.env.TRUSTED_EDGE_FORWARDING_HEADERS !== 'true'
    ) {
      return NextResponse.json(
        { enabled: false },
        { headers: { 'cache-control': 'no-store' } },
      );
    }

    return NextResponse.json(offer, {
      headers: {
        'cache-control': 'no-store',
      },
    });
  } catch {
    return NextResponse.json(
      {
        enabled: false,
      },
      {
        headers: {
          'cache-control': 'no-store',
        },
      },
    );
  }
}
