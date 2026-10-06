import assert from 'node:assert/strict';
import test from 'node:test';

import { postPricingQuote, QUOTE_TIMEOUT_MS } from '../../src/bff/pricingTransport.ts';

const input = { product_slug: 'produto-1', model_key: 'premium', quantity: 2, currency: 'EUR' };
const quote = { product_slug: 'produto-1', model_key: 'premium', quantity: 2, minimum_quantity: 1, maximum_quantity: 100,
  base_unit_price_minor: 500, unit_price_minor: 450, subtotal_minor: 1000, discount_minor: 100, total_minor: 900,
  currency: 'EUR', pricing_rule_version: 2, applied_tier: { minimum_quantity: 2, maximum_quantity: 5, unit_price_minor: 450 } };

test('Laravel request sends only pricing inputs, no-store and the three second deadline', async () => {
  let calledUrl;
  let calledOptions;
  const result = await postPricingQuote(input, 'http://127.0.0.1:8000', async (url, options) => {
    calledUrl = url;
    calledOptions = options;
    return Response.json(quote);
  });
  assert.equal(result.status, 200);
  assert.equal(calledUrl.href, 'http://127.0.0.1:8000/api/v1/pricing/quotes');
  assert.equal(calledOptions.cache, 'no-store');
  assert.equal(calledOptions.signal instanceof AbortSignal, true);
  assert.equal(QUOTE_TIMEOUT_MS, 3000);
  assert.deepEqual(JSON.parse(calledOptions.body), input);
});

test('each Laravel public error is preserved by code and translated explicitly', async () => {
  const cases = [
    ['invalid_request', 400], ['invalid_quantity', 422], ['invalid_model', 422], ['unsupported_currency', 422],
    ['quote_unavailable', 404], ['rate_limited', 429], ['quote_unavailable_temporarily', 503],
  ];
  for (const [code, status] of cases) {
    const result = await postPricingQuote(input, 'http://127.0.0.1:8000', async () => Response.json({ error: {
      code, message: 'upstream internal copy', correlation_id: '00000000-0000-4000-8000-000000000000',
    } }, { status }), 'es');
    assert.equal(result.status, status);
    assert.equal(result.payload.error.code, code);
    assert.notEqual(result.payload.error.message, 'upstream internal copy');
  }
});

test('upstream timeout and malformed success return a recoverable error without inventing a quote', async () => {
  const timeout = await postPricingQuote(input, 'http://127.0.0.1:8000', async (_url, options) => {
    assert.equal(options.signal instanceof AbortSignal, true);
    throw new DOMException('deadline', 'TimeoutError');
  });
  assert.equal(timeout.status, 503);
  assert.equal(timeout.payload.error.code, 'quote_unavailable_temporarily');
  const malformed = await postPricingQuote(input, 'http://127.0.0.1:8000', async () => Response.json({ total_minor: 0 }));
  assert.equal(malformed.status, 503);
  assert.equal('total_minor' in malformed.payload, false);
});

test('an upstream quote for another quantity is rejected', async () => {
  const result = await postPricingQuote(input, 'http://127.0.0.1:8000', async () => Response.json({ ...quote, quantity: 3 }));
  assert.equal(result.status, 503);
  assert.equal(result.payload.error.code, 'quote_unavailable_temporarily');
});

test('an oversized upstream body is stopped before JSON parsing', async () => {
  const result = await postPricingQuote(input, 'http://127.0.0.1:8000', async () => new Response(' '.repeat(16_385)));
  assert.equal(result.status, 503);
  assert.equal(result.payload.error.code, 'quote_unavailable_temporarily');
});
