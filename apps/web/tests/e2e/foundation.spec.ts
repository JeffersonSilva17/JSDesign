import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

import { expect, test } from '@playwright/test';

import { publicContent } from '../../src/features/public-store/publicLayoutContent';

const mojibakePattern = /(?:\u00c3[\u0080-\u00bf]|\u00c2[\u0080-\u00bf]|\u00e2\u20ac[\u0152\u009d\u02dc\u2122]|\ufffd)/;

function expectNoMojibake(content: string) {
  expect(content).not.toMatch(mojibakePattern);
}

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

const mostWantedCards = [
  {
    title: 'Kit Festa Fazendinha',
    modality: 'Produto físico personalizado',
    cta: 'Ver opções: Kit Festa Fazendinha',
    href: '/produtos',
  },
  {
    title: 'Convite Jardim Dourado',
    modality: 'Convite digital personalizado',
    cta: 'Ver modelos: Convite Jardim Dourado',
    href: '/buscar',
  },
  {
    title: 'Topo Céu Encantado',
    modality: 'Produto digital',
    cta: 'Ver arquivo: Topo Céu Encantado',
    href: '/produtos',
  },
] as const;

test('carrega a fundação web pública', async ({ page }) => {
  await page.goto('/');

  await expect(page.locator('html')).toHaveAttribute('lang', 'pt-BR');
  await expect(page.getByRole('heading', { name: /Detalhes personalizados/i })).toBeVisible();
  await expect(page.getByRole('link', { name: 'JS Designs, voltar para a home' })).toBeVisible();
  await expect(page.locator('header')).toHaveCount(1);
  await expect(page.locator('main')).toHaveCount(1);
  await expect(page.locator('footer')).toHaveCount(1);
  await expect(page.getByRole('heading', { level: 1 })).toHaveCount(1);
});

test('conteúdo público usa português do Brasil em UTF-8 sem mojibake', async ({ page }) => {
  await page.goto('/');

  const visibleText = await page.locator('body').innerText();

  for (const expectedCopy of publicContent.qualityCopy) {
    expect(visibleText).toContain(expectedCopy);
  }

  expectNoMojibake(visibleText);
  expectNoMojibake(await page.locator('head').innerHTML());
});

test('home diferencia modalidades sem prometer compra real', async ({ page }) => {
  await page.goto('/');

  const visibleText = await page.locator('body').innerText();
  expect(visibleText).toContain('lembrancinhas físicas personalizadas');
  expect(visibleText).toContain('convites digitais personalizados');
  expect(visibleText).toContain('Produto Digital Pronto');
  expect(visibleText).toContain('Silhouette Studio');
  expect(visibleText).toMatch(/convites digitais personalizados não são entrega imediata/i);
  expect(visibleText).toMatch(/prévia e aprovação/i);
  expect(visibleText).toMatch(/sem personalização/i);
  expect(visibleText).toMatch(/quando a compra estiver disponível.*download imediato/i);

  await expect(
    page.getByText(/adicionar ao carrinho|comprar agora|finalizar compra|checkout/i),
  ).toHaveCount(0);
});

test('home apresenta a curadoria editorial mais procurada na ordem definida', async ({ page }) => {
  await page.goto('/');

  const section = page.getByRole('region', { name: 'Mais procurados' });
  await expect(section).toBeVisible();
  await expect(section.getByRole('heading', { level: 2, name: 'Mais procurados' })).toBeVisible();
  await expect(section.getByText(/seleção editorial.*modalidades/i)).toBeVisible();
  await expect(section.getByRole('heading', { level: 3 })).toHaveText(
    mostWantedCards.map(({ title }) => title),
  );

  for (const card of mostWantedCards) {
    const article = section.getByRole('article', { name: card.title });
    const link = section.getByRole('link', { name: card.cta, exact: true });

    await expect(article).toBeVisible();
    await expect(article.getByText(card.modality, { exact: true })).toBeVisible();
    await expect(link).toHaveAttribute('href', card.href);

    const box = await link.boundingBox();
    expect(box?.height).toBeGreaterThanOrEqual(44);
    expect(box?.width).toBeGreaterThanOrEqual(44);
  }

  await expect(section.locator('.most-wanted-card')).toHaveCount(3);
  await expect(section.getByRole('link')).toHaveCount(3);
  await expect(section.locator('.most-wanted-card__visual[aria-hidden="true"]')).toHaveCount(3);
});

test('home mantém descoberta honesta, indexável e sem linguagem transacional simulada', async ({ page }) => {
  await page.goto('/');

  await expect(page).toHaveTitle('Loja online de personalizados físicos e digitais | JS Designs');
  await expect(page.locator('meta[name="description"]')).toHaveAttribute(
    'content',
    publicContent.metadata.home.description,
  );
  await expect(page.locator('meta[name="robots"]')).toHaveCount(0);

  const mainText = await page.getByRole('main').innerText();
  expect(mainText).toMatch(/produto digital/i);
  expect(mainText).toContain('Silhouette Studio');
  expect(mainText).toMatch(/quando a compra estiver disponível.*download imediato.*pagamento confirmado/i);
  expect(mainText).not.toMatch(
    /€|R\$|\b(?:USD|CHF|BRL)\b|\b\d+[.,]\d{2}\b|\b\d+x\b|\b\d+\s*%|\bgrátis\b|\ba partir de\b|\bdesde\b|\bpromoção\b|\bmais vendido\b|\bcomprar agora\b/i,
  );
});

test('mais procurados aparece na primeira viewport mobile sem overflow', async ({ page }, testInfo) => {
  await page.setViewportSize({ width: 320, height: 800 });
  await page.goto('/');

  const heading = page.getByRole('heading', { level: 2, name: 'Mais procurados' });
  const entryHeading = page.getByRole('heading', { level: 2, name: 'Continue explorando' });
  const headingBox = await heading.boundingBox();
  const entryBox = await entryHeading.boundingBox();

  expect(await page.evaluate(() => window.scrollY)).toBe(0);
  await expect(page.locator('.hero + .most-wanted')).toHaveCount(1);
  expect(headingBox).not.toBeNull();
  expect(headingBox?.y).toBeGreaterThanOrEqual(0);
  expect((headingBox?.y ?? 801) + (headingBox?.height ?? 0)).toBeLessThanOrEqual(800);
  expect(entryBox?.y ?? 0).toBeGreaterThan(headingBox?.y ?? Number.POSITIVE_INFINITY);
  expect(
    await page.evaluate(
      () => document.documentElement.scrollWidth <= document.documentElement.clientWidth,
    ),
  ).toBe(true);

  await testInfo.attach('home-mobile-320x800', {
    body: await page.screenshot(),
    contentType: 'image/png',
  });
});

test('home e curadoria permanecem Server Components', () => {
  const pageSource = readFileSync(resolve('src/app/(public)/page.tsx'), 'utf8');

  expect(pageSource).not.toMatch(/^\s*['"]use client['"];?/m);
  expect(pageSource).not.toContain('modalityCards');
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

    const visibleText = await page.locator('body').innerText();
    expectNoMojibake(visibleText);
    expectNoMojibake(await page.locator('head').innerHTML());
  }
});

test('metadata pública do catálogo permanece em português do Brasil', async ({ page }) => {
  await page.goto('/produtos');

  await expect(page).toHaveTitle('Produtos | JS Designs');
  await expect(page.locator('meta[name="description"]')).toHaveAttribute(
    'content',
    publicContent.catalog.metadata.products.description,
  );
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
