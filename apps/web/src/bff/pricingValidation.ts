export type PricingQuote = Readonly<{
  product_slug: string; model_key: string | null; quantity: number; minimum_quantity: number; maximum_quantity: number;
  base_unit_price_minor: number; unit_price_minor: number; subtotal_minor: number; discount_minor: number;
  total_minor: number; currency: 'EUR'; pricing_rule_version: number;
  applied_tier: Readonly<{ minimum_quantity: number; maximum_quantity: number | null; unit_price_minor: number }> | null;
}>;
export type PricingErrorCode = 'invalid_request' | 'invalid_quantity' | 'invalid_model' | 'unsupported_currency' | 'quote_unavailable' | 'rate_limited' | 'quote_unavailable_temporarily';
export type PricingError = Readonly<{ error: Readonly<{ code: PricingErrorCode; message: string; correlation_id: string }> }>;
export type QuoteInput = Readonly<{ product_slug: string; model_key?: string; quantity: number; currency: 'EUR' }>;
export const pricingMessages: Record<PricingErrorCode, Record<'pt-BR' | 'en' | 'es', string>> = {
  invalid_request: { 'pt-BR': 'Revise os dados informados.', en: 'Review the information provided.', es: 'Revisa la información proporcionada.' },
  invalid_quantity: { 'pt-BR': 'Informe uma quantidade dentro dos limites exibidos.', en: 'Enter a quantity within the displayed limits.', es: 'Indica una cantidad dentro de los límites mostrados.' },
  invalid_model: { 'pt-BR': 'Escolha um modelo disponível.', en: 'Choose an available model.', es: 'Elige un modelo disponible.' },
  unsupported_currency: { 'pt-BR': 'A moeda não é aceita.', en: 'This currency is not supported.', es: 'Esta moneda no está admitida.' },
  quote_unavailable: { 'pt-BR': 'Este produto não está disponível para cotação.', en: 'This product is unavailable for a quote.', es: 'Este producto no está disponible para cotización.' },
  rate_limited: { 'pt-BR': 'Muitas cotações. Tente novamente em instantes.', en: 'Too many quotes. Try again shortly.', es: 'Demasiadas cotizaciones. Inténtalo de nuevo pronto.' },
  quote_unavailable_temporarily: { 'pt-BR': 'Não foi possível calcular o preço agora. Tente novamente.', en: 'The price could not be calculated now. Try again.', es: 'No se pudo calcular el precio ahora. Inténtalo de nuevo.' },
};
const codes: readonly PricingErrorCode[] = ['invalid_request', 'invalid_quantity', 'invalid_model', 'unsupported_currency', 'quote_unavailable', 'rate_limited', 'quote_unavailable_temporarily'];

export function parseQuoteInput(value: unknown): Readonly<{ input?: QuoteInput; error?: PricingErrorCode }> {
  if (!isRecord(value) || !hasOnlyKeys(value, ['product_slug', 'model_key', 'quantity', 'currency']) ||
    typeof value.product_slug !== 'string' || value.product_slug.length > 180 || !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(value.product_slug) ||
    (Object.hasOwn(value, 'model_key') && (typeof value.model_key !== 'string' || value.model_key.length > 120 || !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(value.model_key))) ||
    typeof value.quantity !== 'number' || typeof value.currency !== 'string') return { error: 'invalid_request' };
  if (value.quantity < 1 || value.quantity > 10000 || !Number.isSafeInteger(value.quantity)) return { error: 'invalid_quantity' };
  if (value.currency !== 'EUR') return { error: 'unsupported_currency' };
  return { input: { product_slug: value.product_slug, ...(typeof value.model_key === 'string' ? { model_key: value.model_key } : {}), quantity: value.quantity, currency: 'EUR' } };
}

export async function readLimitedJsonBody(stream: ReadableStream<Uint8Array> | null, limitBytes: number): Promise<string | null> {
  if (!stream) return null;
  const reader = stream.getReader();
  const chunks: Uint8Array[] = [];
  let totalBytes = 0;
  try {
    while (true) {
      const { done, value } = await reader.read();
      if (done) break;
      totalBytes += value.byteLength;
      if (totalBytes > limitBytes) {
        await reader.cancel();
        return null;
      }
      chunks.push(value);
    }
    const body = new Uint8Array(totalBytes);
    let offset = 0;
    for (const chunk of chunks) {
      body.set(chunk, offset);
      offset += chunk.byteLength;
    }
    return new TextDecoder('utf-8', { fatal: true }).decode(body);
  } catch {
    return null;
  } finally {
    reader.releaseLock();
  }
}

