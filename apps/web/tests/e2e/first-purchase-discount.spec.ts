import { expect, test } from '@playwright/test';

const offer = {
  enabled: true,
  delivery_mode: 'display',
  discount_percent: 10,
  minimum_amount: null,
  non_cumulative: true,
  manual_checkout_required: true,
  authorization_text_version: 'coupon-v1',
  offer_id: 'first_purchase:coupon-v1:display:10',
  texts: {
    title: 'Ganhe 10% na primeira compra',
    description:
      'Solicite um cupom individual por e-mail. O desconto será validado no checkout quando essa etapa estiver disponível.',
    authorization:
      'Autorizo o uso deste e-mail somente para emitir e entregar meu cupom de primeira compra.',
    privacy_url: '/privacidade',
  },
} as const;

async function mockPromotionApi(page: import('@playwright/test').Page) {
  await page.route('**/api/promotions/first-purchase-offer', async (route) => {
    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify(offer),
    });
  });

  await page.route('**/api/promotions/first-purchase-coupons', async (route) => {
    await route.fulfill({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({
        status: 'accepted',
        delivery: 'display',
        message:
          'Cupom emitido para teste. Copie o código e use manualmente no checkout quando estiver disponível.',
        request_id: 'req-test',
        coupon_code: 'JS-ABCDEFGH23456789',
      }),
    });
  });
}

async function triggerDiscountDialog(page: import('@playwright/test').Page) {
  await page.locator('dialog.discount-dialog').waitFor({ state: 'attached' });
  await page.mouse.wheel(0, 1200);
  await expect(page.getByRole('dialog', { name: /ganhe 10%/i })).toBeVisible();
}

test('captura não abre apenas por timer', async ({ page }) => {
  await mockPromotionApi(page);
  await page.goto('/');
  await page.waitForTimeout(1000);

  await expect(page.getByRole('dialog', { name: /ganhe 10%/i })).toHaveCount(0);
});

test('captura abre após engajamento e mantém copy de aplicação manual', async ({ page }) => {
  await mockPromotionApi(page);
  await page.goto('/');

  await triggerDiscountDialog(page);

  const dialog = page.getByRole('dialog', { name: /ganhe 10%/i });
  await expect(dialog.getByText(/10% na primeira compra, sem valor mínimo/i)).toBeVisible();
  await expect(dialog.getByText(/inserido manualmente no carrinho ou checkout/i)).toBeVisible();
  await expect(dialog.getByRole('textbox', { name: 'E-mail' })).toHaveAttribute(
    'autocomplete',
    'email',
  );
  await expect(dialog.getByLabel(/autorizo o uso deste e-mail/i)).not.toBeChecked();
});

test('captura valida e preserva e-mail antes de submeter', async ({ page }) => {
  await mockPromotionApi(page);
  await page.goto('/');
  await triggerDiscountDialog(page);

  const dialog = page.getByRole('dialog', { name: /ganhe 10%/i });
  await dialog.getByRole('textbox', { name: 'E-mail' }).fill('cliente');
  await dialog.getByRole('button', { name: 'Solicitar cupom' }).click();

  await expect(dialog.getByText('Informe um e-mail válido.')).toBeVisible();
  await expect(dialog.getByText(/confirme a autorização específica/i)).toBeVisible();
  await expect(dialog.getByRole('textbox', { name: 'E-mail' })).toHaveValue('cliente');
  await expect(dialog.getByRole('textbox', { name: 'E-mail' })).toHaveAttribute(
    'aria-invalid',
    'true',
  );
  await expect(dialog.getByRole('textbox', { name: 'E-mail' })).toBeFocused();
});

test('captura exibe cupom de teste sem fechar automaticamente', async ({ page }) => {
  await mockPromotionApi(page);
  await page.goto('/');
  await triggerDiscountDialog(page);

  const dialog = page.getByRole('dialog', { name: /ganhe 10%/i });
  await dialog.getByRole('textbox', { name: 'E-mail' }).fill('cliente@example.com');
  await dialog.getByLabel(/autorizo o uso deste e-mail/i).check();
  await dialog.getByRole('button', { name: 'Solicitar cupom' }).click();

  await expect(dialog.getByText('JS-ABCDEFGH23456789')).toBeVisible();
  await expect(dialog).toBeVisible();
  await dialog.getByRole('button', { name: 'Agora não' }).click();
  await expect(dialog).toHaveCount(0);
});

