import { expect, test } from '@playwright/test';

test('invitation form is accessible at 320px and adds only after server acceptance', async ({ page }) => {
  await page.setViewportSize({ width: 320, height: 800 });
  const serverIdempotencyKey = 'k'.repeat(43);
  await page.route('**/api/cart/session', (route) => route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ csrf_token: 'c'.repeat(43), idempotency_key: serverIdempotencyKey }) }));
  let submitted: Record<string, unknown> | undefined;
  let idempotencyKey = '';
  await page.route('**/api/cart/items', async (route) => {
    submitted = route.request().postDataJSON() as Record<string, unknown>;
    idempotencyKey = route.request().headers()['idempotency-key'] ?? '';
    const configuration = (submitted.configuration ?? {}) as Record<string, unknown>;
    await route.fulfill({ status: 201, contentType: 'application/json', body: JSON.stringify({
      item_reference: 'd5a9e323-6a41-42bd-a181-b89189eff10b', product_id: '14df04c5-9a9c-4eec-9a9f-b532be345ef9', product_slug: 'produto-2',
      model_id: '25640e9a-9d69-41eb-aa07-d4b2d3c37e43', model_key: 'standard', quantity: 1,
      configuration, quote_snapshot_id: (submitted.quote_snapshot_id as string), quote: { total_minor: 1000, currency: 'EUR', pricing_rule_version: 1 }, created_at: '2030-01-01T12:00:00Z',
    }) });
  });
  const quoteResponse = page.waitForResponse((response) => response.url().endsWith('/api/pricing/quotes'));
  await page.goto('/produtos/produto-2');
  await page.getByLabel('Idioma').selectOption('en');
  expect((await quoteResponse).headers()['cache-control']).toContain('no-store');
  await page.getByLabel('Theme').fill('Garden free text');
  await page.getByLabel('Colors').fill('Blue and gold');
  await page.getByLabel('Name or text to appear on the invitation').fill('Lia');
  await page.getByLabel('Event date').fill('2030-08-14');
  await page.getByLabel('Local time').fill('16:30');
  await page.getByLabel('Event location').fill('Flower Farm');
  await expect(page.getByRole('button', { name: 'Add to cart' })).toBeEnabled();
  await page.getByRole('button', { name: 'Add to cart' }).click();
  await expect(page).toHaveURL(/\/carrinho\?incluido=1$/);
  expect(submitted?.product_slug).toBe('produto-2');
  expect(submitted?.quote_snapshot_id).toMatch(/^[a-f0-9]{64}$/);
  expect((submitted?.configuration as Record<string, string>).theme).toBe('Garden free text');
  expect(idempotencyKey).toBe(serverIdempotencyKey);
  expect(page.url()).not.toContain('Garden');
  expect(await page.evaluate(() => document.documentElement.scrollWidth)).toBeLessThanOrEqual(320);
});

test('complex invitation keeps its published model type in the saved prefill', async ({ page }) => {
  const key = 'x'.repeat(43);
  await page.route('**/api/cart/session', (route) => route.fulfill({ status:200, contentType:'application/json', body:JSON.stringify({ csrf_token:'c'.repeat(43), idempotency_key:key }) }));
  let submitted: Record<string,unknown> | undefined;
  await page.route('**/api/cart/items', async (route) => {
    submitted = route.request().postDataJSON() as Record<string,unknown>;
    const configuration = submitted.configuration as Record<string,unknown>;
    await route.fulfill({ status:201, contentType:'application/json', body:JSON.stringify({
      item_reference:'d5a9e323-6a41-42bd-a181-b89189eff10b', product_id:'14df04c5-9a9c-4eec-9a9f-b532be345ef9', product_slug:'produto-2',
      model_id:'25640e9a-9d69-41eb-aa07-d4b2d3c37e43', model_key:'complex', quantity:1, configuration,
      quote_snapshot_id:submitted.quote_snapshot_id, quote:{ total_minor:1000, currency:'EUR', pricing_rule_version:1 }, created_at:'2030-01-01T12:00:00Z',
    }) });
  });
  await page.goto('/produtos/produto-2?modelo=complex');
  await page.getByLabel('Idioma').selectOption('en');
  await page.getByLabel('Theme').fill('Garden');
  await page.getByLabel('Colors').fill('Blue and gold');
  await page.getByLabel('Name or text to appear on the invitation').fill('Lia');
  await page.getByLabel('Event date').fill('2030-08-14');
  await page.getByLabel('Local time').fill('16:30');
  await page.getByLabel('Event location').fill('Flower Farm');
  await page.getByRole('button', { name:'Add to cart' }).click();
  await expect(page).toHaveURL(/\/carrinho\?incluido=1$/);
  expect(submitted?.model_key).toBe('complex');
  expect((submitted?.configuration as Record<string,unknown>).invite_type).toBe('complex');
});

