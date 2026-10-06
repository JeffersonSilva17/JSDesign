import assert from 'node:assert/strict';
import test from 'node:test';

import { isPublicError, isQuote, localError, parseQuoteInput, pricingMessages, readLimitedJsonBody, statusFor } from '../../src/bff/pricingValidation.ts';

test('quote request forwards only the public pricing inputs', () => {
  assert.deepEqual(parseQuoteInput({ product_slug: 'produto-1', model_key: 'premium', quantity: 2, currency: 'EUR' }).input,
    { product_slug: 'produto-1', model_key: 'premium', quantity: 2, currency: 'EUR' });
  for (const extra of [{ price_minor: 2 }, { discount_minor: 1 }, { pricing_rule_version: 3 }, { maximum_quantity: 8 }]) {
    assert.equal(parseQuoteInput({ product_slug: 'produto-1', quantity: 2, currency: 'EUR', ...extra }).error, 'invalid_request');
  }
});

test('quantity and currency errors retain their explicit public codes', () => {
  for (const quantity of [0, -1, 1.5, 10001, Number.MAX_SAFE_INTEGER + 1]) {
    assert.equal(parseQuoteInput({ product_slug: 'produto-1', quantity, currency: 'EUR' }).error, 'invalid_quantity');
  }
  assert.equal(parseQuoteInput({ product_slug: 'produto-1', quantity: 2, currency: 'USD' }).error, 'unsupported_currency');
  assert.equal(parseQuoteInput({ product_slug: 'INVALID', quantity: 2, currency: 'EUR' }).error, 'invalid_request');
});

test('strict quote response allowlist rejects money rounding and extra properties', () => {
  const quote = { product_slug: 'produto-1', model_key: null, quantity: 2, minimum_quantity: 1, maximum_quantity: 100,
    base_unit_price_minor: 500, unit_price_minor: 450, subtotal_minor: 1000, discount_minor: 100, total_minor: 900,
    currency: 'EUR', pricing_rule_version: 2, applied_tier: { minimum_quantity: 2, maximum_quantity: 5, unit_price_minor: 450 } };
  assert.equal(isQuote(quote), true);
  assert.equal(isQuote({ ...quote, total_minor: 900.5 }), false);
  assert.equal(isQuote({ ...quote, internal_cost_minor: 25 }), false);
  assert.equal(isQuote({ ...quote, total_minor: 901 }), false);
});

test('public errors validate and translate every supported code for all configured locales', () => {
  for (const code of ['invalid_request', 'invalid_quantity', 'invalid_model', 'unsupported_currency', 'quote_unavailable', 'rate_limited', 'quote_unavailable_temporarily']) {
    for (const locale of ['pt-BR', 'en', 'es']) {
      const error = localError(code, locale);
      assert.equal(isPublicError(error), true);
      assert.equal(error.error.message, pricingMessages[code][locale]);
      assert.equal(typeof error.error.correlation_id, 'string');
      assert.equal(isPublicError({ error: { ...error.error, correlation_id: 'untrusted-upstream-value' } }), false);
    }
  }
  assert.deepEqual(['invalid_request', 'invalid_quantity', 'invalid_model', 'unsupported_currency', 'quote_unavailable', 'rate_limited', 'quote_unavailable_temporarily'].map(statusFor),
    [400, 422, 422, 422, 404, 429, 503]);
});

test('limited JSON body reader accepts the byte limit and rejects oversized or invalid UTF-8 input', async () => {
  const atLimit = new TextEncoder().encode('{"a":"á"}');
  assert.equal(await readLimitedJsonBody(new ReadableStream({ start(controller) { controller.enqueue(atLimit); controller.close(); } }), atLimit.byteLength), '{"a":"á"}');
  let cancelled = false;
  const tooLarge = new ReadableStream({
    start(controller) { controller.enqueue(new Uint8Array(4)); },
    cancel() { cancelled = true; },
  });
  assert.equal(await readLimitedJsonBody(tooLarge, 3), null);
  assert.equal(cancelled, true);
  assert.equal(await readLimitedJsonBody(new ReadableStream({ start(controller) { controller.enqueue(new Uint8Array([0xff])); controller.close(); } }), 4), null);
});
