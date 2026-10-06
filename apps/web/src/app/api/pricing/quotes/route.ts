import { NextResponse } from 'next/server';

import { createQuote } from '@/bff/pricingApi';
import { localError, parseQuoteInput, readLimitedJsonBody } from '@/bff/pricingValidation';

export async function POST(request: Request): Promise<NextResponse> {
  const language = request.headers.get('accept-language')?.split(',')[0]?.trim().toLowerCase();
  const locale = language === 'en' || language?.startsWith('en-') ? 'en' : language === 'es' || language?.startsWith('es-') ? 'es' : 'pt-BR';
  const invalid = (code: 'invalid_request' | 'invalid_quantity', status: number) => NextResponse.json(localError(code, locale), { status, headers: { 'cache-control': 'no-store' } });
  if (request.headers.get('content-type')?.split(';', 1)[0].trim().toLowerCase() !== 'application/json') return invalid('invalid_request', 400);
  const length = Number(request.headers.get('content-length') ?? '0');
  if (length > 4096) return invalid('invalid_request', 400);
  const raw = await readLimitedJsonBody(request.body, 4096);
  if (raw === null) return invalid('invalid_request', 400);
  let payload: unknown;
  try { payload = JSON.parse(raw) as unknown; } catch { return invalid('invalid_request', 400); }
  const parsed = parseQuoteInput(payload);
  if (!parsed.input) {
    const code = parsed.error ?? 'invalid_request';
    const status = code === 'invalid_request' ? 400 : 422;
    return NextResponse.json(localError(code, locale), { status, headers: { 'cache-control': 'no-store' } });
  }
  const result = await createQuote(parsed.input, fetch, locale);
  return NextResponse.json(result.payload, { status: result.status, headers: { 'cache-control': 'no-store' } });
}