export function isQuote(value: unknown): value is PricingQuote {
  if (!isRecord(value) || !hasExactKeys(value, ['product_slug', 'model_key', 'quantity', 'minimum_quantity', 'maximum_quantity', 'base_unit_price_minor', 'unit_price_minor', 'subtotal_minor', 'discount_minor', 'total_minor', 'currency', 'pricing_rule_version', 'applied_tier'])) return false;
  const numeric = ['quantity', 'minimum_quantity', 'maximum_quantity', 'base_unit_price_minor', 'unit_price_minor', 'subtotal_minor', 'discount_minor', 'total_minor', 'pricing_rule_version'];
  if (!numeric.every((key) => Number.isSafeInteger(value[key]) && (value[key] as number) >= (key.includes('price') || key.includes('minor') || key === 'discount_minor' || key === 'subtotal_minor' || key === 'total_minor' ? 0 : 1))) return false;
  if (typeof value.product_slug !== 'string' || value.product_slug.length > 180 || !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(value.product_slug) ||
    !(value.model_key === null || (typeof value.model_key === 'string' && value.model_key.length <= 120 && /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(value.model_key))) ||
    value.currency !== 'EUR' || (value.quantity as number) < (value.minimum_quantity as number) ||
    (value.quantity as number) > (value.maximum_quantity as number) || (value.maximum_quantity as number) > 10000) return false;
  const baseSubtotal = BigInt(value.base_unit_price_minor as number) * BigInt(value.quantity as number);
  const appliedTotal = BigInt(value.unit_price_minor as number) * BigInt(value.quantity as number);
  if (baseSubtotal !== BigInt(value.subtotal_minor as number) || appliedTotal !== BigInt(value.total_minor as number) ||
    BigInt(value.subtotal_minor as number) - BigInt(value.total_minor as number) !== BigInt(value.discount_minor as number) ||
    (value.unit_price_minor as number) > (value.base_unit_price_minor as number)) return false;
  if (value.applied_tier === null) return value.unit_price_minor === value.base_unit_price_minor;
  const tier = value.applied_tier;
  return isRecord(tier) && hasExactKeys(tier, ['minimum_quantity', 'maximum_quantity', 'unit_price_minor']) &&
    Number.isSafeInteger(tier.minimum_quantity) && (tier.minimum_quantity as number) > 0 &&
    (tier.maximum_quantity === null || (Number.isSafeInteger(tier.maximum_quantity) && (tier.maximum_quantity as number) >= (tier.minimum_quantity as number))) &&
    Number.isSafeInteger(tier.unit_price_minor) && (tier.unit_price_minor as number) >= 0 &&
    (value.quantity as number) >= (tier.minimum_quantity as number) &&
    (tier.maximum_quantity === null || (value.quantity as number) <= (tier.maximum_quantity as number)) &&
    tier.unit_price_minor === value.unit_price_minor;
}

export function isPublicError(value: unknown): value is PricingError {
  return isRecord(value) && Object.keys(value).length === 1 && isRecord(value.error) && hasExactKeys(value.error, ['code', 'message', 'correlation_id']) &&
    codes.includes(value.error.code as PricingErrorCode) && typeof value.error.message === 'string' &&
    typeof value.error.correlation_id === 'string' && /^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i.test(value.error.correlation_id);
}
export function statusFor(code: PricingErrorCode): number { return code === 'invalid_request' ? 400 : code === 'quote_unavailable' ? 404 : code === 'rate_limited' ? 429 : code === 'quote_unavailable_temporarily' ? 503 : 422; }
export function localError(code: PricingErrorCode, locale: 'pt-BR' | 'en' | 'es' = 'pt-BR'): PricingError {
  return { error: { code, message: pricingMessages[code][locale], correlation_id: crypto.randomUUID() } };
}
function isRecord(value: unknown): value is Record<string, unknown> { return value !== null && typeof value === 'object' && !Array.isArray(value); }
function hasExactKeys(value: Record<string, unknown>, keys: string[]): boolean { return Object.keys(value).length === keys.length && keys.every((key) => Object.hasOwn(value, key)); }
function hasOnlyKeys(value: Record<string, unknown>, keys: string[]): boolean { return Object.keys(value).every((key) => keys.includes(key)) && ['product_slug', 'quantity', 'currency'].every((key) => Object.hasOwn(value, key)); }
