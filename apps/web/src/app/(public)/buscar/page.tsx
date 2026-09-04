import type { Metadata } from 'next';
import Link from 'next/link';
import type { ReactNode } from 'react';

import { CatalogApiError, fetchCatalogFacets, fetchPublicCatalogSearch, parsePublicSearchParams } from '@/bff/catalogApi';
import type { CatalogFacets } from '@/bff/catalogApi';
import { SearchForm } from '@/features/catalog-search/SearchForm';
import { SearchResults } from '@/features/catalog-search/SearchResults';
import { SearchState } from '@/features/catalog-search/SearchState';
import { searchContent } from '@/features/public-store/publicLayoutContent';

type Props = Readonly<{ searchParams: Promise<Record<string, string | string[] | undefined>> }>;

export async function generateMetadata({ searchParams }: Props): Promise<Metadata> {
  const parameterized = Object.keys(await searchParams).length > 0;
  return { ...searchContent.metadata, robots: parameterized ? { index: false, follow: true } : undefined };
}

export default async function BuscarPage({ searchParams }: Props) {
  const rawParams = await searchParams;
  let criteria;
  try {
    criteria = parsePublicSearchParams(rawParams);
  } catch {
    return <SearchPageShell><SearchForm invalid /><SearchState kind="invalid" /></SearchPageShell>;
  }

  if (criteria.query === null) {
    const emptyFacets: CatalogFacets = { categories: [], occasions: [], modalities: [] };
    const facets = await fetchCatalogFacets().catch(() => emptyFacets);
    return (
      <SearchPageShell>
        <SearchForm />
        <section className="catalog-search-initial" aria-labelledby="catalog-search-initial-title">
          <h2 id="catalog-search-initial-title">{searchContent.initial.title}</h2>
          <p>{searchContent.initial.description}</p>
          {facets.categories.length > 0 && <><h3>{searchContent.initial.categories}</h3><div className="catalog-search-chips">{facets.categories.map((category) => <Link key={category.slug} href={`/produtos?category=${encodeURIComponent(category.slug)}`}>{category.label}</Link>)}</div></>}
          {facets.occasions.length > 0 && <><h3>{searchContent.initial.editorialSuggestions}</h3><div className="catalog-search-chips">{facets.occasions.map((occasion) => <Link key={occasion.key} href={`/buscar?q=${encodeURIComponent(occasion.label)}`}>{occasion.label}</Link>)}</div></>}
        </section>
      </SearchPageShell>
    );
  }

  let result: Awaited<ReturnType<typeof fetchPublicCatalogSearch>> | null = null;
  let invalid = false;
  try {
    result = await fetchPublicCatalogSearch(criteria);
  } catch (error) {
    invalid = error instanceof CatalogApiError && error.kind === 'invalid-filter';
  }
  if (result === null) {
    return <SearchPageShell><SearchForm query={criteria.query} invalid={invalid} /><SearchState kind={invalid ? 'invalid' : 'unavailable'} actionHref={invalid ? '/buscar' : `/buscar?q=${encodeURIComponent(criteria.query)}`} /></SearchPageShell>;
  }
  return <SearchPageShell><SearchForm query={criteria.query} /><SearchResults result={result} criteria={criteria} /></SearchPageShell>;
}

function SearchPageShell({ children }: Readonly<{ children: ReactNode }>) {
  return <div className="catalog-page catalog-search-page"><header className="catalog-page__header"><p className="eyebrow">{searchContent.eyebrow}</p><h1>{searchContent.title}</h1><p>{searchContent.description}</p></header>{children}</div>;
}
