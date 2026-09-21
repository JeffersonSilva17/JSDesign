import assert from 'node:assert/strict';
import test from 'node:test';
import { randomBytes } from 'node:crypto';
import { clientAddress, signClient, validClient } from '../../src/features/catalog-seo/sitemapIdentity.ts';

test('identidade deriva do socket ou proxy autorizado; assinatura expira e rejeita adulteração', () => {
  const previousKey = process.env.SITEMAP_CLIENT_KEY;
  const previousProxies = process.env.CATALOG_TRUSTED_PROXY_IPS;
  try {
    process.env.SITEMAP_CLIENT_KEY = randomBytes(32).toString('hex');
    process.env.CATALOG_TRUSTED_PROXY_IPS = '';
    assert.equal(clientAddress('::ffff:127.0.0.1', '192.0.2.99'), '127.0.0.1');
    process.env.CATALOG_TRUSTED_PROXY_IPS = '127.0.0.1';
    assert.equal(clientAddress('127.0.0.1', '192.0.2.1'), '192.0.2.1');
    assert.throws(() => clientAddress('127.0.0.1', ['192.0.2.1', '192.0.2.2']));
    assert.throws(() => clientAddress('127.0.0.1', '192.0.2.1, 192.0.2.2'));
    const signed = signClient('192.0.2.1');
    assert.equal(validClient(signed), true);
    assert.equal(validClient(signed + '0'), false);
    assert.equal(validClient(signClient('192.0.2.1', Math.floor(Date.now() / 1000) - 31)), false);
    assert.notEqual(signClient('192.0.2.2').split('.')[0], signed.split('.')[0]);
  } finally {
    if (previousKey === undefined) delete process.env.SITEMAP_CLIENT_KEY; else process.env.SITEMAP_CLIENT_KEY = previousKey;
    if (previousProxies === undefined) delete process.env.CATALOG_TRUSTED_PROXY_IPS; else process.env.CATALOG_TRUSTED_PROXY_IPS = previousProxies;
  }
});

import { parseSeoConfig } from '../../src/features/catalog-seo/seoConfig.ts';
import { listingPolicy } from '../../src/features/catalog-seo/catalogPolicy.ts';
import { parseCatalogSearchParams } from '../../src/bff/catalogParams.ts';

test('canonical de paginação e matriz de indexação preservam parsers estritos', () => {
  const listing = { data: [{}], meta: { last_page: 3, filter_labels: { category: 'Festas' } } };
  for (const [query, canonical, index] of [
    [{}, '/produtos', true], [{ page: '1' }, '/produtos', true],
    [{ page: '2' }, '/produtos?page=2', true],
    [{ category: 'festas', page: '2' }, '/produtos?category=festas&page=2', true],
    [{ modality: 'digital_ready' }, '/produtos?modality=digital_ready', false],
    [{ page: '4' }, undefined, false],
  ]) {
    const filters = parseCatalogSearchParams(query);
    assert.deepEqual(listingPolicy(filters, listing, true), { index, follow: true, canonical });
    assert.deepEqual(listingPolicy(filters, listing, false), { index: false, follow: false, canonical: undefined });
  }
  for (const query of [{ page: ['1','2'] }, { page: '01' }, { unknown: 'x' }]) assert.throws(() => parseCatalogSearchParams(query));
  assert.equal(listingPolicy({ page: 1, category: 'festas' }, { ...listing, data: [] }, true).index, false);
  assert.equal(listingPolicy({ page: 1 }, null, true).canonical, undefined);
});

test('origem explícita, sem credenciais, caminhos ou protocolo inseguro', () => {
  for (const siteUrl of [undefined, '', 'https://user:secret@example.com', 'https://example.com/path', 'https://example.com/a/..', 'https:example.com', 'https://example.com?x', 'https://example.com#x', 'http://example.com', '//example.com']) {
    assert.throws(() => parseSeoConfig(siteUrl), { message: 'Configuração SITE_URL inválida.' });
  }
  assert.deepEqual(parseSeoConfig('https://example.com/'), { origin: 'https://example.com', indexing: false });
  assert.equal(parseSeoConfig('http://127.0.0.1:3000', 'true').indexing, true);
  assert.equal(parseSeoConfig('http://[::1]:3000', 'false').indexing, false);
  assert.throws(() => parseSeoConfig('https://example.com', 'TRUE'), { message: 'Configuração SEO_INDEXING_ENABLED inválida.' });
});

