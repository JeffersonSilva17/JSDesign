import { expect, test } from '@playwright/test';

const headerLinks = [
  { label: 'Home', href: '/' },
  { label: 'Produtos', href: '/produtos' },
  { label: 'Categorias', href: '/categorias' },
  { label: 'Buscar', href: '/buscar' },
  { label: 'Carrinho', href: '/carrinho' },
  { label: 'Área da Cliente', href: '/entrar' },
  { label: 'Suporte', href: '/suporte' },
] as const;

const footerLinks = [
  { label: 'Políticas', href: '/politicas' },
  { label: 'Privacidade', href: '/privacidade' },
  { label: 'Termos', href: '/termos' },
  { label: 'Entrega', href: '/entrega' },
  { label: 'Trocas e reembolso', href: '/trocas-e-reembolso' },
  { label: 'Suporte', href: '/suporte' },
] as const;

test('carrega a fundação web pública', async ({ page }) => {
  await page.goto('/');

  await expect(page.getByRole('heading', { name: /Detalhes personalizados/i })).toBeVisible();
  await expect(page.getByRole('link', { name: 'JS Designs, voltar para a home' })).toBeVisible();
  await expect(page.locator('header')).toHaveCount(1);
  await expect(page.locator('main')).toHaveCount(1);
  await expect(page.locator('footer')).toHaveCount(1);
  await expect(page.getByRole('heading', { level: 1 })).toHaveCount(1);
});

test('layout público expõe cabeçalho, CTA e rodapé essenciais', async ({ page }) => {
  await page.goto('/');

  const header = page.getByRole('banner');
  await expect(header.getByRole('navigation', { name: 'Navegação principal' })).toBeVisible();

  for (const link of headerLinks) {
    const item = header.getByRole('link', { name: link.label, exact: true });

    await expect(item).toBeVisible();
    await expect(item).toHaveAttribute('href', link.href);
  }

  await expect(header.getByRole('link', { name: 'Ver produtos', exact: true })).toHaveAttribute(
    'href',
    '/produtos',
  );

  const footer = page.getByRole('contentinfo');
  await expect(footer.getByText(/contato e suporte/i)).toBeVisible();

  for (const link of footerLinks) {
    const item = footer.getByRole('link', { name: link.label, exact: true });

    await expect(item).toBeVisible();
    await expect(item).toHaveAttribute('href', link.href);
  }

  await expect(
    page.getByRole('button', { name: /whatsapp/i }).or(page.getByRole('link', { name: /whatsapp/i })),
  ).toHaveCount(0);
  await expect(
    page.locator(
      'a[href*="wa.me"], a[href*="whatsapp"], a[href*="api.whatsapp.com"], [class*="whatsapp" i], [id*="whatsapp" i]',
    ),
  ).toHaveCount(0);
});

test('layout público é operável em 320 px e por teclado', async ({ page }) => {
  await page.setViewportSize({ width: 320, height: 800 });
  await page.goto('/');

  await expect(page.getByRole('banner')).toBeVisible();
  await expect(
    page.getByRole('banner').getByRole('link', { name: 'Ver produtos', exact: true }),
  ).toBeVisible();

  const header = page.getByRole('banner');
  for (const link of headerLinks) {
    await expect(header.getByRole('link', { name: link.label, exact: true })).toBeInViewport();
  }

  await page.keyboard.press('Tab');
  await expect(page.getByRole('link', { name: 'Pular para o conteúdo principal' })).toBeFocused();

  await page.keyboard.press('Tab');
  await expect(page.getByRole('link', { name: 'JS Designs, voltar para a home' })).toBeFocused();

  for (const link of headerLinks) {
    await page.keyboard.press('Tab');
    await expect(header.getByRole('link', { name: link.label, exact: true })).toBeFocused();
  }

  await page.keyboard.press('Tab');
  await expect(header.getByRole('link', { name: 'Ver produtos', exact: true })).toBeFocused();
});

test('placeholders transacionais não são indexáveis', async ({ page }) => {
  for (const path of ['/carrinho', '/entrar']) {
    await page.goto(path);

    await expect(page.locator('meta[name="robots"]')).toHaveAttribute(
      'content',
      /noindex.*nofollow|nofollow.*noindex/,
    );
  }
});

test('rotas placeholder públicas renderizam conteúdo honesto', async ({ page }) => {
  const placeholderRoutes = [
    { path: '/produtos', heading: 'Produtos' },
    { path: '/categorias', heading: 'Categorias' },
    { path: '/buscar', heading: 'Buscar' },
    { path: '/carrinho', heading: 'Carrinho' },
    { path: '/entrar', heading: 'Área da Cliente' },
    { path: '/suporte', heading: 'Suporte' },
    { path: '/politicas', heading: 'Políticas' },
    { path: '/privacidade', heading: 'Privacidade' },
    { path: '/termos', heading: 'Termos' },
    { path: '/entrega', heading: 'Entrega' },
    { path: '/trocas-e-reembolso', heading: 'Trocas e reembolso' },
  ];

  for (const route of placeholderRoutes) {
    await page.goto(route.path);

    await expect(page.getByRole('heading', { level: 1, name: route.heading })).toBeVisible();
    await expect(page.getByText(/em preparação|será|serão|ainda não/i).first()).toBeVisible();
  }
});

test('mantém a página técnica de health renderizando', async ({ page }) => {
  await page.goto('/health');

  await expect(page.getByRole('heading', { name: 'Fundação web operacional' })).toBeVisible();
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
