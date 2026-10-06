import { isPublicError, isQuote, localError, pricingMessages, readLimitedJsonBody, statusFor } from './pricingValidation.ts';
import type { PricingError, PricingQuote, QuoteInput } from './pricingValidation.ts';

export type PricingResult = Readonly<{ status: number; payload: PricingQuote | PricingError }>;
export const QUOTE_TIMEOUT_MS = 3000;

export async function postPricingQuote(
  input: QuoteInput,
  apiInternalUrl: string,
  fetcher: typeof fetch = fetch,
  locale: 'pt-BR' | 'en' | 'es' = 'pt-BR',
): Promise<PricingResult> {
  try {
    const path = new URL('/api/v1/pricing/quotes', apiInternalUrl);
    const response = await fetcher(path, {
      method: 'POST',
      headers: { accept: 'application/json', 'content-type': 'application/json' },
      cache: 'no-store',
      signal: AbortSignal.timeout(QUOTE_TIMEOUT_MS),
      body: JSON.stringify(input),
    });
    const rawPayload = await readLimitedJsonBody(response.body, 16_384);
    if (rawPayload === null) throw new Error('Invalid upstream response body.');
    const payload: unknown = JSON.parse(rawPayload) as unknown;
    if (
      response.status === 200 && isQuote(payload) &&
      payload.product_slug === input.product_slug &&
      payload.model_key === (input.model_key ?? null) &&
      payload.quantity === input.quantity
    ) {
      return { status: 200, payload };
    }
    if (isPublicError(payload) && statusFor(payload.error.code) === response.status) {
      return {
        status: response.status,
        payload: { error: { ...payload.error, message: pricingMessages[payload.error.code][locale] } },
      };
    }
  } catch {
    // Internal details stay server-side; the public response remains recoverable.
  }

  return { status: 503, payload: localError('quote_unavailable_temporarily', locale) };
}
