import 'server-only';

import { getApiInternalUrl } from '@/bff/apiClient';
import { localError } from '@/bff/pricingValidation';
import { postPricingQuote } from '@/bff/pricingTransport';
import type { PricingResult } from '@/bff/pricingTransport';
import type { QuoteInput } from '@/bff/pricingValidation';

export type { PricingQuote, PricingError } from '@/bff/pricingValidation';

export async function createQuote(
  input: QuoteInput,
  fetcher: typeof fetch = fetch,
  locale: 'pt-BR' | 'en' | 'es' = 'pt-BR',
): Promise<PricingResult> {
  try {
    return await postPricingQuote(input, getApiInternalUrl(), fetcher, locale);
  } catch {
    return { status: 503, payload: localError('quote_unavailable_temporarily', locale) };
  }
}
