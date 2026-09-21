import { spawn, spawnSync, type ChildProcess } from 'node:child_process';
import { createServer, type Server } from 'node:http';

import { expect, test } from '@playwright/test';
import { validClient } from '../../src/features/catalog-seo/sitemapIdentity';

const enabled = process.env.SEO_INDEXING_ENABLED === 'true';
const base = 'http://127.0.0.1:3013';
let next: ChildProcess;
let api: Server;
let mode = 'success';
const counts = new Map<string, number>();
const identities: string[] = [];
const product = {
  id: '10d6c281-33b2-4d97-857e-29ffccec8ae9', slug: 'publico', name: 'Celebração </script><script>alert(1)</script>',
  description: 'Descrição pública', category: { slug: 'festas', label: 'Festas' }, modality: 'digital_ready', price_minor: 100,
  currency: 'EUR', availability: 'unavailable', delivery_type: 'digital', production_lead_time_days: null,
  is_immediate_delivery: true, primary_image: null, taxonomy: [], compatibility: null,
};

test.beforeAll(async () => {
  api = createServer((req, res) => {
    const url = new URL(req.url ?? '/', 'http://localhost');
    if (url.pathname.includes('sitemap')) identities.push(String(req.headers['x-catalog-client'] ?? ''));
    counts.set(url.pathname, (counts.get(url.pathname) ?? 0) + 1);
    res.setHeader('content-type', 'application/json');
    if (mode === 'failure') { res.statusCode = 503; res.end('{"message":"private SQL"}'); return; }
    if (mode === 'rate') { res.statusCode = 429; res.setHeader('Retry-After', '19'); res.end('{}'); return; }
    if (url.pathname.endsWith('/publico')) {
      if (mode === 'withdrawn') { res.statusCode = 404; res.end('{}'); return; }
      res.end(JSON.stringify({ data: { ...product, ...(mode === 'wrong-product' ? { slug: 'outro', name: 'IDENTIDADE ERRADA' } : {}) } })); return;
    }
    if (url.pathname.endsWith('/sitemap-facets')) {
      res.end(JSON.stringify({ data: { categories: mode === 'bad-facets' ? [{ slug: '../private', label: 'Secret' }] : [{ slug: 'festas', label: 'Festas' }] } })); return;
    }
    if (url.pathname.endsWith('/sitemap')) {
      const page = Number(url.searchParams.get('page'));
      const total = mode === 'withdrawn' ? 500 : 501;
      if (page > Math.ceil(total / 500)) { res.statusCode = 404; res.end('{}'); return; }
      const data = Array.from({ length: page === 1 ? 500 : 1 }, (_, index) => ({ slug: `produto-${(page - 1) * 500 + index}` }));
      res.end(JSON.stringify({ data, meta: { current_page: page, per_page: 500, last_page: Math.ceil(total / 500), total } })); return;
    }
    if (url.pathname.endsWith('/facets')) {
      res.end(JSON.stringify({ data: { categories: [{ slug: 'festas', label: 'Festas' }], occasions: [], modalities: [] } })); return;
    }
    const { description, compatibility, ...common } = product;
    res.end(JSON.stringify({ data: [{ ...common, description_excerpt: description, compatibility_excerpt: compatibility }], meta: { current_page: 1, per_page: 12, last_page: 1, total: 1, applied_filters: mode === 'wrong-listing' ? { category: 'outra' } : {}, filter_labels: mode === 'wrong-listing' ? { category: 'Outra' } : {} } }));
  });
  await new Promise<void>((resolve) => api.listen(8013, '127.0.0.1', resolve));
  next = spawn(process.execPath, ['scripts/server.mjs'], {
    env: { ...process.env, PORT: '3013', SITE_URL: 'http://127.0.0.1:3000', SEO_INDEXING_ENABLED: String(enabled), API_INTERNAL_URL: 'http://127.0.0.1:8013' }, stdio: 'ignore', windowsHide: true,
  });
  for (let attempt = 0; attempt < 80; attempt++) {
    // base is the fixed loopback origin of this isolated mock; no external traffic or credentials.
    // nosemgrep: typescript.react.security.react-insecure-request.react-insecure-request
    try { await fetch(`${base}/health`); return; } catch { await new Promise((resolve) => setTimeout(resolve, 100)); }
  }
  throw new Error('Servidor SEO de teste indisponível.');
});

test.afterAll(async () => {
  if (next?.pid) {
    if (process.platform === 'win32') spawnSync('taskkill.exe', ['/pid', String(next.pid), '/t', '/f'], { stdio: 'ignore', windowsHide: true });
    else next.kill();
  }
  api?.closeAllConnections();
  await new Promise<void>((resolve) => api ? api.close(() => resolve()) : resolve());
});

test.beforeEach(() => { mode = 'success'; counts.clear(); identities.length = 0; });