test('does not show an estimated price or enable inclusion if quote fails', async ({ page }) => {
  await page.route('**/api/pricing/quotes', (route) => route.fulfill({ status: 503, contentType: 'application/json', body: JSON.stringify({ error: { code: 'quote_unavailable_temporarily', message: 'The price could not be calculated now. Try again.', correlation_id: '00000000-0000-4000-8000-000000000000' } }) }));
  await page.goto('/produtos/produto-2');
  await page.getByLabel('Idioma').selectOption('en');
  await expect(page.getByRole('status')).toContainText('The price could not be calculated now. Try again.');
  await expect(page.locator('.pricing-panel__values')).toBeEmpty();
  await expect(page.getByRole('button', { name: 'Try again' })).toBeVisible();
  await expect(page.getByRole('button', { name: 'Add to cart' })).toBeDisabled();
});

test('preserves entered personalization and reuses the server idempotency key after a recoverable failure', async ({ page }) => {
  const key = 'r'.repeat(43);
  await page.route('**/api/cart/session', (route) => route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ csrf_token: 'c'.repeat(43), idempotency_key: key }) }));
  const submittedKeys: string[] = [];
  let attempt = 0;
  await page.route('**/api/cart/items', async (route) => {
    submittedKeys.push(route.request().headers()['idempotency-key'] ?? '');
    attempt += 1;
    if (attempt === 1) return route.fulfill({ status: 503, contentType: 'application/json', body: JSON.stringify({ error: { code:'temporarily_unavailable', message:'Try again.', correlation_id:'d5a9e323-6a41-42bd-a181-b89189eff10b' } }) });
    const input = route.request().postDataJSON() as Record<string, unknown>;
    const configuration = input.configuration as Record<string, unknown>;
    return route.fulfill({ status: 201, contentType: 'application/json', body: JSON.stringify({
      item_reference:'d5a9e323-6a41-42bd-a181-b89189eff10b', product_id:'14df04c5-9a9c-4eec-9a9f-b532be345ef9', product_slug:'produto-2',
      model_id:'25640e9a-9d69-41eb-aa07-d4b2d3c37e43', model_key:'standard', quantity:1, configuration,
      quote_snapshot_id:input.quote_snapshot_id, quote:{ total_minor:1000, currency:'EUR', pricing_rule_version:1 }, created_at:'2030-01-01T12:00:00Z',
    }) });
  });
  await page.goto('/produtos/produto-2');
  await page.getByLabel('Idioma').selectOption('en');
  await page.getByLabel('Theme').fill('Garden free text');
  await page.getByLabel('Colors').fill('Blue and gold');
  await page.getByLabel('Name or text to appear on the invitation').fill('Lia');
  await page.getByLabel('Event date').fill('2030-08-14');
  await page.getByLabel('Local time').fill('16:30');
  await page.getByLabel('Event location').fill('Flower Farm');
  await page.getByRole('button', { name:'Add to cart' }).click();
  await expect(page.locator('form [role="alert"]')).toContainText('Try again.');
  await expect(page.getByLabel('Name or text to appear on the invitation')).toHaveValue('Lia');
  await expect(page).not.toHaveURL(/\/carrinho/);
  await page.getByRole('button', { name:'Add to cart' }).click();
  await expect(page).toHaveURL(/\/carrinho\?incluido=1$/);
  expect(submittedKeys).toEqual([key,key]);
});

test('ignores an old quote response when the latest quantity has already been quoted', async ({ page }) => {
  let releaseFirst: (() => void) | undefined;
  const firstResponse = new Promise<void>((resolve) => { releaseFirst = resolve; });
  await page.route('**/api/pricing/quotes', async (route) => {
    const request = route.request().postDataJSON() as { product_slug: string; model_key?: string; quantity: number };
    if (request.quantity === 12) await firstResponse;
    const unit = request.quantity >= 50 ? 900 : 950;
    await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({
      product_slug: request.product_slug, model_key: request.model_key ?? null, quantity: request.quantity, minimum_quantity: 12, maximum_quantity: 250, quote_snapshot_id: 'a'.repeat(64),
      base_unit_price_minor: 1003, unit_price_minor: unit, subtotal_minor: 1003 * request.quantity,
      discount_minor: (1003 - unit) * request.quantity, total_minor: unit * request.quantity,
      currency: 'EUR', pricing_rule_version: 1, applied_tier: { minimum_quantity: request.quantity >= 50 ? 50 : 12, maximum_quantity: request.quantity >= 50 ? null : 49, unit_price_minor: unit },
    }) });
  });
  await page.goto('/produtos/produto-3');
  await page.getByLabel('Idioma').selectOption('en');
  const quantity = page.getByRole('spinbutton', { name: 'Quantity' });
  await expect(quantity).toHaveValue('12');
  await quantity.fill('50');
  await expect(page.locator('.pricing-panel')).toContainText('\u20ac450.00');
  releaseFirst?.();
  await expect(page.locator('.pricing-panel')).toContainText('\u20ac450.00');
  await expect(page.locator('.pricing-panel')).not.toContainText('\u20ac9.50');
});
