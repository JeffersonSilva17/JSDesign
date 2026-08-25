import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

import {
  isCatalogListingPayload,
  isSafeCatalogImagePath,
} from '../../src/bff/catalogValidation.ts';
import { fetchCatalogJson } from '../../src/bff/catalogTransport.ts';

function product() {
  return {
    id: '10d6c281-33b2-4d97-857e-29ffccec8ae9', slug: 'convite-digital', name: 'Convite digital',
    description_excerpt: 'Descrição pública', category: { slug: 'festas', label: 'Festas' },
    modality: 'digital_ready', price_minor: 1290, currency: 'EUR', availability: 'available',
    delivery_type: 'digital', production_lead_time_days: null, is_immediate_delivery: true,
    primary_image: { url: '/catalog-e2e-product.svg', alt_text: 'Produto digital' }, taxonomy: [],
    compatibility_excerpt: 'Silhouette Studio',
  };
}

function listing(item = product()) {
  return { data: [item], meta: { current_page: 1, per_page: 12, last_page: 1, total: 1, applied_filters: {}, filter_labels: {} } };
}

test('aceita o contrato público exato e rejeita campo administrativo', () => {
  assert.equal(isCatalogListingPayload(listing()), true);
  assert.equal(isCatalogListingPayload(listing({ ...product(), status: 'published' })), false);
  assert.equal(isCatalogListingPayload(listing({ ...product(), storage_reference: 'opaque' })), false);
  assert.equal(isCatalogListingPayload({ ...listing(), meta: { ...listing().meta, per_page: 48 } }), true);
});

test('rejeita payload malformado, preço inventado e URL de imagem insegura', () => {
  assert.equal(isCatalogListingPayload({ data: [], meta: [] }), false);
  assert.equal(isCatalogListingPayload(listing({ ...product(), currency: 'USD' })), false);
  assert.equal(isCatalogListingPayload(listing({ ...product(), primary_image: { url: 'https://unsafe.example/a.jpg', alt_text: 'x' } })), false);
  for (const path of ['//unsafe.example/a.jpg', '/media/../secret', '/media\\a.jpg', '/media/a.jpg#x', '/media/a.jpg?v=1']) {
    assert.equal(isSafeCatalogImagePath(path), false);
  }
  assert.equal(isSafeCatalogImagePath('/missing-public-file.jpg'), false);
  assert.equal(isCatalogListingPayload(listing({ ...product(), id: 'not-a-uuid' })), false);
  assert.equal(isCatalogListingPayload(listing({ ...product(), slug: 'x'.repeat(181) })), false);
  assert.equal(isCatalogListingPayload(listing({ ...product(), category: { slug: '../segredo', label: 'Festas' } })), false);
  assert.equal(isCatalogListingPayload(listing({ ...product(), description_excerpt: 'x'.repeat(241) })), false);
  assert.equal(isCatalogListingPayload(listing({ ...product(), compatibility_excerpt: 'x'.repeat(121) })), false);
});

test('cliente server-only fixa timeout e no-store', async () => {
  let observed;
  const payload = await fetchCatalogJson(new URL('http://internal.invalid/catalog'), {
    timeoutMs: 25,
    fetcher: async (url, init) => {
      observed = { url: url.toString(), init };
      return new Response(JSON.stringify({ ok: true }), { status: 200, headers: { 'content-type': 'application/json' } });
    },
  });
  assert.deepEqual(payload, { ok: true });
  assert.equal(observed.url, 'http://internal.invalid/catalog');
  assert.equal(observed.init.cache, 'no-store');
  assert.ok(observed.init.signal instanceof AbortSignal);
  const source = readFileSync(new URL('../../src/bff/catalogApi.ts', import.meta.url), 'utf8');
  assert.match(source, /import 'server-only'/);
});

test('transporte distingue 404 real, falha upstream, timeout e JSON inválido', async () => {
  await assert.rejects(() => fetchCatalogJson(new URL('http://internal.invalid/x'), { detail: true, fetcher: async () => new Response('{}', { status: 404 }) }), { kind: 'not-found' });
  await assert.rejects(() => fetchCatalogJson(new URL('http://internal.invalid/x'), { fetcher: async () => new Response('{}', { status: 503 }) }), { kind: 'upstream-unavailable' });
  await assert.rejects(() => fetchCatalogJson(new URL('http://internal.invalid/x'), { fetcher: async () => { throw new DOMException('timeout', 'AbortError'); } }), { kind: 'upstream-unavailable' });
  await assert.rejects(() => fetchCatalogJson(new URL('http://internal.invalid/x'), { fetcher: async () => new Response('not-json', { status: 200 }) }), { kind: 'invalid-payload' });
});