test('ingress sobrescreve identidade e ignora IPs alegados pelo visitante', async ({ request }) => {
  if (!enabled) return;
  for (const ip of ['192.0.2.1', '192.0.2.2']) {
    const response = await request.get(`${base}/sitemap-catalogo.xml?page=1`, { headers: { 'x-catalog-client': 'forged', 'x-real-ip': ip, 'x-forwarded-for': ip } });
    expect(response.status()).toBe(200);
  }
  expect(identities).toHaveLength(2);
  expect(identities.every((token) => validClient(token))).toBe(true);
  expect(identities[0].split('.')[0]).toBe(identities[1].split('.')[0]);
});

test('memoização por requisição em sucesso e falha; identidade e retirada', async ({ request, page }) => {
  const get = (path: string) => request.get(`${base}${path}`, { headers: { 'user-agent': 'Twitterbot/1.0' } });
  const first = await get('/produtos/publico');
  expect(first.status()).toBe(200);
  expect(counts.get('/api/v1/catalog/products/publico')).toBe(1);
  const html = await first.text();
  if (enabled) {
    const script = html.match(/<script type="application\/ld\+json">(.*?)<\/script>/)?.[1];
    expect(script).toBeTruthy();
    expect(JSON.parse(script!).name).toBe(product.name);
    expect(script).not.toContain('<');
    expect(script).not.toContain('Offer');
    expect(script).not.toContain('image');
  }
  mode = 'wrong-product';
  const wrong = await (await get('/produtos/publico')).text();
  expect(wrong).not.toContain('IDENTIDADE ERRADA');
  expect(wrong).toContain('noindex');
  expect(counts.get('/api/v1/catalog/products/publico')).toBe(2);
  mode = 'failure';
  const failed = await (await get('/produtos/publico')).text();
  expect(failed).toContain('Catálogo temporariamente indisponível');
  expect(failed).not.toContain('private SQL');
  expect(counts.get('/api/v1/catalog/products/publico')).toBe(3);
  mode = 'withdrawn';
  expect((await get('/produtos/publico')).status()).toBe(404);
  expect(counts.get('/api/v1/catalog/products/publico')).toBe(4);
  const browserResponse = await page.goto(`${base}/produtos/publico`);
  expect([200, 404]).toContain(browserResponse?.status());
  await expect(page.locator('meta[name="robots"]').first()).toHaveAttribute('content', /noindex/);
});

test('listagem e facetas compartilham leitura e rejeitam outra consulta', async ({ request }) => {
  const get = () => request.get(`${base}/produtos`, { headers: { 'user-agent': 'Twitterbot/1.0' } });
  await get();
  expect(counts.get('/api/v1/catalog/products')).toBe(1);
  expect(counts.get('/api/v1/catalog/facets')).toBe(1);
  mode = 'wrong-listing';
  const html = await (await get()).text();
  expect(html).toContain('Catálogo temporariamente indisponível');
  expect(html).not.toContain('application/ld+json');
  expect(counts.get('/api/v1/catalog/products')).toBe(2);
  mode = 'failure';
  await get();
  expect(counts.get('/api/v1/catalog/products')).toBe(3);
});

test('índice não anuncia editorial inválido; todos os lotes e matriz HTTP', async ({ request }) => {
  const get = (path: string) => request.get(`${base}${path}`);
  if (!enabled) {
    for (const path of ['/sitemap.xml', '/sitemap-editorial.xml', '/sitemap-catalogo.xml?page=1']) expect((await get(path)).status()).toBe(404);
    expect(counts.size).toBe(0); return;
  }
  const index = await get('/sitemap.xml');
  expect(index.status()).toBe(200);
  expect(counts.get('/api/v1/catalog/sitemap')).toBe(1);
  const children = [...(await index.text()).matchAll(/<loc>(.*?)<\/loc>/g)].map((match) => new URL(match[1]));
  expect(children).toHaveLength(3);
  const slugs: string[] = [];
  for (const child of children) {
    const response = await get(child.pathname + child.search);
    expect(response.status()).toBe(200);
    if (child.pathname.includes('catalogo')) slugs.push(...[...(await response.text()).matchAll(/<loc>(.*?)<\/loc>/g)].map((match) => match[1]));
  }
  expect(new Set(slugs).size).toBe(501);
  mode = 'withdrawn';
  expect((await get('/sitemap-catalogo.xml?page=2')).status()).toBe(404);
  mode = 'bad-facets';
  for (const path of ['/sitemap.xml', '/sitemap-editorial.xml']) expect((await get(path)).status()).toBe(503);
  mode = 'rate';
  const rate = await get('/sitemap.xml');
  expect(rate.status()).toBe(429); expect(rate.headers()['retry-after']).toBe('19');
  mode = 'failure';
  const failed = await get('/sitemap.xml');
  expect(failed.status()).toBe(503); expect(failed.headers()['retry-after']).toBe('60');
  expect(await failed.text()).not.toContain('private SQL');
  counts.clear();
  expect((await get('/sitemap.xml?bad=1')).status()).toBe(422);
  expect(counts.size).toBe(0);
});