test('captura é suprimida na mesma sessão depois de vista', async ({ page }) => {
  await mockPromotionApi(page);
  await page.goto('/');
  await triggerDiscountDialog(page);
  await page.getByRole('button', { name: 'Agora não' }).click();

  await page.reload();
  await page.evaluate(() => window.scrollTo(0, document.documentElement.scrollHeight));
  await page.waitForTimeout(800);

  await expect(page.getByRole('dialog', { name: /ganhe 10%/i })).toHaveCount(0);
});

test('captura reflow em 320 px sem overflow horizontal', async ({ page }) => {
  await page.setViewportSize({ width: 320, height: 800 });
  await mockPromotionApi(page);
  await page.goto('/');
  await triggerDiscountDialog(page);

  await expect(
    await page.evaluate(
      () => document.documentElement.scrollWidth <= document.documentElement.clientWidth,
    ),
  ).toBe(true);
});

test('scroll restaurado sem intenção nova não abre a captura', async ({ page }) => {
  await mockPromotionApi(page);
  await page.goto('/');
  await page.locator('dialog.discount-dialog').waitFor({ state: 'attached' });

  await page.evaluate(() => {
    window.scrollTo(0, document.documentElement.scrollHeight);
    window.dispatchEvent(new Event('scroll'));
  });
  await page.waitForTimeout(800);

  await expect(page.getByRole('dialog', { name: /ganhe 10%/i })).toHaveCount(0);
});

test('cópia fecha a captura e restaura o foco anterior', async ({ page, context }) => {
  await context.grantPermissions(['clipboard-read', 'clipboard-write']);
  await mockPromotionApi(page);
  await page.goto('/');

  const previousFocus = page.getByRole('link', { name: /ver produtos/i }).first();
  await previousFocus.focus();
  await triggerDiscountDialog(page);

  const dialog = page.getByRole('dialog', { name: /ganhe 10%/i });
  await dialog.getByRole('textbox', { name: 'E-mail' }).fill('cliente@example.com');
  await dialog.getByLabel(/autorizo o uso deste e-mail/i).check();
  await dialog.getByRole('button', { name: 'Solicitar cupom' }).click();
  await dialog.getByRole('button', { name: 'Copiar código' }).click();

  await expect(dialog).toHaveCount(0);
  await expect(previousFocus).toBeFocused();
});

test('Escape fecha a captura e restaura o foco', async ({ page }) => {
  await mockPromotionApi(page);
  await page.goto('/');

  const previousFocus = page.getByRole('link', { name: /ver produtos/i }).first();
  await previousFocus.focus();
  await triggerDiscountDialog(page);
  await page.keyboard.press('Escape');

  await expect(page.getByRole('dialog', { name: /ganhe 10%/i })).toHaveCount(0);
  await expect(previousFocus).toBeFocused();
});

test('fallback inline pode ser dispensado sem bloquear a página', async ({ page }) => {
  await page.addInitScript(() => {
    Object.defineProperty(HTMLDialogElement.prototype, 'showModal', {
      configurable: true,
      value: undefined,
    });
  });
  await mockPromotionApi(page);
  await page.goto('/');

  const fallback = page.locator('section.discount-inline');
  await expect(fallback).toBeVisible();
  await fallback.getByRole('button', { name: 'Agora não' }).click();
  await expect(fallback).toHaveCount(0);
  await expect(page.getByRole('main')).toBeVisible();
});

test('duplo submit no mesmo tick envia uma única solicitação', async ({ page }) => {
  let requestCount = 0;

  await page.route('**/api/promotions/first-purchase-offer', async (route) => {
    await route.fulfill({ contentType: 'application/json', body: JSON.stringify(offer) });
  });
  await page.route('**/api/promotions/first-purchase-coupons', async (route) => {
    requestCount += 1;
    await new Promise((resolve) => setTimeout(resolve, 150));
    await route.fulfill({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({
        status: 'accepted',
        delivery: 'display',
        message: 'Cupom emitido para teste.',
        request_id: 'req-test',
        coupon_code: 'JS-ABCDEFGH23456789',
      }),
    });
  });

  await page.goto('/');
  await triggerDiscountDialog(page);
  const dialog = page.getByRole('dialog', { name: /ganhe 10%/i });
  await dialog.getByRole('textbox', { name: 'E-mail' }).fill(' cliente@example.com ');
  await dialog.getByLabel(/autorizo o uso deste e-mail/i).check();
  await dialog.locator('form').evaluate((form) => {
    form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
  });

  await expect(dialog.getByText('JS-ABCDEFGH23456789')).toBeVisible();
  expect(requestCount).toBe(1);
});

