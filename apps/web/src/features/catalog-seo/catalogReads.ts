import 'server-only';

import { cache } from 'react';

import { fetchCatalogFacets, fetchCatalogListing, fetchCatalogProduct } from '@/bff/catalogApi';
import type { CatalogFilters, CatalogModality } from '@/bff/catalogApi';

export const readProduct = cache(fetchCatalogProduct);
export const readFacets = cache(fetchCatalogFacets);
const listing = cache((category: string, occasion: string, modality: CatalogModality | '', page: number) =>
  fetchCatalogListing({ category: category || undefined, occasion: occasion || undefined, modality: modality || undefined, page }));

export function readListing(filters: CatalogFilters) {
  return listing(filters.category ?? '', filters.occasion ?? '', filters.modality ?? '', filters.page);
}
