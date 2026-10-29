import { NextResponse } from 'next/server';
import { getApiInternalUrl } from '@/bff/apiClient';
import { addCartItem, readCart } from '@/bff/cartTransport';
import { cartMessages, parseCartInput, readLimitedJsonBody } from '@/bff/cartValidation';
import type { CartErrorCode } from '@/bff/cartValidation';

const sessionName = '__Host-jsd_cart_session';
const csrfName = '__Host-jsd_cart_csrf';

export async function GET(request: Request): Promise<NextResponse> {
  const locale = selectedLocale(request.headers.get('accept-language'));
  const cookies = parseCookies(request.headers.get('cookie') ?? '');
  const session = cookies[sessionName] ?? '';
  const token = process.env.API_CART_SERVICE_TOKEN ?? '';
  if (!/^[A-Za-z0-9_-]{43}$/.test(session)) return NextResponse.json({ items: [] }, { headers: { 'cache-control': 'no-store, private' } });
  if (token === '') return error('temporarily_unavailable', 503, locale);
  const items = await readCart(session, token, getApiInternalUrl(), trustedForwardingHeaders(request));
  if (items === null) return error('temporarily_unavailable', 503, locale);
  return NextResponse.json({ items }, { headers: { 'cache-control': 'no-store, private' } });
}

export async function POST(request: Request): Promise<NextResponse> {
  const locale = selectedLocale(request.headers.get('accept-language'));
  const origin = new URL(request.url).origin;
  let source = request.headers.get('origin') ?? '';
  if (!source && request.headers.get('referer')) {
    try { source = new URL(request.headers.get('referer') as string).origin; } catch { return error('csrf_failed', 419, locale); }
  }
  const cookies = parseCookies(request.headers.get('cookie') ?? '');
  if (!source || source !== origin || !cookies[sessionName] || !cookies[csrfName] || request.headers.get('x-csrf-token') !== cookies[csrfName]) {
    return error('csrf_failed', 419, locale);
  }
  if (request.headers.get('content-type')?.split(';', 1)[0].trim().toLowerCase() !== 'application/json') return error('invalid_request', 400, locale);
  const contentLength = Number(request.headers.get('content-length') ?? '0');
  if (contentLength > 16384) return error('invalid_request', 413, locale);
  const raw = await readLimitedJsonBody(request.body, 16384);
  if (raw === null) return error('invalid_request', 413, locale);
  let decoded: unknown;
  try { decoded = JSON.parse(raw) as unknown; } catch { return error('invalid_request', 400, locale); }
  const input = parseCartInput(decoded);
  const key = request.headers.get('idempotency-key') ?? '';
  if (!input || !/^[A-Za-z0-9._~-]{20,120}$/.test(key)) return error('invalid_request', 400, locale);
  const token = process.env.API_CART_SERVICE_TOKEN ?? '';
  if (token === '') return error('temporarily_unavailable', 503, locale);
  const result = await addCartItem(input, key, cookies[sessionName], cookies[csrfName], token, getApiInternalUrl(), locale, trustedForwardingHeaders(request));
  const response = NextResponse.json(result.payload, { status: result.status, headers: { 'cache-control': 'no-store, private', ...(result.retryAfter ? { 'retry-after': result.retryAfter } : {}) } });
  return response;
}

function error(code: CartErrorCode, status: number, locale: 'pt-BR'|'en'|'es'): NextResponse {
  return NextResponse.json({ error: { code, message: cartMessages[code][locale], correlation_id: crypto.randomUUID() } }, { status, headers: { 'cache-control': 'no-store, private' } });
}
function selectedLocale(language: string | null): 'pt-BR'|'en'|'es' {
  const value = language?.split(',')[0].trim().toLowerCase();
  return value === 'en' || value?.startsWith('en-') ? 'en' : value === 'es' || value?.startsWith('es-') ? 'es' : 'pt-BR';
}
function parseCookies(header: string): Record<string, string> {
  const result: Record<string,string> = {};
  for (const part of header.split(';')) { const index = part.indexOf('='); if (index > 0) result[part.slice(0,index).trim()] = part.slice(index+1); }
  return result;
}
function trustedForwardingHeaders(request: Request): Record<string, string> {
  if (process.env.TRUSTED_EDGE_FORWARDING_HEADERS !== 'true') return {};
  const value = request.headers.get('x-forwarded-for')?.trim();
  return value && value.length <= 1024 && !/[\r\n]/.test(value) ? { 'x-forwarded-for': value } : {};
}
