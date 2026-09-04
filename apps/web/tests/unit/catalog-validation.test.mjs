import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

import {
  isCatalogListingPayload,
  isCatalogSearchPayload,
  isSafeCatalogImagePath,
} from '../../src/bff/catalogValidation.ts';
import { fetchCatalogJson } from '../../src/bff/catalogTransport.ts';
import { parsePublicSearchParams, publicSearchHref } from '../../src/bff/catalogSearchParams.ts';

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

function searchPayload() {
  return {
    data: {
      exact_groups: [{ category: { slug: 'festas', label: 'Festas' }, items: [product()] }],
      similar: [],
      suggestions: [{ label: 'Aniversário', href: '/buscar?q=Anivers%C3%A1rio' }],
      intent: { type: 'generic', preserved_term: 'convite', handoff_href: null },
    },
    meta: { query: 'convite', current_page: 1, per_page: 12, last_page: 1, total: 1, total_exact: 1, total_similar: 0 },
  };
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

test('aceita envelope fechado de busca e rejeita campos internos ou extras', () => {
  assert.equal(isCatalogSearchPayload(searchPayload()), true);
  const internal = structuredClone(searchPayload());
  internal.data.similar = [{ ...product(), score: 0.9 }];
  internal.meta.total = 2;
  internal.meta.total_similar = 1;
  assert.equal(isCatalogSearchPayload(internal), false);
  assert.equal(isCatalogSearchPayload({ ...searchPayload(), debug: true }), false);
  const character = structuredClone(searchPayload());
  character.data.exact_groups[0].items[0].taxonomy = [{ type: 'character', key: 'x', label: 'X' }];
  assert.equal(isCatalogSearchPayload(character), false);
});

test('rejeita duplicações, totais inconsistentes e hrefs inseguros na busca', () => {
  const duplicate = structuredClone(searchPayload());
  duplicate.data.similar = [product()];
  duplicate.meta.total = 2;
  duplicate.meta.total_similar = 1;
  assert.equal(isCatalogSearchPayload(duplicate), false);

  const badTotal = structuredClone(searchPayload());
  badTotal.meta.total = 2;
  assert.equal(isCatalogSearchPayload(badTotal), false);

  const tooManySuggestions = structuredClone(searchPayload());
  tooManySuggestions.data.suggestions = Array.from({ length: 7 }, (_, index) => ({ label: `Tema ${index}`, href: `/buscar?q=${index}` }));
  assert.equal(isCatalogSearchPayload(tooManySuggestions), false);

  for (const href of ['https://evil.example/', '//evil.example/', '/admin', '/buscar?character=x', '/buscar?q=x&q=y']) {
    const unsafe = structuredClone(searchPayload());
    unsafe.data.suggestions[0].href = href;
    assert.equal(isCatalogSearchPayload(unsafe), false);
  }

  const outOfRange = structuredClone(searchPayload());
  outOfRange.data.exact_groups = [];
  outOfRange.meta.current_page = 2;
  assert.equal(isCatalogSearchPayload(outOfRange), true);
});

test('rejeita inconsistências semânticas entre query, grupos, classes e links', () => {
  const wrongTerm = structuredClone(searchPayload());
  wrongTerm.data.intent.preserved_term = 'outro termo';
  assert.equal(isCatalogSearchPayload(wrongTerm), false);

  const wrongCategory = structuredClone(searchPayload());
  wrongCategory.data.exact_groups[0].items[0].category = { slug: 'outra', label: 'Outra' };
  assert.equal(isCatalogSearchPayload(wrongCategory), false);

  const wrongSplit = structuredClone(searchPayload());
  wrongSplit.meta.total_exact = 0;
  wrongSplit.meta.total_similar = 1;
  assert.equal(isCatalogSearchPayload(wrongSplit), false);

  const wrongSuggestion = structuredClone(searchPayload());
  wrongSuggestion.data.suggestions[0].href = '/buscar?q=Outro';
  assert.equal(isCatalogSearchPayload(wrongSuggestion), false);

  const invalidSuggestionLabel = structuredClone(searchPayload());
  invalidSuggestionLabel.data.suggestions[0] = { label: 'x'.repeat(121), href: `/buscar?q=${'x'.repeat(121)}` };
  assert.equal(isCatalogSearchPayload(invalidSuggestionLabel), false);

  const invitationWithoutHandoff = structuredClone(searchPayload());
  invitationWithoutHandoff.data.intent.type = 'invitation';
  assert.equal(isCatalogSearchPayload(invitationWithoutHandoff), false);

  const wrongHandoffTerm = structuredClone(searchPayload());
  wrongHandoffTerm.data.intent = { type: 'invitation', preserved_term: 'convite', handoff_href: '/produtos?modality=digital_personalized#busca=outro' };
  assert.equal(isCatalogSearchPayload(wrongHandoffTerm), false);
});

test('parser de busca normaliza espaços e href preserva termo e página', () => {
  const criteria = parsePublicSearchParams({ q: '  Convite\u00a0  Ágil ', page: '2' });
  assert.deepEqual(criteria, { query: 'Convite Ágil', page: 2 });
  assert.equal(publicSearchHref(criteria), '/buscar?q=Convite+%C3%81gil&page=2');
  assert.deepEqual(parsePublicSearchParams({}), { query: null, page: 1 });
  const source = readFileSync(new URL('../../src/features/catalog-search/SearchForm.tsx', import.meta.url), 'utf8');
  assert.doesNotMatch(source, /maxLength=/);
});

test('validador de busca rejeita payload valido que pertence a outra requisicao', () => {
  assert.equal(isCatalogSearchPayload(searchPayload(), { query: 'convite', page: 1, perPage: 12 }), true);
  assert.equal(isCatalogSearchPayload(searchPayload(), { query: 'outro', page: 1, perPage: 12 }), false);
  assert.equal(isCatalogSearchPayload(searchPayload(), { query: 'convite', page: 2, perPage: 12 }), false);
  assert.equal(isCatalogSearchPayload(searchPayload(), { query: 'convite', page: 1, perPage: 24 }), false);
});

test('parser de busca rejeita arrays, controles, limites e paginação não canônica', () => {
  for (const input of [
    { q: ['convite'] }, { q: 'a' }, { q: 'convite\nsecreto' }, { q: 'convi\u200bte' },
    { q: 'convite', page: '01' }, { q: 'convite', page: '1001' }, { q: 'convite', extra: 'x' }, { page: '2' },
  ]) assert.throws(() => parsePublicSearchParams(input), { kind: 'invalid-filter' });
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

test('contrato OpenAPI fecha hrefs de busca e envelopes de erro', () => {
  const openApi = readFileSync(new URL('../../../../packages/contracts/catalog-public-v1.openapi.yaml', import.meta.url), 'utf8');
  assert.match(openApi, /pattern: '\^\/buscar\\\?q=\[\^#&\]\+\$'/);
  assert.match(openApi, /pattern: '\^\/produtos\\\?modality=digital_personalized#busca=\[\^#&\]\+\$'/);
  assert.match(openApi, /SimpleErrorEnvelope:[\s\S]*?additionalProperties: false/);
  assert.match(openApi, /ValidationErrorEnvelope:[\s\S]*?additionalProperties: false/);
  assert.match(openApi, /RateLimitErrorEnvelope:[\s\S]*?additionalProperties: false/);
});
