import type { Metadata } from 'next';

import { CatalogApiError, parseCatalogSearchParams } from '@/bff/catalogApi';
import type { CatalogFacets } from '@/bff/catalogApi';
import { CatalogFiltersView } from '@/features/catalog/CatalogFilters';
import { CatalogListing } from '@/features/catalog/CatalogListing';
import { CatalogState } from '@/features/catalog/CatalogState';
import { catalogContent } from '@/features/public-store/publicLayoutContent';

import { readListing, readFacets } from '@/features/catalog-seo/catalogReads';
import { catalogMetadata, listingPresentation } from '@/features/catalog-seo/catalogMetadata';
import { collectionStructuredData } from '@/features/catalog-seo/catalogStructuredData';
import { StructuredData } from '@/features/catalog-seo/StructuredData';
import { seoConfig } from '@/features/catalog-seo/siteUrl';

type Props = Readonly<{ searchParams: Promise<Record<string, string | string[] | undefined>> }>;

export async function generateMetadata({ searchParams }: Props): Promise<Metadata> {
  try {
    const filters = parseCatalogSearchParams(await searchParams);
    const listing = await readListing(filters);
    return listingPresentation(filters, listing).metadata;
  } catch { return catalogMetadata(catalogContent.metadata.products.title, catalogContent.metadata.products.description); }
}

export default async function ProdutosPage({ searchParams }: Props) {
  let filters;
  try { filters = parseCatalogSearchParams(await searchParams); } catch { filters = null; }
  if (filters === null) return <CatalogState kind="invalid" />;
  const emptyFacets: CatalogFacets = { categories: [], occasions: [], modalities: [] };
  const [listingResult, facetsResult] = await Promise.allSettled([readListing(filters), readFacets()]);
  if (listingResult.status === 'rejected') {
    const kind = listingResult.reason instanceof CatalogApiError && listingResult.reason.kind === 'invalid-filter' ? 'invalid' : 'unavailable';
    return <CatalogState kind={kind} />;
  }
  const listing = listingResult.value;
  const facets = facetsResult.status === 'fulfilled' ? facetsResult.value : emptyFacets;
  const presentation = listingPresentation(filters, listing);
  return <div className="catalog-page">
    {presentation.policy.index && presentation.policy.canonical && listing.data.length > 0 && <StructuredData value={collectionStructuredData(listing, presentation.policy.canonical, seoConfig().origin)} />}
    <header className="catalog-page__header"><p className="eyebrow">{catalogContent.listing.eyebrow}</p><h1>{presentation.title}</h1><p>{presentation.description}</p></header>
    <CatalogFiltersView facets={facets} filters={filters} />
    <CatalogListing listing={listing} filters={filters} />
  </div>;
}
