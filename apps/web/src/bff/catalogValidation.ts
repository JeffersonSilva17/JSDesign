import { existsSync } from 'node:fs';
import { join, normalize, sep } from 'node:path';

const modalities = ['physical_personalized', 'digital_personalized', 'digital_ready'] as const;
const slugPattern = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
const uuidPattern = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
const publicRoot = normalize(join(process.cwd(), 'public'));

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