import { isCatalogListingPayload, isCatalogProductEnvelope } from '../../src/bff/catalogValidation.ts';
import { detailQuery } from '../../src/features/catalog-seo/detailQuery.ts';
import { serializeJsonLd, productStructuredData } from '../../src/features/catalog-seo/catalogStructuredData.ts';
import { fetchSitemapJson } from '../../src/bff/catalogSitemapTransport.ts';
import { sitemapPage, sitemapCategories } from '../../src/bff/catalogSitemapValidation.ts';
import { editorialXml, sitemapXml } from '../../src/features/catalog-seo/sitemapXml.ts';

test('sitemap valida lotes, duplicatas, teto editorial e escape XML', () => {
  const payload = { data: [{ slug: 'festa' }], meta: { current_page: 1, per_page: 500, last_page: 1, total: 1 } };
  assert.equal(sitemapPage(payload, 1), payload);
  for (const invalid of [ { ...payload, data: [{ slug: '../private' }] }, { ...payload, data: [{ slug: 'festa', secret: 'x' }] }, { ...payload, meta: { ...payload.meta, total: 5000001 } }, { ...payload, data: [{ slug: 'festa' }, { slug: 'festa' }], meta: { ...payload.meta, total: 2 } } ]) assert.throws(() => sitemapPage(invalid, 1));
  assert.throws(() => sitemapPage(payload, 2));
  const categories = Array.from({ length: 4998 }, (_, i) => ({ slug: `categoria-${i}`, label: `Categoria ${i}` }));
  assert.equal(sitemapCategories({ data: { categories } }).length, 4998);
  assert.equal((editorialXml(categories, 'https://example.com').match(/<loc>/g) ?? []).length, 5000);
  assert.throws(() => sitemapCategories({ data: { categories: [...categories, { slug: 'extra', label: 'Extra' }] } }));
  assert.throws(() => editorialXml([...categories, { slug: 'extra', label: 'Extra' }], 'https://example.com'));
  assert.match(sitemapXml(['/produtos?category=festas&page=2'], 'https://example.com'), /&amp;page=2/);
  assert.throws(() => sitemapXml(['//evil.test'], 'https://example.com'));
  assert.throws(() => sitemapXml(['/' + 'a'.repeat(2048)], 'https://example.com'));
});

test('transporte limita bytes antes do parse em sucesso e erro e aplica deadline do corpo', async () => {
  const url = new URL('http://internal.invalid');
  const json = (body, status = 200, headers = {}) => new Response(body, { status, headers: { 'content-type': 'application/json', ...headers } });
  assert.deepEqual(await fetchSitemapJson(url, 50, { fetcher: async () => json('{"ok":true}') }), { ok: true });
  for (const response of [json('x'.repeat(51)), json('{}', 200, { 'content-length': '999' }), json('x'.repeat(51), 200, { 'content-length': '1' }), json('{'), new Response('{}')]) {
    await assert.rejects(() => fetchSitemapJson(url, 50, { fetcher: async () => response }), { status: 503 });
  }
  for (const [status, child, expected] of [[404, true, 404], [404, false, 503], [422, true, 503], [500, false, 503]]) {
    await assert.rejects(() => fetchSitemapJson(url, 50, { productChild: child, fetcher: async () => json('{}', status) }), { status: expected });
  }
  for (const [retry, expected] of [['15', '15'], ['0', '60'], ['3601', '60'], ['date', '60']]) {
    await assert.rejects(() => fetchSitemapJson(url, 50, { fetcher: async () => json('{}', 429, { 'retry-after': retry }) }), { status: 429, retryAfter: expected });
  }
  await assert.rejects(() => fetchSitemapJson(url, 50, { fetcher: async () => new Response('', { status: 429, headers: { 'retry-after': '19' } }) }), { status: 429, retryAfter: '19' });
  await assert.rejects(() => fetchSitemapJson(url, 50, { productChild: true, fetcher: async () => new Response('', { status: 404 }) }), { status: 404 });
  let cancelled = false;
  await assert.rejects(() => fetchSitemapJson(url, 50, { timeoutMs: 20, fetcher: async () => json(new ReadableStream({ start(c) { c.enqueue(new TextEncoder().encode('{')); }, cancel() { cancelled = true; } })) }), { status: 503 });
  assert.equal(cancelled, true);
  await assert.rejects(() => fetchSitemapJson(url, 50, { fetcher: async () => json(new ReadableStream({ start(c) { c.error(new Error('private')); } })) }), { status: 503 });
});

