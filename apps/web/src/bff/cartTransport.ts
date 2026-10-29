import 'server-only';
import { cartMessages, isCartError, isCartItem, readLimitedJsonBody } from './cartValidation';
import { isCartEnvelope } from './cartValidation';
import type { CartAddInput, CartErrorCode } from './cartValidation';

function configuredTimeout(): number {
  const value = Number(process.env.API_CART_TIMEOUT_MS ?? '3000');
  return Number.isInteger(value) && value >= 500 && value <= 10_000 ? value : 3000;
}
export const CART_TIMEOUT_MS = configuredTimeout();
const CART_RESPONSE_MAX_BYTES = 65_536;
const statusByCode: Record<CartErrorCode, number> = {
  invalid_request: 400, unsupported_mode: 422, invalid_configuration: 422, stale_quote: 409, unavailable: 409,
  idempotency_conflict: 409, csrf_failed: 419, rate_limited: 429, temporarily_unavailable: 503,
};

export async function addCartItem(input: CartAddInput, idempotencyKey: string, session: string, csrfToken: string, token: string, apiUrl: string, locale: 'pt-BR'|'en'|'es', forwardingHeaders: Record<string, string> = {}, fetcher: typeof fetch = fetch) {
  try {
    const response = await fetcher(new URL('/api/v1/cart/items', apiUrl), {
      method: 'POST', cache: 'no-store', signal: AbortSignal.timeout(CART_TIMEOUT_MS),
      headers: { accept: 'application/json', 'content-type': 'application/json', authorization: `Bearer ${token}`, 'x-guest-session': session, 'x-csrf-token': csrfToken, 'idempotency-key': idempotencyKey, ...forwardingHeaders },
      body: JSON.stringify(input),
    });
    const raw = await readLimitedJsonBody(response.body, CART_RESPONSE_MAX_BYTES);
    if (raw === null) throw new Error('Invalid cart response.');
    const payload: unknown = JSON.parse(raw) as unknown;
    if ((response.status === 201 || response.status === 200) && isCartItem(payload) && payload.product_slug === input.product_slug && payload.model_key === input.model_key && payload.quantity === input.quantity && payload.quote_snapshot_id === input.quote_snapshot_id && stableJson(payload.configuration) === stableJson(input.configuration)) {
      return { status: response.status, payload, retryAfter: null as string | null };
    }
    if (isCartError(payload) && statusByCode[payload.error.code] === response.status) {
      return { status: response.status, payload: { error: { ...payload.error, message: cartMessages[payload.error.code][locale] } }, retryAfter: response.headers.get('retry-after') };
    }
  } catch { /* Internal details stay server-side. */ }
  return { status: 503, payload: { error: { code: 'temporarily_unavailable', message: cartMessages.temporarily_unavailable[locale], correlation_id: crypto.randomUUID() } }, retryAfter: null as string | null };
}

export async function readCart(session: string, token: string, apiUrl: string, forwardingHeaders: Record<string, string> = {}, fetcher: typeof fetch = fetch) {
  try {
    const response = await fetcher(new URL('/api/v1/cart/items', apiUrl), { method: 'GET', cache: 'no-store', signal: AbortSignal.timeout(CART_TIMEOUT_MS), headers: { accept: 'application/json', authorization: `Bearer ${token}`, 'x-guest-session': session, ...forwardingHeaders } });
    const raw = await readLimitedJsonBody(response.body, CART_RESPONSE_MAX_BYTES);
    if (response.status !== 200 || raw === null) return null;
    const payload: unknown = JSON.parse(raw) as unknown;
    return isCartEnvelope(payload) ? payload.items : null;
  } catch { return null; }
}

function stableJson(value: unknown): string {
  if (Array.isArray(value)) return `[${value.map(stableJson).join(',')}]`;
  if (value && typeof value === 'object') {
    const record = value as Record<string, unknown>;
    return `{${Object.keys(record).sort().map((key) => `${JSON.stringify(key)}:${stableJson(record[key])}`).join(',')}}`;
  }
  return JSON.stringify(value);
}