test('erro 422 mapeado pelo BFF permanece associado ao campo', async ({ page }) => {
  await page.route('**/api/promotions/first-purchase-offer', async (route) => {
    await route.fulfill({ contentType: 'application/json', body: JSON.stringify(offer) });
  });
  await page.route('**/api/promotions/first-purchase-coupons', async (route) => {
    await route.fulfill({
      status: 422,
      contentType: 'application/json',
      body: JSON.stringify({
        status: 'validation_error',
        delivery: 'none',
        message: 'Os dados informados são inválidos.',
        errors: { email: ['O domínio do e-mail não é aceito.'] },
      }),
    });
  });

  await page.goto('/');
  await triggerDiscountDialog(page);
  const dialog = page.getByRole('dialog', { name: /ganhe 10%/i });
  const email = dialog.getByRole('textbox', { name: 'E-mail' });
  await email.fill('cliente@example.com');
  await dialog.getByLabel(/autorizo o uso deste e-mail/i).check();
  await dialog.getByRole('button', { name: 'Solicitar cupom' }).click();

  await expect(dialog.getByText('O domínio do e-mail não é aceito.')).toBeVisible();
  await expect(email).toHaveAttribute('aria-invalid', 'true');
  await expect(email).toBeFocused();
});

test('diálogo mantém foco, alvos de 44 px e reduced motion', async ({ page }, testInfo) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await mockPromotionApi(page);
  await page.goto('/');
  await triggerDiscountDialog(page);

  const dialog = page.getByRole('dialog', { name: /ganhe 10%/i });
  const checkbox = dialog.getByLabel(/autorizo o uso deste e-mail/i);
  const close = dialog.getByRole('button', { name: /fechar oferta/i });

  for (const control of [checkbox, close, dialog.getByRole('button', { name: 'Agora não' })]) {
    const box = await control.boundingBox();
    expect(box?.width).toBeGreaterThanOrEqual(44);
    expect(box?.height).toBeGreaterThanOrEqual(44);
  }

  for (let index = 0; index < 8; index += 1) {
    await page.keyboard.press('Tab');
    expect(await dialog.evaluate((element) => element.contains(document.activeElement))).toBe(true);
  }

  await page.screenshot({ path: testInfo.outputPath('discount-dialog-reduced-motion.png') });
});

test('modo e-mail confirma aceitação sem revelar código', async ({ page }) => {
  const emailOffer = { ...offer, delivery_mode: 'email' as const, offer_id: 'offer-email' };
  await page.route('**/api/promotions/first-purchase-offer', async (route) => {
    await route.fulfill({ contentType: 'application/json', body: JSON.stringify(emailOffer) });
  });
  await page.route('**/api/promotions/first-purchase-coupons', async (route) => {
    await route.fulfill({
      status: 202,
      contentType: 'application/json',
      body: JSON.stringify({
        status: 'accepted',
        delivery: 'email',
        message: 'Solicitação aceita para entrega por e-mail.',
        request_id: 'request-email',
      }),
    });
  });

  await page.goto('/');
  await triggerDiscountDialog(page);
  const dialog = page.getByRole('dialog', { name: /ganhe 10%/i });
  await dialog.getByRole('textbox', { name: 'E-mail' }).fill('cliente@example.com');
  await dialog.getByLabel(/autorizo o uso deste e-mail/i).check();
  await dialog.getByRole('button', { name: 'Solicitar cupom' }).click();

  await expect(dialog.getByText('Solicitação aceita para entrega por e-mail.')).toBeVisible();
  await expect(dialog.locator('code')).toHaveCount(0);
  await dialog.getByRole('button', { name: 'Entendi' }).click();
  await expect(dialog).toHaveCount(0);
});
