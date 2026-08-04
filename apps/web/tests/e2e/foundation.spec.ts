import { expect, test } from '@playwright/test';

test('carrega a fundação web pública', async ({ page }) => {
  await page.goto('/');

  await expect(page.getByRole('heading', { name: 'Loja online em preparação' })).toBeVisible();
  await expect(page.getByRole('link', { name: 'Ver health local' })).toBeVisible();
});

test('BFF consulta o health da Laravel API', async ({ request }) => {
  const response = await request.get('/api/health');

  expect(response.ok()).toBeTruthy();
  await expect(response.json()).resolves.toEqual({
    status: 'ok',
    service: 'jsdesign-web-bff',
    api: {
      status: 'ok',
      service: 'jsdesign-api',
      api_version: 'v1',
      checks: {
        app: 'ok',
        database: 'ok',
        redis: 'ok',
      },
    },
  });
});
