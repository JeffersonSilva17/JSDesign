import { existsSync } from 'node:fs';
import { join, normalize, sep } from 'node:path';

const modalities = ['physical_personalized', 'digital_personalized', 'digital_ready'] as const;
const slugPattern = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
const uuidPattern = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
const publicRoot = normalize(join(process.cwd(), 'public'));
type ExpectedSearchCriteria = Readonly<{ query: string; page: number; perPage: number }>;

export function isCatalogListingPayload(value: unknown): boolean {
  if (!isRecord(value) || !hasOnlyKeys(value, ['data', 'meta']) || !Array.isArray(value.data) || !isRecord(value.meta)) return false;
  const meta = value.meta;
  return value.data.every((item) => isProduct(item, false)) && hasOnlyKeys(meta, ['current_page', 'per_page', 'last_page', 'total', 'applied_filters', 'filter_labels']) &&
    positiveInteger(meta.current_page) && positiveInteger(meta.per_page) && (meta.per_page as number) <= 48 && positiveInteger(meta.last_page) &&
    nonNegativeInteger(meta.total) && isSafeFilterMap(meta.applied_filters) && isSafeFilterMap(meta.filter_labels);
}

export function isCatalogFacetsEnvelope(value: unknown): boolean {
  if (!isRecord(value) || !hasOnlyKeys(value, ['data']) || !isRecord(value.data)) return false;
  const facets = value.data;
  if (!hasOnlyKeys(facets, ['categories', 'occasions', 'modalities'])) return false;
  return Array.isArray(facets.categories) && facets.categories.every(isCategory) &&
    Array.isArray(facets.occasions) && facets.occasions.every((item) => namedKey(item, 'key')) &&
    Array.isArray(facets.modalities) && facets.modalities.every((item) => isRecord(item) && hasOnlyKeys(item, ['value', 'label']) && modalities.includes(item.value as (typeof modalities)[number]) && nonEmpty(item.label));
}

export function isCatalogProductEnvelope(value: unknown): boolean {
  return isRecord(value) && hasOnlyKeys(value, ['data']) && isProduct(value.data, true);
}

