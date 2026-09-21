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

export { parseCatalogSearchParams, catalogHref } from './catalogParams';

export async function fetchCatalogListing(filters: CatalogFilters): Promise<CatalogListing> {
  const url = apiUrl('/api/v1/catalog/products');
  if (filters.category) url.searchParams.set('category', filters.category);
  if (filters.occasion) url.searchParams.set('occasion', filters.occasion);
  if (filters.modality) url.searchParams.set('modality', filters.modality);
  url.searchParams.set('page', String(filters.page));
  url.searchParams.set('per_page', '12');
  const payload = await fetchCatalogJson(url);
  if (!isCatalogListingPayload(payload, filters)) throw new CatalogApiError('invalid-payload');
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
  if (slug.length > 180 || !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug)) throw new CatalogApiError('not-found');
  const payload = await fetchCatalogJson(apiUrl(`/api/v1/catalog/products/${encodeURIComponent(slug)}`), { detail: true });
  if (!isCatalogProductEnvelope(payload, slug)) {
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
