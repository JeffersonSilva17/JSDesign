import { NextResponse } from 'next/server';

import { fetchApiHealth } from '@/bff/apiClient';

export async function GET(): Promise<NextResponse> {
  try {
    const api = await fetchApiHealth();

    return NextResponse.json({
      status: 'ok',
      service: 'jsdesign-web-bff',
      api,
    });
  } catch {
    return NextResponse.json(
      {
      status: 'degraded',
      service: 'jsdesign-web-bff',
      error: 'Laravel API indisponível ou retornando health inválido.',
    },
    { status: 503 },
  );
  }
}