export function isCatalogSearchPayload(value: unknown, expected?: ExpectedSearchCriteria): boolean {
  if (!isRecord(value) || !hasOnlyKeys(value, ['data', 'meta']) || !isRecord(value.data) || !isRecord(value.meta)) return false;
  const data = value.data;
  const meta = value.meta;
  if (!hasOnlyKeys(data, ['exact_groups', 'similar', 'suggestions', 'intent']) ||
      !hasOnlyKeys(meta, ['query', 'current_page', 'per_page', 'last_page', 'total', 'total_exact', 'total_similar'])) return false;
  if (!Array.isArray(data.exact_groups) || !Array.isArray(data.similar) || !Array.isArray(data.suggestions) || !isRecord(data.intent)) return false;
  if (!validSearchQuery(meta.query) || !positiveInteger(meta.current_page) || (meta.current_page as number) > 1000 ||
      !positiveInteger(meta.per_page) || (meta.per_page as number) > 48 || !positiveInteger(meta.last_page) ||
      !nonNegativeInteger(meta.total) || !nonNegativeInteger(meta.total_exact) || !nonNegativeInteger(meta.total_similar)) return false;
  if ((meta.total as number) !== (meta.total_exact as number) + (meta.total_similar as number) ||
      (meta.last_page as number) !== Math.max(1, Math.ceil((meta.total as number) / (meta.per_page as number)))) return false;
  if (expected !== undefined && (meta.query !== expected.query || meta.current_page !== expected.page || meta.per_page !== expected.perPage)) return false;

  const categorySlugs = new Set<string>();
  const ids = new Set<string>();
  let visible = 0;
  let visibleExact = 0;
  let visibleSimilar = 0;
  for (const group of data.exact_groups) {
    if (!isRecord(group) || !hasOnlyKeys(group, ['category', 'items']) || !isCategory(group.category) || !Array.isArray(group.items) || group.items.length === 0) return false;
    const category = group.category as Record<string, unknown>;
    if (categorySlugs.has(category.slug as string)) return false;
    categorySlugs.add(category.slug as string);
    for (const item of group.items) {
      if (!isProduct(item, false) || !recordProductId(item, ids) || !isRecord(item) || !isRecord(item.category) ||
          item.category.slug !== category.slug || item.category.label !== category.label) return false;
      visible += 1;
      visibleExact += 1;
    }
  }
  for (const item of data.similar) {
    if (!isProduct(item, false) || !recordProductId(item, ids)) return false;
    visible += 1;
    visibleSimilar += 1;
  }
  const expectedVisible = (meta.current_page as number) > (meta.last_page as number)
    ? 0
    : Math.min(meta.per_page as number, Math.max(0, (meta.total as number) - ((meta.current_page as number) - 1) * (meta.per_page as number)));
  if (visible !== expectedVisible) return false;
  const offset = ((meta.current_page as number) - 1) * (meta.per_page as number);
  const expectedExact = (meta.current_page as number) > (meta.last_page as number)
    ? 0
    : Math.min(meta.per_page as number, Math.max(0, (meta.total_exact as number) - offset));
  const similarOffset = Math.max(0, offset - (meta.total_exact as number));
  const expectedSimilar = (meta.current_page as number) > (meta.last_page as number)
    ? 0
    : Math.min((meta.per_page as number) - expectedExact, Math.max(0, (meta.total_similar as number) - similarOffset));
  if (visibleExact !== expectedExact || visibleSimilar !== expectedSimilar) return false;

  if (data.suggestions.length > 6) return false;
  const suggestionHrefs = new Set<string>();
  const suggestionLabels = new Set<string>();
  for (const suggestion of data.suggestions) {
    const linkedQuery = isRecord(suggestion) ? searchHrefQuery(suggestion.href) : null;
    if (!isRecord(suggestion) || !hasOnlyKeys(suggestion, ['label', 'href']) || !nonEmpty(suggestion.label) ||
        !validSearchQuery(suggestion.label) || linkedQuery === null || linkedQuery !== suggestion.label ||
        suggestionHrefs.has(suggestion.href as string) || suggestionLabels.has(canonicalLabel(suggestion.label))) return false;
    suggestionHrefs.add(suggestion.href as string);
    suggestionLabels.add(canonicalLabel(suggestion.label));
  }

  const intent = data.intent;
  if (!hasOnlyKeys(intent, ['type', 'preserved_term', 'handoff_href']) ||
      (intent.type !== 'generic' && intent.type !== 'invitation') || !validSearchQuery(intent.preserved_term) ||
      intent.preserved_term !== meta.query) return false;
  return intent.type === 'generic'
    ? intent.handoff_href === null
    : intent.handoff_href !== null && safeInvitationHandoff(intent.handoff_href, intent.preserved_term);
}

