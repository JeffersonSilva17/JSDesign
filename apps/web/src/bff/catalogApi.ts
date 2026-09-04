import 'server-only';

import { getApiInternalUrl } from '@/bff/apiClient';
import { CatalogApiError } from '@/bff/catalogErrors';
import type { CatalogSearchCriteria } from '@/bff/catalogSearchParams';
import { fetchCatalogJson } from '@/bff/catalogTransport';
import {
  isCatalogFacetsEnvelope,
  isCatalogListingPayload,
  isCatalogProductEnvelope,
  isCatalogSearchPayload,
} from '@/bff/catalogValidation';

export type CatalogModality =
  | 'physical_personalized'
  | 'digital_personalized'
  | 'digital_ready';
export type CatalogFilters = Readonly<{
  category?: string;
  occasion?: string;
  modality?: CatalogModality;
  page: number;
}>;
export type CatalogImage = Readonly<{ url: string; alt_text: string }>;
export type CatalogTaxonomy = Readonly<{ type: 'theme' | 'occasion'; key: string; label: string }>;
type CatalogProductBase = Readonly<{
  id: string;
  slug: string;
  name: string;
  category: Readonly<{ slug: string; label: string }>;
  modality: CatalogModality;
  price_minor: number;
  currency: 'EUR';
  availability: 'available' | 'unavailable' | 'made_to_order';
  delivery_type: 'physical' | 'digital';
  production_lead_time_days: number | null;
  is_immediate_delivery: boolean;
  primary_image: CatalogImage | null;
  taxonomy: readonly CatalogTaxonomy[];
}>;
export type CatalogCard = CatalogProductBase &
  Readonly<{ description_excerpt: string; compatibility_excerpt: string | null }>;
export type CatalogProduct = CatalogProductBase &
  Readonly<{ description: string; compatibility: string | null }>;
export type CatalogFacets = Readonly<{
  categories: readonly Readonly<{ slug: string; label: string }>[];
  occasions: readonly Readonly<{ key: string; label: string }>[];
  modalities: readonly Readonly<{ value: CatalogModality; label: string }>[];
}>;
export type CatalogListing = Readonly<{
  data: readonly CatalogCard[];
  meta: Readonly<{
    current_page: number;
    per_page: number;
    last_page: number;
    total: number;
    applied_filters: Readonly<Partial<Record<'category' | 'occasion' | 'modality', string>>>;
    filter_labels: Readonly<Partial<Record<'category' | 'occasion' | 'modality', string>>>;
  }>;
}>;
export type { CatalogSearchCriteria } from '@/bff/catalogSearchParams';
export { parsePublicSearchParams, publicSearchHref } from '@/bff/catalogSearchParams';
export type CatalogSearchResult = Readonly<{
  data: Readonly<{
    exact_groups: readonly Readonly<{ category: Readonly<{ slug: string; label: string }>; items: readonly CatalogCard[] }>[];
    similar: readonly CatalogCard[];
    suggestions: readonly Readonly<{ label: string; href: string }>[];
    intent: Readonly<{ type: 'generic' | 'invitation'; preserved_term: string; handoff_href: string | null }>;
  }>;
  meta: Readonly<{
    query: string;
    current_page: number;
    per_page: number;
    last_page: number;
    total: number;
    total_exact: number;
    total_similar: number;
  }>;
}>;

export { CatalogApiError } from '@/bff/catalogErrors';

type SearchParams = Readonly<Record<string, string | string[] | undefined>>;
const slugPattern = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
const maxPage = 10000;
const modalities: readonly CatalogModality[] = [
  'physical_personalized',
  'digital_personalized',
  'digital_ready',
];

