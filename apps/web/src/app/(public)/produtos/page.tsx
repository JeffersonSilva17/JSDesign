import type { Metadata } from 'next';

import { CatalogApiError, fetchCatalogFacets, fetchCatalogListing, parseCatalogSearchParams } from '@/bff/catalogApi';
import type { CatalogFacets } from '@/bff/catalogApi';
import { CatalogFiltersView } from '@/features/catalog/CatalogFilters';
import { CatalogListing } from '@/features/catalog/CatalogListing';
import { CatalogState } from '@/features/catalog/CatalogState';
import { catalogContent } from '@/features/public-store/publicLayoutContent';

type Props = Readonly<{ searchParams: Promise<Record<string, string | string[] | undefined>> }>;

export async function generateMetadata({ searchParams }: Props): Promise<Metadata> {
  const params = await searchParams;
  return { ...catalogContent.metadata.products, robots: Object.keys(params).length ? { index: false, follow: true } : undefined };
}

export default async function ProdutosPage({ searchParams }: Props) {
  let filters;
  try { filters = parseCatalogSearchParams(await searchParams); } catch { filters = null; }
  if (filters === null) return <CatalogState kind="invalid" />;
  const emptyFacets: CatalogFacets = { categories: [], occasions: [], modalities: [] };
  const [listingResult, facetsResult] = await Promise.allSettled([fetchCatalogListing(filters), fetchCatalogFacets()]);
  if (listingResult.status === 'rejected') {
    const kind = listingResult.reason instanceof CatalogApiError && listingResult.reason.kind === 'invalid-filter' ? 'invalid' : 'unavailable';
    return <CatalogState kind={kind} />;
  }
  const listing = listingResult.value;
  const facets = facetsResult.status === 'fulfilled' ? facetsResult.value : emptyFacets;
  return <div className="catalog-page">
    <header className="catalog-page__header"><p className="eyebrow">{catalogContent.listing.eyebrow}</p><h1>{catalogContent.listing.title}</h1><p>{catalogContent.listing.description}</p></header>
    <CatalogFiltersView facets={facets} filters={filters} />
    <CatalogListing listing={listing} filters={filters} />
  </div>;
}