export function isSafeCatalogImagePath(value: unknown): value is string {
  if (!nonEmpty(value) || !value.startsWith('/') || value.startsWith('//') || /[\\#?\u0000-\u001f\u007f]/.test(value)) return false;
  let decoded;
  try { decoded = decodeURIComponent(value); } catch { return false; }
  if (decoded.includes('..')) return false;
  const candidate = normalize(join(publicRoot, decoded.slice(1)));
  return candidate.startsWith(`${publicRoot}${sep}`) && existsSync(candidate);
}

function isProduct(value: unknown, detail: boolean): boolean {
  if (!isRecord(value)) return false;
  const common = ['id','slug','name',detail ? 'description' : 'description_excerpt','category','modality','price_minor','currency','availability','delivery_type','production_lead_time_days','is_immediate_delivery','primary_image','taxonomy',detail ? 'compatibility' : 'compatibility_excerpt'];
  if (!hasOnlyKeys(value, common) || !nonEmpty(value.id) || !uuidPattern.test(value.id) || !nonEmpty(value.name) || !nonEmpty(value.slug) || value.slug.length > 180 || !slugPattern.test(value.slug)) return false;
  if (!isCategory(value.category) || !modalities.includes(value.modality as (typeof modalities)[number])) return false;
  if (!nonNegativeInteger(value.price_minor) || value.currency !== 'EUR' || !['available','unavailable','made_to_order'].includes(value.availability as string) || !['physical','digital'].includes(value.delivery_type as string)) return false;
  if (value.production_lead_time_days !== null && !nonNegativeInteger(value.production_lead_time_days)) return false;
  if (typeof value.is_immediate_delivery !== 'boolean' || !Array.isArray(value.taxonomy) || !value.taxonomy.every(isTaxonomy)) return false;
  if (value.primary_image !== null && !(isRecord(value.primary_image) && hasOnlyKeys(value.primary_image, ['url', 'alt_text']) && isSafeCatalogImagePath(value.primary_image.url) && nonEmpty(value.primary_image.alt_text))) return false;
  const text = value[detail ? 'description' : 'description_excerpt'];
  const compatibility = value[detail ? 'compatibility' : 'compatibility_excerpt'];
  if (!nonEmpty(text) || text.length > (detail ? 10000 : 240)) return false;
  return compatibility === null || (typeof compatibility === 'string' && compatibility.length <= (detail ? 5000 : 120));
}

function isTaxonomy(value: unknown): boolean { return isRecord(value) && hasOnlyKeys(value, ['type','key','label']) && (value.type === 'theme' || value.type === 'occasion') && nonEmpty(value.key) && nonEmpty(value.label); }
function isCategory(value: unknown): boolean { return isRecord(value) && hasOnlyKeys(value, ['slug', 'label']) && nonEmpty(value.slug) && value.slug.length <= 160 && slugPattern.test(value.slug) && nonEmpty(value.label); }
function namedKey(value: unknown, key: 'slug' | 'key'): boolean { return isRecord(value) && hasOnlyKeys(value, [key, 'label']) && nonEmpty(value[key]) && nonEmpty(value.label); }
function isSafeFilterMap(value: unknown): boolean { return isRecord(value) && Object.keys(value).every((key) => ['category','occasion','modality'].includes(key)) && Object.values(value).every(nonEmpty); }
function hasOnlyKeys(value: Record<string, unknown>, allowed: readonly string[]): boolean { return Object.keys(value).length === allowed.length && Object.keys(value).every((key) => allowed.includes(key)); }
function isRecord(value: unknown): value is Record<string, unknown> { return !!value && typeof value === 'object' && !Array.isArray(value); }
function nonEmpty(value: unknown): value is string { return typeof value === 'string' && value.trim().length > 0; }
function positiveInteger(value: unknown): boolean { return Number.isSafeInteger(value) && (value as number) >= 1; }
function nonNegativeInteger(value: unknown): boolean { return Number.isSafeInteger(value) && (value as number) >= 0; }

function recordProductId(value: unknown, ids: Set<string>): boolean {
  if (!isRecord(value) || typeof value.id !== 'string' || ids.has(value.id)) return false;
  ids.add(value.id);
  return true;
}

function validSearchQuery(value: unknown): value is string {
  if (typeof value !== 'string' || /[\p{Cc}\p{Cf}]/u.test(value)) return false;
  const canonical = value.normalize('NFKC').trim().replace(/[\p{Z}\s]+/gu, ' ');
  const length = Array.from(canonical).length;
  return canonical === value && length >= 2 && length <= 120 && new TextEncoder().encode(canonical).length <= 512;
}

function searchHrefQuery(value: unknown): string | null {
  const url = safeRelativeUrl(value);
  if (url === null || url.pathname !== '/buscar' || url.hash !== '') return null;
  const entries = [...url.searchParams.entries()];
  return entries.length === 1 && entries[0]?.[0] === 'q' && nonEmpty(entries[0]?.[1]) ? entries[0][1] : null;
}

function safeInvitationHandoff(value: unknown, preservedTerm: string): value is string {
  const url = safeRelativeUrl(value);
  if (url === null || url.pathname !== '/produtos') return false;
  const entries = [...url.searchParams.entries()];
  if (entries.length !== 1 || entries[0]?.[0] !== 'modality' || entries[0]?.[1] !== 'digital_personalized' || !url.hash.startsWith('#busca=')) return false;
  try {
    return decodeURIComponent(url.hash.slice('#busca='.length)) === preservedTerm;
  } catch {
    return false;
  }
}

function safeRelativeUrl(value: unknown): URL | null {
  if (!nonEmpty(value) || !value.startsWith('/') || value.startsWith('//')) return null;
  try {
    const url = new URL(value, 'http://same-origin.invalid');
    if (url.origin !== 'http://same-origin.invalid' || url.username !== '' || url.password !== '') return null;
    const keys = [...url.searchParams.keys()];
    if (new Set(keys).size !== keys.length) return null;
    return url;
  } catch {
    return null;
  }
}

function canonicalLabel(value: string): string {
  return value.normalize('NFKC').trim().replace(/[\p{Z}\s]+/gu, ' ').toLocaleLowerCase('pt-BR');
}
