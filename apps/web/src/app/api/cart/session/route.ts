import { NextResponse } from 'next/server';
import { getApiInternalUrl } from '@/bff/apiClient';
import { readLimitedJsonBody } from '@/bff/cartValidation';

const sessionName = '__Host-jsd_cart_session';
const csrfName = '__Host-jsd_cart_csrf';

export async function GET(request: Request): Promise<NextResponse> {
  const cookies = parseCookies(request.headers.get('cookie') ?? '');
  const token = process.env.API_CART_SERVICE_TOKEN ?? '';
  if (token === '') return unavailable();
  try {
    const existing = /^[A-Za-z0-9_-]{43}$/.test(cookies[sessionName] ?? '') ? cookies[sessionName] : '';
    const upstream = await fetch(new URL('/api/v1/cart/sessions', getApiInternalUrl()), { method: 'POST', cache: 'no-store', signal: AbortSignal.timeout(3000), headers: { accept: 'application/json', authorization: `Bearer ${token}`, ...(existing ? { 'x-guest-session': existing } : {}), ...trustedForwardingHeaders(request) } });
    const raw = await readLimitedJsonBody(upstream.body, 4096);
    const body: unknown = raw === null ? null : JSON.parse(raw);
    if (upstream.status !== 201 || !isSession(body)) return unavailable();
    const response = NextResponse.json({ csrf_token: body.csrf_token, idempotency_key: body.idempotency_key }, { headers: { 'cache-control': 'no-store, private' } });
    response.cookies.set(sessionName, body.reference, { httpOnly: true, secure: true, sameSite: 'lax', path: '/', maxAge: 60 * 60 * 24 * 30 });
    response.cookies.set(csrfName, body.csrf_token, { httpOnly: false, secure: true, sameSite: 'strict', path: '/', maxAge: 60 * 60 * 24 * 30 });
    return response;
  } catch { return unavailable(); }
}

function unavailable(): NextResponse { return NextResponse.json({ error: { code: 'temporarily_unavailable' } }, { status: 503, headers: { 'cache-control': 'no-store, private' } }); }
function isSession(value: unknown): value is { reference: string; csrf_token: string; idempotency_key: string } {
  return !!value && typeof value === 'object' && /^[A-Za-z0-9_-]{43}$/.test((value as Record<string, unknown>).reference as string) && /^[A-Za-z0-9_-]{43}$/.test((value as Record<string, unknown>).csrf_token as string) && /^[A-Za-z0-9_-]{43}$/.test((value as Record<string, unknown>).idempotency_key as string);
}

function parseCookies(header: string): Record<string, string> {
  return Object.fromEntries(header.split(';').map((part) => part.trim().split(/=(.*)/s, 2)).filter((item) => item.length === 2));
}
function trustedForwardingHeaders(request: Request): Record<string, string> {
  if (process.env.TRUSTED_EDGE_FORWARDING_HEADERS !== 'true') return {};
  const value = request.headers.get('x-forwarded-for')?.trim();
  return value && value.length <= 1024 && !/[\r\n]/.test(value) ? { 'x-forwarded-for': value } : {};
}
