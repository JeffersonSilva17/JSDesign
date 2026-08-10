import { NextResponse } from 'next/server';

import { requestFirstPurchaseCoupon } from '@/bff/apiClient';

function isRequestPayload(payload: unknown): payload is {
  email: string;
  authorization_accepted: true;
  authorization_text_version: string;
  offer_id: string;
} {
  if (!payload || typeof payload !== 'object') {
    return false;
  }

  const candidate = payload as Record<string, unknown>;

  return (
    typeof candidate.email === 'string' &&
    candidate.authorization_accepted === true &&
    typeof candidate.authorization_text_version === 'string' &&
    typeof candidate.offer_id === 'string'
  );
}

export async function POST(request: Request): Promise<NextResponse> {
  let payload: unknown;

  try {
    payload = await request.json();
  } catch {
    return NextResponse.json(
      {
        status: 'unavailable',
        delivery: 'none',
        message: 'Payload inválido.',
      },
      { status: 400 },
    );
  }

  if (!isRequestPayload(payload)) {
    return NextResponse.json(
      {
        status: 'unavailable',
        delivery: 'none',
        message: 'Payload inválido.',
      },
      { status: 400 },
    );
  }

  try {
    const upstream = await requestFirstPurchaseCoupon({
      email: payload.email,
      authorizationTextVersion: payload.authorization_text_version,
      offerId: payload.offer_id,
      forwardingHeaders: trustedForwardingHeaders(request),
    });

    return NextResponse.json(upstream.payload, {
      status: upstream.status,
      headers: {
        'cache-control': 'no-store',
      },
    });
  } catch {
    return NextResponse.json(
      {
        status: 'unavailable',
        delivery: 'none',
        message: 'A solicitação de cupom está temporariamente indisponível.',
      },
      { status: 503 },
    );
  }
}

function trustedForwardingHeaders(request: Request): Record<string, string> {
  if (process.env.TRUSTED_EDGE_FORWARDING_HEADERS !== 'true') {
    return {};
  }

  const forwardedHeaders: Record<string, string> = {};

  for (const name of [
    'forwarded',
    'x-forwarded-for',
    'x-forwarded-host',
    'x-forwarded-port',
    'x-forwarded-proto',
  ]) {
    const value = request.headers.get(name)?.trim();

    if (value && value.length <= 1024 && !/[\r\n]/.test(value)) {
      forwardedHeaders[name] = value;
    }
  }

  return forwardedHeaders;
}
