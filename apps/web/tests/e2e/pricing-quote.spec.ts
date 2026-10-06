import { expect, test } from '@playwright/test';

test('cota o produto físico pelo BFF e atualiza faixa, preço e estado acessível', async ({ page }) => {
  await page.setViewportSize({ width: 320, height: 800 });
  const quoteResponse = page.waitForResponse((response) => response.url().endsWith('/api/pricing/quotes'));
  await page.goto('/produtos/produto-3');
  expect((await quoteResponse).headers()['cache-control']).toContain('no-store');
  const quantity = page.getByRole('spinbutton', { name: 'Quantidade' });
  await expect(quantity).toHaveValue('12');
  await expect(page.getByText('Total', { exact: true })).toBeVisible();
  await expect(page.locator('.pricing-panel')).toContainText('€ 114,00');
  await quantity.fill('50');
  await expect(page.locator('.pricing-panel')).toContainText('€ 450,00');
  await expect(page.locator('.pricing-panel')).toContainText('Máximo: 250');
  await expect(page.getByRole('link', { name: 'Continuar para configuração' })).toBeVisible();
  await expect(page.getByText('Ainda não adicionamos este item ao carrinho.')).toBeVisible();
  await expect(page.locator('.pricing-panel__values')).toHaveAttribute('aria-live', 'polite');
  const width = await page.evaluate(() => document.documentElement.scrollWidth);
  expect(width).toBeLessThanOrEqual(320);
  await quantity.focus();
  await expect(quantity).toBeFocused();
});

test('não exibe preço estimado se a cotação pública falhar', async ({ page }) => {
  await page.route('**/api/pricing/quotes', (route) => route.fulfill({ status: 503, contentType: 'application/json', body: JSON.stringify({ error: { code: 'quote_unavailable_temporarily', message: 'Não foi possível calcular o preço agora. Tente novamente.', correlation_id: '00000000-0000-4000-8000-000000000000' } }) }));
  await page.goto('/produtos/produto-3');
  await expect(page.getByRole('status')).toContainText('Não foi possível calcular o preço agora. Tente novamente.');
  await expect(page.locator('.pricing-panel__values')).toBeEmpty();
  await expect(page.getByRole('button', { name: 'Tentar novamente' })).toBeVisible();
});

test('ignora resposta antiga quando a quantidade mais recente já foi cotada', async ({ page }) => {
  let releaseFirst: (() => void) | undefined;
  const firstResponse = new Promise<void>((resolve) => { releaseFirst = resolve; });
  await page.route('**/api/pricing/quotes', async (route) => {
    const request = route.request().postDataJSON() as { product_slug: string; model_key?: string; quantity: number };
    if (request.quantity === 12) await firstResponse;
    const unit = request.quantity >= 50 ? 900 : 950;
    await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({
      product_slug: request.product_slug, model_key: request.model_key ?? null, quantity: request.quantity, minimum_quantity: 12, maximum_quantity: 250,
      base_unit_price_minor: 1003, unit_price_minor: unit, subtotal_minor: 1003 * request.quantity,
      discount_minor: (1003 - unit) * request.quantity, total_minor: unit * request.quantity,
      currency: 'EUR', pricing_rule_version: 1, applied_tier: { minimum_quantity: request.quantity >= 50 ? 50 : 12, maximum_quantity: request.quantity >= 50 ? null : 49, unit_price_minor: unit },
    }) });
  });
  await page.goto('/produtos/produto-3');
  const quantity = page.getByRole('spinbutton', { name: 'Quantidade' });
  await expect(quantity).toHaveValue('12');
  await quantity.fill('50');
  await expect(page.locator('.pricing-panel')).toContainText('€ 450,00');
  releaseFirst?.();
  await expect(page.locator('.pricing-panel')).toContainText('€ 450,00');
  await expect(page.locator('.pricing-panel')).not.toContainText('€ 114,00');
});