export function parseCatalogSearchParams(input: SearchParams): CatalogFilters {
  const allowed = new Set(['category', 'occasion', 'modality', 'page']);
  if (Object.keys(input).some((key) => !allowed.has(key))) throw new CatalogApiError('invalid-filter');
  for (const value of Object.values(input)) {
    if (Array.isArray(value) || (typeof value === 'string' && value.trim() === '')) {
      throw new CatalogApiError('invalid-filter');
    }
  }
  const category = optionalSlug(input.category, 160);
  const occasion = optionalOccasion(input.occasion);
  const modality = input.modality;
  if (modality !== undefined && !modalities.includes(modality as CatalogModality)) {
    throw new CatalogApiError('invalid-filter');
  }
  const rawPage = input.page ?? '1';
  if (typeof rawPage !== 'string') throw new CatalogApiError('invalid-filter');
  if (!/^[1-9][0-9]*$/.test(rawPage) || !Number.isSafeInteger(Number(rawPage)) || Number(rawPage) > maxPage) {
    throw new CatalogApiError('invalid-filter');
  }
  return { category, occasion, modality: modality as CatalogModality | undefined, page: Number(rawPage) };
}

export function catalogHref(filters: CatalogFilters, changes: Partial<CatalogFilters> = {}): string {
  const merged = { ...filters, ...changes };
  const url = new URL('/produtos', 'http://same-origin.invalid');
  if (merged.category) url.searchParams.set('category', merged.category);
  if (merged.occasion) url.searchParams.set('occasion', merged.occasion);
  if (merged.modality) url.searchParams.set('modality', merged.modality);
  if (merged.page > 1) url.searchParams.set('page', String(merged.page));
  return `${url.pathname}${url.search}`;
}

export async function fetchCatalogListing(filters: CatalogFilters): Promise<CatalogListing> {
  const url = apiUrl('/api/v1/catalog/products');
  if (filters.category) url.searchParams.set('category', filters.category);
  if (filters.occasion) url.searchParams.set('occasion', filters.occasion);
  if (filters.modality) url.searchParams.set('modality', filters.modality);
  url.searchParams.set('page', String(filters.page));
  url.searchParams.set('per_page', '12');
  const payload = await fetchCatalogJson(url);
  if (!isCatalogListingPayload(payload)) throw new CatalogApiError('invalid-payload');
  return payload as CatalogListing;
}

export async function fetchCatalogFacets(): Promise<CatalogFacets> {
  const payload = await fetchCatalogJson(apiUrl('/api/v1/catalog/facets'));
  if (!isCatalogFacetsEnvelope(payload)) {
    throw new CatalogApiError('invalid-payload');
  }
  return (payload as { data: CatalogFacets }).data;
}

export async function fetchCatalogProduct(slug: string): Promise<CatalogProduct> {
  const payload = await fetchCatalogJson(apiUrl(`/api/v1/catalog/products/${encodeURIComponent(slug)}`), { detail: true });
  if (!isCatalogProductEnvelope(payload)) {
    throw new CatalogApiError('invalid-payload');
  }
  return (payload as { data: CatalogProduct }).data;
}

export async function fetchPublicCatalogSearch(criteria: CatalogSearchCriteria): Promise<CatalogSearchResult> {
  if (criteria.query === null) throw new CatalogApiError('invalid-filter');
  const url = apiUrl('/api/v1/catalog/search');
  url.searchParams.set('q', criteria.query);
  url.searchParams.set('page', String(criteria.page));
  url.searchParams.set('per_page', '12');
  const payload = await fetchCatalogJson(url);
  if (!isCatalogSearchPayload(payload, { query: criteria.query, page: criteria.page, perPage: 12 })) throw new CatalogApiError('invalid-payload');
  return payload as CatalogSearchResult;
}

function apiUrl(path: string): URL {
  return new URL(path, `${getApiInternalUrl()}/`);
}

function optionalSlug(value: string | string[] | undefined, max: number): string | undefined {
  if (value === undefined) return undefined;
  if (typeof value !== 'string' || value.length > max || !slugPattern.test(value)) {
    throw new CatalogApiError('invalid-filter');
  }
  return value;
}

function optionalOccasion(value: string | string[] | undefined): string | undefined {
  if (value === undefined) return undefined;
  if (typeof value !== 'string' || value.length > 180 || value.trim() === '') throw new CatalogApiError('invalid-filter');
  const canonical = value.normalize('NFC').trim().replace(/\s+/gu, ' ').toLocaleLowerCase('pt-BR')
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '').normalize('NFC');
  if (canonical !== value) throw new CatalogApiError('invalid-filter');
  return value;
}