test('JSON-LD conserva Unicode e impede fechamento do script sem publicar campos extras', () => {
  const product = { name: 'Celebração </script><script>alert(1)</script>', description: 'Descrição pública', slug: 'festa', primary_image: null, price_minor: 100, private_notes: 'secret' };
  const data = productStructuredData(product, 'https://example.com');
  const text = serializeJsonLd(data);
  assert.ok(!text.includes('<'));
  assert.equal(JSON.parse(text).name, product.name);
  assert.deepEqual(Object.keys(data).sort(), ['@context', '@type', 'description', 'name', 'url'].sort());
  assert.ok(!text.includes('secret'));
});
test('listagem rejeita identidade e paginação de outra consulta', () => {
 const empty = { data: [], meta: { current_page: 1, per_page: 12, last_page: 1, total: 0, applied_filters: {}, filter_labels: {} } };
 assert.equal(isCatalogListingPayload(empty, { page: 2 }), true);
 assert.equal(isCatalogListingPayload(empty, { page: 1, category: 'festas' }), false);
 assert.equal(isCatalogListingPayload({...empty, meta: {...empty.meta, total: 12}}, { page: 1 }), false);
 assert.equal(isCatalogListingPayload(empty, { page: 1 }), true);
});

test('vazio legado preserva filtros exatos e não inventa labels', () => {
  const value = { data: [], meta: { current_page: 1, per_page: 12, last_page: 1, total: 0, applied_filters: { category: 'sem-produtos' }, filter_labels: {} } };
  assert.equal(isCatalogListingPayload(value, { page: 99, category: 'sem-produtos' }), true);
  assert.equal(isCatalogListingPayload(value, { page: 99, category: 'outra' }), false);
  assert.equal(isCatalogListingPayload({ ...value, meta: { ...value.meta, total: 1 } }, { page: 99, category: 'sem-produtos' }), false);
  assert.equal(isCatalogListingPayload({ ...value, meta: { ...value.meta, filter_labels: { occasion: 'injetada' } } }, { page: 99, category: 'sem-produtos' }), false);
});

test('identidade de produto, labels de categoria, modalidade e IDs duplicados', () => {
  const item = { id: '10d6c281-33b2-4d97-857e-29ffccec8ae9', slug: 'festa', name: 'Festa', description_excerpt: 'Público', category: { slug: 'festas', label: 'Festas' }, modality: 'digital_ready', price_minor: 100, currency: 'EUR', availability: 'available', delivery_type: 'digital', production_lead_time_days: null, is_immediate_delivery: true, primary_image: null, taxonomy: [], compatibility_excerpt: null };
  const listing = { data: [item], meta: { current_page: 1, per_page: 12, last_page: 1, total: 1, applied_filters: { category: 'festas' }, filter_labels: { category: 'Festas' } } };
  const filters = { page: 1, category: 'festas' };
  assert.equal(isCatalogListingPayload(listing, filters), true);
  assert.equal(isCatalogListingPayload({ ...listing, meta: { ...listing.meta, filter_labels: { category: 'Outra' } } }, filters), false);
  assert.equal(isCatalogListingPayload({ ...listing, data: [item, item], meta: { ...listing.meta, total: 2 } }, filters), false);
  assert.equal(isCatalogListingPayload({ ...listing, meta: { ...listing.meta, applied_filters: { modality: 'digital_ready' }, filter_labels: { modality: 'Outra' } } }, { page: 1, modality: 'digital_ready' }), false);
  const { description_excerpt, compatibility_excerpt, ...base } = item;
  const detail = { data: { ...base, description: description_excerpt, compatibility: compatibility_excerpt } };
  assert.equal(isCatalogProductEnvelope(detail, 'festa'), true);
  assert.equal(isCatalogProductEnvelope(detail, 'outro'), false);
});

test('query do detalhe permite somente retorno seguro e único', () => {
  for (const query of [{ unknown: 'x' }, { return_to: '' }, { return_to: ['a', 'b'] }, { return_to: '//evil.test' }, { return_to: '/produtos#x' }, { return_to: '/buscar?q=a&q=b' }, { return_to: '/private/../buscar?q=convite' }, { return_to: '/buscar?q=convite%' }]) {
    assert.deepEqual(detailQuery(query), { valid: false, returnHref: '/produtos' });
  }
  assert.deepEqual(detailQuery({ return_to: '/buscar?q=convite' }), { valid: true, returnHref: '/buscar?q=convite' });
  assert.equal(detailQuery({ return_to: '/produtos' }).valid, true);
  assert.equal(detailQuery({}).valid, true);
});
